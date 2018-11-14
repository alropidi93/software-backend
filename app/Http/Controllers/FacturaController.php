<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\FacturaResource;
use App\Http\Resources\FacturasResource;
use App\Http\Resources\PersonaJuridicaResource;
use App\Http\Resources\LineaDeVentaResource;
use App\Http\Resources\LineasDeVentaResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Models\Factura;
use App\Models\LineaDeVenta;
use App\Repositories\FacturaRepository;
use App\Repositories\ComprobantePagoRepository;
use App\Repositories\LineaDeVentaRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Collection;

//CHECKING AGAINST USUARIO CONTROLLER
class FacturaController extends Controller
{
    protected $facturaRepository;
    protected $comprobantePagoRepository; //no tiene equi
    // protected $lineasDeVenta;

    public function __construct(FacturaRepository $facturaRepository, ComprobantePagoRepository $comprobantePagoRepository, LineaDeVentaRepository $lineaDeVentaRepository){
        FacturaResource::withoutWrapping();
        $this->facturaRepository = $facturaRepository;
        $this->comprobantePagoRepository = $comprobantePagoRepository; //no tiene equi
        $this->lineaDeVentaRepository = $lineaDeVentaRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        try{
            $facturas = $this->facturaRepository->listarFacturas();
            foreach($facturas as $key => $factura){
                $this->facturaRepository->loadPersonaJuridicaRelationship($factura);
                $comprobantePago = $this->facturaRepository->obtenerComprobantePago();            
                $this->comprobantePagoRepository->loadLineasDeVentaRelationship($comprobantePago);
            }
            $facturasResource =  new FacturasResource($facturas); 
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de facturas');  
            $responseResourse->body($facturasResource);
            return $responseResourse;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $facturaData){
        try{
            $facturaDataArray = $facturaData->all();
            $validator = \Validator::make($facturaData->all(), 
                            ['subtotal' => 'required'],
                            ['igv' => 'required',
                            'lineasDeVenta'=>  'required']);
                            

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            //$idCliente = $facturaDataArray['idCliente'];
            $idCliente = array_key_exists('idCliente', $facturaDataArray)? $facturaDataArray['idCliente']:null;
            $personaJuridica = $this->facturaRepository->getUsuarioById($idCliente);
           
            if($personaJuridica){
                $this->facturaRepository->setPersonaJuridicaModel($personaJuridica);
            }else{
                // $this->boletaRepository->setPersonaNaturalModel();
            }

            DB::beginTransaction();
            $this->facturaRepository->guarda($facturaDataArray); //aqui(im not sure) se envian las lineas para guardarlas en el comprobante de pago
            // $this->comprobantePagoRepository->guarda($boletaDataArray);
            $factura = $this->facturaRepository->obtenerModelo();
           
            /*Alvaro's change*/
            $comprobantePago = $this->facturaRepository->obtenerComprobantePago();
            
            /*Alvaro's change END*/
            $list = $facturaData['lineasDeVenta'];
            $list_collection = new Collection($list);
            /*Alvaro's change*/
            $this->comprobantePagoRepository->setModel($comprobantePago); 
            /*Alvaro's change END*/   
            foreach ($list_collection as $key => $elem) {
                $this->comprobantePagoRepository->setLineaDeVentaData($elem);
                $this->comprobantePagoRepository->attachLineaDeVentaWithOwnModels();
                

                // $this->boletaRepository->setLineaDeVentaData($elem); //it will call the same method but on comprobantePagoRepository
                // $this->boletaRepository->attachLineaDeVentaWithOwnModels(); //it will also call the same method but on comprobantePagoRepository
            }
            DB::commit();
            
            $this->facturaRepository->loadPersonaJuridicaRelationship();
            $this->comprobantePagoRepository->loadLineasDeVentaRelationship();
            // $this->boletaRepository->loadLineasDeVentaRelationship();
            $facturaCreated = $this->facturaRepository->obtenerModelo();
            $facturaResource =  new FacturaResource($facturaCreated);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Factura creada exitosamente');       
            $responseResourse->body($facturaResource);       
            return $responseResourse;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        try{
            $factura = $this->facturaRepository->obtenerFacturaPorId($id);

            if (!$factura){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Factura no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }

            $this->facturaRepository->setModelFactura($factura);
            $this->facturaRepository->loadPersonaJuridicaRelationship(); //para su cliente
            $this->comprobantePagoRepository->loadLineasDeVentaRelationship();
            // $this->comprobantePagoRepository->loadLineasDeVentaRelationship();
            $facturaResource =  new FacturaResource($factura);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar factura');  
            $responseResourse->body($facturaResource);
            return $responseResourse;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        try{
            DB::beginTransaction();
            $factura = $this->facturaRepository->obtenerFacturaPorId($id);
            if (!$factura){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Factura no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->facturaRepository->setModelFactura($factura);
            $this->facturaRepository->softDelete();

            $responseResourse = new ResponseResource($factura);
            $responseResourse->title('Factura eliminada');  
            $responseResourse->body(['id' => $id]);
            DB::commit();
            return $responseResourse;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }
}
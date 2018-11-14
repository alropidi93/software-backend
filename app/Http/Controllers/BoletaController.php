<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\BoletaResource;
use App\Http\Resources\BoletasResource;
use App\Http\Resources\PersonaNaturalResource;
use App\Http\Resources\LineaDeVentaResource;
use App\Http\Resources\LineasDeVentaResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Models\Boleta;
use App\Models\LineaDeVenta;
use App\Repositories\BoletaRepository;
use App\Repositories\ComprobantePagoRepository;
use App\Repositories\LineaDeVentaRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Collection;

//CHECKING AGAINST USUARIO CONTROLLER
class BoletaController extends Controller
{
    protected $boletaRepository;
    protected $comprobantePagoRepository; //no tiene equi
    // protected $lineasDeVenta;

    public function __construct(BoletaRepository $boletaRepository, ComprobantePagoRepository $comprobantePagoRepository, LineaDeVentaRepository $lineaDeVentaRepository){
        BoletaResource::withoutWrapping();
        $this->boletaRepository = $boletaRepository;
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
            $boletas = $this->boletaRepository->listarBoletas();
            foreach($boletas as $key => $boleta){
                $this->boletaRepository->loadPersonaNaturalRelationship($boleta);
                $comprobantePago = $this->boletaRepository->obtenerComprobantePago();            
                $this->comprobantePagoRepository->loadLineasDeVentaRelationship($comprobantePago);
            }
            $boletasResource =  new BoletasResource($boletas); 
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de boletas');  
            $responseResourse->body($boletasResource);
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
    public function store(Request $boletaData){
        try{
            $boletaDataArray = $boletaData->all();
            $validator = \Validator::make($boletaData->all(), 
                            ['subtotal' => 'required'],
                            ['igv' => 'required',
                            'lineasDeVenta'=>  'required']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            // $idCliente = $boletaDataArray['idCliente'];
            $idCliente = array_key_exists('idCliente', $boletaDataArray)? $boletaDataArray['idCliente']:null;
            $personaNatural = $this->boletaRepository->getUsuarioById($idCliente);
            if($personaNatural){
                $this->boletaRepository->setPersonaNaturalModel($personaNatural);
            }else{
                // $this->boletaRepository->setPersonaNaturalModel();
            }

            DB::beginTransaction();
            $this->boletaRepository->guarda($boletaDataArray); //aqui(im not sure) se envian las lineas para guardarlas en el comprobante de pago
            // $this->comprobantePagoRepository->guarda($boletaDataArray);
            $boleta = $this->boletaRepository->obtenerModelo();
            /*Alvaro's change*/
            $comprobantePago = $this->boletaRepository->obtenerComprobantePago();
            /*Alvaro's change END*/
            $list = $boletaData['lineasDeVenta'];
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
            
            $this->boletaRepository->loadPersonaNaturalRelationship();
            $this->comprobantePagoRepository->loadLineasDeVentaRelationship();
            // $this->boletaRepository->loadLineasDeVentaRelationship();
            $boletaCreated = $this->boletaRepository->obtenerModelo();
            $boletaResource =  new BoletaResource($boletaCreated);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Boleta creada exitosamente');       
            $responseResourse->body($boletaResource);       
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
            $boleta = $this->boletaRepository->obtenerBoletaPorId($id);

            if (!$boleta){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Boleta no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }

            $this->boletaRepository->setModelBoleta($boleta);
            $this->boletaRepository->loadPersonaNaturalRelationship(); //para su cliente
            $this->comprobantePagoRepository->loadLineasDeVentaRelationship();
            // $this->comprobantePagoRepository->loadLineasDeVentaRelationship();
            $boletaResource =  new BoletaResource($boleta);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar boleta');  
            $responseResourse->body($boletaResource);
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
            $boleta = $this->boletaRepository->obtenerBoletaPorId($id);
            if (!$boleta){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Boleta no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->boletaRepository->setModelBoleta($boleta);
            $this->boletaRepository->softDelete();

            $responseResourse = new ResponseResource($boleta);
            $responseResourse->title('Boleta eliminada');  
            $responseResourse->body(['id' => $id]);
            DB::commit();
            return $responseResourse;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }
}

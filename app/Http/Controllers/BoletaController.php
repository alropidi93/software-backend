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
use App\Models\PersonaNatural;
use App\Repositories\BoletaRepository;
use App\Repositories\ComprobantePagoRepository;
use App\Repositories\LineaDeVentaRepository;
use App\Repositories\PersonaNaturalRepository;
use App\Repositories\ProductoRepository;
use App\Repositories\TiendaRepository;
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
    protected $tiendaRepository;
    protected $productoRepository;

    public function __construct(BoletaRepository $boletaRepository, ComprobantePagoRepository $comprobantePagoRepository, LineaDeVentaRepository $lineaDeVentaRepository,TiendaRepository $tiendaRepository,ProductoRepository $productoRepository){
        BoletaResource::withoutWrapping();
        $this->boletaRepository = $boletaRepository;
        $this->comprobantePagoRepository = $comprobantePagoRepository; //no tiene equi
        $this->lineaDeVentaRepository = $lineaDeVentaRepository;
        $this->tiendaRepository = $tiendaRepository;
        $this->productoRepository = $productoRepository;
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
                // $comprobantePago = $this->boletaRepository->obtenerComprobantePago();
                $this->comprobantePagoRepository->loadCajeroRelationship($boleta->comprobantePago);
                $this->comprobantePagoRepository->loadLineasDeVentaRelationship($boleta->comprobantePago);
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

            //idCliente no es obligatorio pero hay que verificar si existe en el request enviado
            $idCliente = array_key_exists('idCliente', $boletaDataArray)? $boletaDataArray['idCliente']:null;
            $personaNatural = $this->boletaRepository->getUsuarioById($idCliente);
            if($personaNatural){
                $this->boletaRepository->setPersonaNaturalModel($personaNatural);
            }else{
                // $this->boletaRepository->setPersonaNaturalModel();
            }

            DB::beginTransaction();
            $this->boletaRepository->guarda($boletaDataArray);
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
            }
            //Modificar stock
            $esParaRecoger=array_key_exists('entrega', $boletaDataArray)? $boletaDataArray['entrega']:false;
            $idTienda= $boletaData['idTienda'];           
            $idAlmacen=$this->tiendaRepository->obtenerIdAlmacenConIdTienda($idTienda);
            if(!$esParaRecoger){ //solo se tiene que restar del Almacen Principal
               foreach ($list_collection as $key => $elem) {  
                    $idProducto=$elem['idProducto'];
                    $cantidad= $elem['cantidad'];
                    $idTipoStock= 1;
                    $producto = $this->productoRepository->obtenerPorId($idProducto);
                    $this->productoRepository->setModel($producto);
                    $stockAnterior= $this->productoRepository->consultarStock($idProducto,$idAlmacen,$idTipoStock);
                    $nuevoStock=$stockAnterior - $cantidad;
                    if($nuevoStock < 0){
                        $errorResource =  new ErrorResource (null);
                        $errorResource->title("Error de stock");
                        $errorResource->message("No hay stock suficiente para concretar la venta");
                        return $errorResource;
                    }
                    $this->productoRepository->updateStock( $idTipoStock, $idAlmacen, $nuevoStock);                   
                }
            }
            elseif ($esParaRecoger){ // se tiene que restar del Almacen Principal y añadir al Almacen de Recojos
                foreach ($list_collection as $key => $elem) {
                    $idProducto=$productoData['id'];
                    $cantidad= $elem['cantidad'];
                    $idTipoStock= 1;
                    $producto = $this->productoRepository->obtenerPorId($idProducto);
                    $this->productoRepository->setModel($producto);
                    $stockAnteriorPrincipal= $this->productoRepository->consultarStock($idProducto,$idAlmacen,$idTipoStock);
                    $nuevoStockPrincipal=$stockAnteriorPrincipal - $cantidad;
                    if($nuevoStockPrincipal < 0){
                        $errorResource =  new ErrorResource (null);
                        $errorResource->title("Error de stock");
                        $errorResource->message("No hay stock suficiente para concretar la venta");
                        return $errorResource;
                    }
                    $this->productoRepository->updateStock( $idTipoStock, $idAlmacen, $nuevoStockPrincipal); 
                    //Almacen de Recojo
                    $idTipoStock= 3;
                    $stockAnteriorRecojo= $this->productoRepository->consultarStock($idProducto,$idAlmacen,$idTipoStock);
                    $nuevoStockRecojo=$stockAnteriorRecojo + $cantidad;                    
                    $this->productoRepository->updateStock( $idTipoStock, $idAlmacen, $nuevoStockRecojo); 
                }
            }
            DB::commit();
            
            $this->boletaRepository->loadPersonaNaturalRelationship();
            $this->comprobantePagoRepository->loadLineasDeVentaRelationship();
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
            $this->boletaRepository->loadPersonaNaturalRelationship();

            //se deben mostrar las lineas de venta del comprobante de pago que le pertenece a la boleta
            $this->comprobantePagoRepository->loadCajeroRelationship($boleta->comprobantePago);
            $this->comprobantePagoRepository->loadLineasDeVentaRelationship($boleta->comprobantePago);
            
            $boletaResource =  new BoletaResource($boleta);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar boleta');  
            $responseResourse->body($boletaResource);
            return $responseResourse;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }

    public function asignarCliente($idComprobantePago, Request $data){
        try{
            DB::beginTransaction();
            $boleta = $this->boletaRepository->obtenerBoletaPorId($idComprobantePago);
            
            if (!$boleta){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Boleta no encontrada');
                $notFoundResource->notFound(['id' => $idComprobantePago]);
                return $notFoundResource->response()->setStatusCode(404);
            }

            $personaNaturalRepository =  new PersonaNaturalRepository(new PersonaNatural);
            $idCliente = $data['idCliente'];
            $personaNatural =  $personaNaturalRepository->obtenerPorId($idCliente);
            
            if (!$personaNatural){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Cliente no encontrado');
                $notFoundResource->notFound(['idCliente' => $idCliente]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            
            $this->boletaRepository->setModel($boleta);
            $this->boletaRepository->setPersonaNaturalModel($personaNatural);
            $this->boletaRepository->loadPersonaNaturalRelationship();    
            $this->boletaRepository->attachPersonaNatural();
            DB::commit();
            
            $boleta =  $this->boletaRepository->obtenerModelo();
          
            $boletaResource =  new BoletaResource($boleta);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Cliente asignado satisfactoriamente');  
            $responseResourse->body($boletaResource);
            return $responseResourse;
        }catch(\Exception $e){
            DB::rollback();
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


/*
                   ▄              ▄
                  ▌▒█           ▄▀▒▌
                  ▌▒▒█        ▄▀▒▒▒▐
                 ▐▄▀▒▒▀▀▀▀▄▄▄▀▒▒▒▒▒▐
               ▄▄▀▒░▒▒▒▒▒▒▒▒▒█▒▒▄█▒▐
             ▄▀▒▒▒░░░▒▒▒░░░▒▒▒▀██▀▒▌
            ▐▒▒▒▄▄▒▒▒▒░░░▒▒▒▒▒▒▒▀▄▒▒▌
            ▌░░▌█▀▒▒▒▒▒▄▀█▄▒▒▒▒▒▒▒█▒▐
           ▐░░░▒▒▒▒▒▒▒▒▌██▀▒▒░░░▒▒▒▀▄▌
           ▌░▒▄██▄▒▒▒▒▒▒▒▒▒░░░░░░▒▒▒▒▌
          ▌▒▀▐▄█▄█▌▄░▀▒▒░░░░░░░░░░▒▒▒▐
          ▐▒▒▐▀▐▀▒░▄▄▒▄▒▒▒▒▒▒░▒░▒░▒▒▒▒▌
          ▐▒▒▒▀▀▄▄▒▒▒▄▒▒▒▒▒▒▒▒░▒░▒░▒▒▐
           ▌▒▒▒▒▒▒▀▀▀▒▒▒▒▒▒░▒░▒░▒░▒▒▒▌
           ▐▒▒▒▒▒▒▒▒▒▒▒▒▒▒░▒░▒░▒▒▄▒▒▐
            ▀▄▒▒▒▒▒▒▒▒▒▒▒░▒░▒░▒▄▒▒▒▒▌
              ▀▄▒▒▒▒▒▒▒▒▒▒▄▄▄▀▒▒▒▒▄▀
                ▀▄▄▄▄▄▄▀▀▀▒▒▒▒▒▄▄▀
                   ▒▒▒▒▒▒▒▒▒▒▀▀
 */
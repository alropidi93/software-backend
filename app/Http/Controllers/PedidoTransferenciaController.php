<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PedidoTransferencia;
use App\Models\Usuario;
use App\Repositories\PedidoTransferenciaRepository;
use App\Repositories\LineaPedidoTransferenciaRepository;
use App\Repositories\SolicitudCompraRepository;
use App\Repositories\LineaSolicitudCompraRepository;
use App\Http\Resources\LineaSolicitudCompraResource;
use App\Http\Resources\LineasSolicitudCompraResource;

use App\Repositories\UsuarioRepository;
use App\Services\AlmacenService;
use App\Services\PedidoTransferenciaService;
use App\Http\Controllers\Controller;
use App\Http\Resources\SolicitudCompraResource;
use App\Http\Resources\SolicitudesCompraResource;
use App\Http\Resources\PedidoTransferenciaResource;
use App\Http\Resources\PedidosTransferenciaResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Collection;

class PedidoTransferenciaController extends Controller {

    protected $pedidoTransferenciaRepository;
    protected $solicitudCompraRepository;
    protected $lineaSolicitudCompraRepository;
    protected $lineasPedidoTransferencia;

    public function __construct(PedidoTransferenciaRepository $pedidoTransferenciaRepository, SolicitudCompraRepository $solicitudCompraRepository,LineaSolicitudCompraRepository $lineaSolicitudCompraRepository, LineaPedidoTransferenciaRepository $lineaPedidoTransferenciaRepository)
    {
        PedidoTransferenciaResource::withoutWrapping();
        
        SolicitudCompraResource::withoutWrapping();
        LineaSolicitudCompraResource::withoutWrapping();
        
        $this->pedidoTransferenciaRepository = $pedidoTransferenciaRepository;
        $this->solicitudCompraRepository = $solicitudCompraRepository;
        $this->lineaSolicitudCompraRepository = $lineaSolicitudCompraRepository;
        $this->lineaPedidoTransferenciaRepository = $lineaPedidoTransferenciaRepository;
    }

    public function index() 
    {
        try{
            $responseResource = new ResponseResource(null);
            $filter = strtolower(Input::get('estado'));
            
            if ($filter){
                
                switch ($filter) {
                    case 'en_transito':
                                     
                        $pedidosTransferencia = $this->pedidoTransferenciaRepository->buscarPorFiltroPorTransferencia('estado', 'en transito');
                        foreach ($pedidosTransferencia as $key => $pt) {
                            $this->pedidoTransferenciaRepository->loadTransferenciaRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship($pt);
                            
                        }
                        $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia);
                        $responseResource->title('Lista de pedidos de transferencia filtrados por estado');       
                        $responseResource->body($pedidosTransferenciaResource);
                        break;
    
                    case 'aceptado':
                        $pedidosTransferencia = $this->pedidoTransferenciaRepository->buscarPorFiltroPorTransferencia('estado', 'aceptado');
                        foreach ($pedidosTransferencia as $key => $pt) {
                            $this->pedidoTransferenciaRepository->loadTransferenciaRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship($pt);
                            
                        }
                        $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia);
                        $responseResource->title('Lista de pedidos de transferencia filtrados por estado');       
                        $responseResource->body($pedidosTransferenciaResource);
                        break;
                    
    
                    case 'realizado':
                        $pedidosTransferencia = $this->pedidoTransferenciaRepository->buscarPorFiltroPorTransferencia('estado', 'realizado');
                        foreach ($pedidosTransferencia as $key => $pt) {
                            $this->pedidoTransferenciaRepository->loadTransferenciaRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship($pt);
                            
                        }
                        $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia);
                        $responseResource->title('Lista de pedidos de transferencia filtrados por estado');       
                        $responseResource->body($pedidosTransferenciaResource);
                        break;
                    case 'denegado':
                        $pedidosTransferencia = $this->pedidoTransferenciaRepository->buscarPorFiltroPorTransferencia('estado', 'denegado');
                        foreach ($pedidosTransferencia as $key => $pt) {
                            $this->pedidoTransferenciaRepository->loadTransferenciaRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship($pt);
                            
                        }
                        $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia);
                        $responseResource->title('Lista de pedidos de transferencia filtrados por estado');       
                        $responseResource->body($pedidosTransferenciaResource);
                        break;
                    case 'cancelado':
                        $pedidosTransferencia = $this->pedidoTransferenciaRepository->buscarPorFiltroPorTransferencia('estado', 'cancelado');
                        foreach ($pedidosTransferencia as $key => $pt) {
                            $this->pedidoTransferenciaRepository->loadTransferenciaRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship($pt);
                            
                        }
                        $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia);
                        $responseResource->title('Lista de pedidos de transferencia filtrados por estado');       
                        $responseResource->body($pedidosTransferenciaResource);
                        break;
    
                    default:
                        $errorResource = new ErrorResource(null);
                        $errorResource->title('Error de búsqueda');
                        $errorResource->message('Valor de filtro inválido');
                        return $errorResource->response()->setStatusCode(400);
                }
                return $responseResource;
            
            }

            $pedidosTransferencia = $this->pedidoTransferenciaRepository->obtenerTodos();
           
            foreach ($pedidosTransferencia as $key => $pt) {
                $this->pedidoTransferenciaRepository->loadTransferenciaRelationship($pt);
                $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pt);
                $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pt);
                $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship($pt);
                
            }
            $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia);  
            
            
            $responseResource->title('Lista de pedidos de transferencia');  
            $responseResource->body($pedidosTransferenciaResource);
            return $responseResource;
        }
        catch(\Exception $e){
         
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

       
    }
  
    public function show($id) 
    {
        try{
            $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerPorId($id);
            
            if (!$pedidoTransferencia){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Pedido de transferencia no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->pedidoTransferenciaRepository->setModel($pedidoTransferencia);
            $this->pedidoTransferenciaRepository->loadAlmacenORelationship();
            $this->pedidoTransferenciaRepository->loadAlmacenDRelationship();
            $this->pedidoTransferenciaRepository->loadUsuarioRelationship();
           
            $pedidoTransferenciaResource =  new PedidoTransferenciaResource($pedidoTransferencia);  
            $responseResource = new ResponseResource(null);
            $responseResource->title('Mostrar pedido de transferencia');  
            $responseResource->body($pedidoTransferenciaResource);
            return $responseResource;
        }
        catch(\Exception $e){
            
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }

    public function store(Request $data) 
    {
        
        try{
            $dataArray=$data->all();
            $validator = \Validator::make($dataArray, 
                            [ 
                            'idAlmacen' => 'required',
                            'lineasPedidoTransferencia'=>  'required'
                            ]);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            

            $almacen = $this->pedidoTransferenciaRepository->getAlmacenById($data['idAlmacen']);
     
            if (!$almacen){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Almacen no encontrado');
                $notFoundResource->notFound(['idAlmacen'=>$data['idAlmacen']]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            if($almacen->esCentral()){
                $errorResource = new ErrorResource(null);
                $errorResource->title('Almacen prohibido');
                $errorResource->message('No se puede solicitar un pedido de transferencia para este almacen por ser el central, al menos desde este servicio');
                return $errorResource->response()->setStatusCode(400);
            }
            $dataArray['idAlmacenO']=$almacen->id;
            $almacenService =  new AlmacenService;
            $almacenCercano= $almacenService->obtenerAlmacenCercano($almacen,1);
            $dataArray['idAlmacenD']=$almacenCercano->id;
            $dataArray['fase']=1;

            if(array_key_exists('idUsuario',$dataArray) && $dataArray['idUsuario']!=null){
                $usuario = $this->pedidoTransferenciaRepository->getUsuarioById($data['idUsuario']);
            
                if (!$usuario){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('Usuario no encontrado');
                    $notFoundResource->notFound(['idUsuario'=>$data['idUsuario']]);
                    return $notFoundResource->response()->setStatusCode(404);;
                }
                $dataArray['idUsuario'] = $usuario->id;
            }
                              
            DB::beginTransaction();
            
            $this->pedidoTransferenciaRepository->guarda($dataArray);
            $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
            $list = $data['lineasPedidoTransferencia'];
            $list_collection = new Collection($list);
                       
            foreach ($list_collection as $key => $elem) {
                
                $this->pedidoTransferenciaRepository->setLineaPedidoTransferenciaData($elem);
                $this->pedidoTransferenciaRepository->attachLineaPedidoTransferenciaWithOwnModels();
                            
            }
            DB::commit();
            
                
            //return $this->pedidoTransferenciaRepository->obtenerModelo();
            $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship();
            
            $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship();
            $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship();
            //return $this->campaignRepository->getModel();
            $pedidoTransferenciaCreado = $this->pedidoTransferenciaRepository->obtenerModelo();
            
            
            
            
            
            
            $pedidoTransferenciaResource =  new PedidoTransferenciaResource($pedidoTransferenciaCreado);
            $responseResource = new ResponseResource(null);
            
           
            $responseResource->title('Pedido de transferencia creado exitosamente');       
            $responseResource->body($pedidoTransferenciaResource);       
            return $responseResource;
        }
        catch(\Exception $e){
            DB::rollback();
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
        
    }

    public function update($id,Request $pedidoTransferenciaData) 
    {
        
        try{
            DB::beginTransaction();
            $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerPorId($id);
            
            if (!$pedidoTransferencia){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Pedido de transferencia no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            

            
            
            $this->pedidoTransferenciaRepository->setModel($pedidoTransferencia);
            $pedidoTransferenciaDataArray= Algorithm::quitNullValuesFromArray($pedidoTransferenciaData->all());
            $this->pedidoTransferenciaRepository->actualiza($pedidoTransferenciaDataArray);
            $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
            
            DB::commit();
            $pedidoTransferenciaResource =  new PedidoTransferenciaResource($pedidoTransferencia);
            $responseResource = new ResponseResource(null);
            
            $responseResource->title('Pedido de transferencia actualizado exitosamente');       
            $responseResource->body($pedidoTransferenciaResource);     
            
            return $responseResource;
        }
        catch(\Exception $e){
            DB::rollback();
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
        
    }

    public function destroy($id) 
    {
              
 
        try{
            DB::beginTransaction();
            $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerPorId($id);
            
            if (!$pedidoTransferencia){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Pedido de transferencia no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->pedidoTransferenciaRepository->setModel($pedidoTransferencia);
            $this->pedidoTransferenciaRepository->softDelete();
            

              
            $responseResource = new ResponseResource(null);
            $responseResource->title('Pedido de transferencia eliminado');  
            $responseResource->body(['id' => $id]);
            DB::commit();
            return $responseResource;
        }
        catch(\Exception $e){
         
            DB::rollback();
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

       
    }

    public function verPedidosTransferenciaRecibidos($idAlmacenD)
    {
        {
            try{
                $pedidosTransferencia = $this->pedidoTransferenciaRepository->obtenerPedidosTransferenciaPorAlmacenD($idAlmacenD);
                foreach ($pedidosTransferencia as $key => $pedidoTransferencia) {
                    $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pedidoTransferencia); 
                    $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pedidoTransferencia); 
                    $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship($pedidoTransferencia);
                    
                }
                
                if (!$pedidosTransferencia){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('Este almacen no tiene Pedidos de Transferencia ');
                    $notFoundResource->notFound(['id' => $idAlmacenD]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
    
                           
                $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia); 
                $responseResource = new ResponseResource(null);
                $responseResource->title('Listado de Pedidos de Transferencia recibidos');  
                $responseResource->body($pedidosTransferenciaResource);
                return $responseResource;
            }
            catch(\Exception $e){
             
                
                return (new ExceptionResource($e))->response()->setStatusCode(500);
                
            }
    
        }
    }
    public function obtenerPedidoTransferenciaPorId($idPedidoTransferencia)
    {
        try{
            $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerPedidoTransferenciaConTransferenciaPorId($idPedidoTransferencia);
         
            
            if (!$pedidoTransferencia){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('No existe este pedido de transferencia ');
                $notFoundResource->notFound(['id' => $idPedidoTransferencia]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->pedidoTransferenciaRepository->loadTransferenciaRelationship($pedidoTransferencia); 
            $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pedidoTransferencia); 
            $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pedidoTransferencia);               
            $pedidoTransferenciaResource =  new PedidoTransferenciaResource($pedidoTransferencia); 
            $responseResource = new ResponseResource(null);
            $responseResource->title('Pedido de Transferencia encontrado');  
            $responseResource->body($pedidoTransferenciaResource);
            return $responseResource;
        }
        catch(\Exception $e){
         
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

    }

    public function evaluarPedidoTransferencia($idPedidoTransferencia,Request $data)
    {
        try{
            $dataArray=$data->all();
            $dataArray= Algorithm::quitNullValuesFromArray($dataArray);
            $validator = \Validator::make($dataArray, 
                            [ 
                            'evaluacion' => 'required'
                            ]);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerPedidoTransferenciaConTransferenciaPorId($idPedidoTransferencia);
            $evaluacion = $data['evaluacion'];
            
            if (!$pedidoTransferencia){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('No existe este pedido de transferencia ');
                $notFoundResource->notFound(['id' => $idPedidoTransferencia]);
                return $notFoundResource->response()->setStatusCode(404);
            }

            if ($pedidoTransferencia->fueEvaluado()){
                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de validación');
                $errorResource->message('El pedido de transferencia ya fue evaluado');
                return $errorResource->response()->setStatusCode(400);
            }

           


            
            
            $responseResource = new ResponseResource(null);
            DB::beginTransaction();
            $this->pedidoTransferenciaRepository->setModel($pedidoTransferencia);
            $this->pedidoTransferenciaRepository->setLineasPedidoTransferenciaByOwnModel();
            
            if ($pedidoTransferencia->estaEnPrimerIntento()){
                
                if ($evaluacion){
                    $dataArray['estado']='Aceptado';
                    $dataArray['deleted']=false;
                    $this->pedidoTransferenciaRepository->setTransferenciaData($dataArray);
                    $this->pedidoTransferenciaRepository->attachTransferenciaWithOwnModels();
                    $this->pedidoTransferenciaRepository->loadTransferenciaRelationShip();
                    $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
                    DB::commit();
                    $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship();
                    $text= "Pedido de transferencia aceptado en el intento {$pedidoTransferencia->fase}";
                    $pedidoTransferenciaResource =  new PedidoTransferenciaResource($pedidoTransferencia);
                    $responseResource->title($text);  
                    $responseResource->body($pedidoTransferenciaResource);
                }
                else{
                    
                    $dataArray['estado']='Denegado';
                    $dataArray['deleted']=false;
                    $this->pedidoTransferenciaRepository->setTransferenciaData($dataArray);
                    $this->pedidoTransferenciaRepository->attachTransferenciaWithOwnModels();
                    //$this->pedidoTransferenciaRepository->loadTransferenciaRelationShip();
                    $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
                    $text= "Pedido de transferencia denegado en el intento {$pedidoTransferencia->fase}, se generó un nuevo pedido de transferencia al segundo almacén más cercano";
                    
                    $lineasPedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerLineasPedidoTransferenciaFromOwnModel();
                    
                    $almacenOrigen = $this->pedidoTransferenciaRepository->getAlmacenById($pedidoTransferencia->idAlmacenO);
     
                    if (!$almacenOrigen){
                        $notFoundResource = new NotFoundResource(null);
                        $notFoundResource->title('Almacen no encontrado');
                        $notFoundResource->notFound(['idAlmacen'=>$pedidoTransferencia->idAlmacenO]);
                        return $notFoundResource->response()->setStatusCode(404);
                    }
                    $almacenService = new AlmacenService;
                    $pedidoTransferenciaService = new PedidoTransferenciaService;
                    $almacenCercano = $almacenService->obtenerAlmacenCercano($almacenOrigen,2);
                    $pedidoTransferencia->idAlmacenD = $almacenCercano->id;
                    
                    $nuevoPedidoTransferenciaArray = $pedidoTransferenciaService->nuevaInstancia($pedidoTransferencia,2);
                    
                    $nuevasListasArray = $pedidoTransferenciaService->nuevasLineasPedidoTransferencia($lineasPedidoTransferencia);
                    
                    $this->pedidoTransferenciaRepository->guarda($nuevoPedidoTransferenciaArray);
                    
                
                    
                    $list_collection = new Collection($nuevasListasArray);
                            
                    foreach ($list_collection as $key => $elem) {
                        
                        $this->pedidoTransferenciaRepository->setLineaPedidoTransferenciaData($elem);
                        $this->pedidoTransferenciaRepository->attachLineaPedidoTransferenciaWithOwnModels();
                                    
                    }
                    
                    $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
                    DB::commit();
                    $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship();
                    $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship();
                    $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship();
                    $this->pedidoTransferenciaRepository->loadUsuarioRelationship();
                    $pedidoTransferenciaResource =  new PedidoTransferenciaResource($pedidoTransferencia);
                    $responseResource->title($text);  
                    $responseResource->body($pedidoTransferenciaResource);
                    
                }

            }
            else if ($pedidoTransferencia->estaEnSegundoIntento()){
                
                if ($evaluacion){
                    $dataArray['estado']='Aceptado';
                    $dataArray['deleted']=false;
                    $this->pedidoTransferenciaRepository->setTransferenciaData($dataArray);
                    $this->pedidoTransferenciaRepository->attachTransferenciaWithOwnModels();
                    $this->pedidoTransferenciaRepository->loadTransferenciaRelationShip();
                    $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
                    DB::commit();
                    $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship();
                    $text= "Pedido de transferencia aceptado en el intento {$pedidoTransferencia->fase}";
                    $pedidoTransferenciaResource =  new PedidoTransferenciaResource($pedidoTransferencia);
                    $responseResource->title($text);  
                    $responseResource->body($pedidoTransferenciaResource);

                }
                else{
                    $dataArray['estado']='Denegado';
                    $dataArray['deleted']=false;
                    $this->pedidoTransferenciaRepository->setTransferenciaData($dataArray);
                    $this->pedidoTransferenciaRepository->attachTransferenciaWithOwnModels();
                    //$this->pedidoTransferenciaRepository->loadTransferenciaRelationShip();
                    $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
                    $text= "Pedido de transferencia denegado en el intento {$pedidoTransferencia->fase}, se generó un nuevo pedido de transferencia al almacen central";
                    $lineasPedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerLineasPedidoTransferenciaFromOwnModel();
                    $almacenOrigen = $this->pedidoTransferenciaRepository->getAlmacenById($pedidoTransferencia->idAlmacenO);
     
                    if (!$almacenOrigen){
                        $notFoundResource = new NotFoundResource(null);
                        $notFoundResource->title('Almacen no encontrado');
                        $notFoundResource->notFound(['idAlmacen'=>$pedidoTransferencia->idAlmacenO]);
                        return $notFoundResource->response()->setStatusCode(404);
                    }
                    $almacenService = new AlmacenService;
                    $pedidoTransferenciaService = new PedidoTransferenciaService;
                    $almacenCentral = $this->pedidoTransferenciaRepository->getAlmacenCentral();
                    $pedidoTransferencia->idAlmacenD = $almacenCentral->id;
                    $nuevoPedidoTransferenciaArray = $pedidoTransferenciaService->nuevaInstancia($pedidoTransferencia,3);
                    $nuevasListasArray = $pedidoTransferenciaService->nuevasLineasPedidoTransferencia($lineasPedidoTransferencia);
                    
                    $this->pedidoTransferenciaRepository->guarda($nuevoPedidoTransferenciaArray);
                    
                
                    
                    $list_collection = new Collection($nuevasListasArray);
                            
                    foreach ($list_collection as $key => $elem) {
                        
                        $this->pedidoTransferenciaRepository->setLineaPedidoTransferenciaData($elem);
                        $this->pedidoTransferenciaRepository->attachLineaPedidoTransferenciaWithOwnModels();
                                    
                    }
                    
                    $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
                    DB::commit();
                    $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship();
                    $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship();
                    $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship();
                    $this->pedidoTransferenciaRepository->loadUsuarioRelationship();
                    $pedidoTransferenciaResource =  new PedidoTransferenciaResource($pedidoTransferencia);
                    $responseResource->title($text);  
                    $responseResource->body($pedidoTransferenciaResource);
                    
                }
            }
            else if ($pedidoTransferencia->estaEnTercerIntento()){
                
                if ($evaluacion){
                    $dataArray['estado']='Aceptado';
                    $dataArray['deleted']=false;
                    $this->pedidoTransferenciaRepository->setTransferenciaData($dataArray);
                    $this->pedidoTransferenciaRepository->attachTransferenciaWithOwnModels();
                    $this->pedidoTransferenciaRepository->loadTransferenciaRelationShip();
                    $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
                    DB::commit();
                    $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship();
                    $text= "Pedido de transferencia aceptado en el intento {$pedidoTransferencia->fase}";
                    $pedidoTransferenciaResource =  new PedidoTransferenciaResource($pedidoTransferencia);
                    $responseResource->title($text);  
                    $responseResource->body($pedidoTransferenciaResource);
                }
                else{
                    
                    $dataArray['estado']='Denegado';
                    $dataArray['deleted']=false;
                    $this->pedidoTransferenciaRepository->setTransferenciaData($dataArray);
                    $this->pedidoTransferenciaRepository->attachTransferenciaWithOwnModels();
                    $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
                    $text= "Pedido de transferencia denegado en el intento {$pedidoTransferencia->fase}, se generá agragara o acumulará una linea en la solicitud de compra";
                    
                    $lineasPedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerLineasPedidoTransferenciaFromOwnModel();
                    $almacenOrigen = $this->pedidoTransferenciaRepository->getAlmacenById($pedidoTransferencia->idAlmacenO);
     
                    if (!$almacenOrigen){
                        $notFoundResource = new NotFoundResource(null);
                        $notFoundResource->title('Almacen no encontrado');
                        $notFoundResource->notFound(['idAlmacen'=>$pedidoTransferencia->idAlmacenO]);
                        return $notFoundResource->response()->setStatusCode(404);
                    }
                    
                    
                   
                        
                    $solicitud = $this->solicitudCompraRepository->obtenerSolicitudDisponible();
                    if (!$solicitud){
                        $solicitud=  $this->solicitudCompraRepository->crearNueva();
                        
                        $this->solicitudCompraRepository->setModel($solicitud);
                    }
                    $this->lineaSolicitudCompraRepository->setSolicitudCompraModel($solicitud);
                    foreach ($lineasPedidoTransferencia as $key => $lpt) {
                        $producto = $this->lineaSolicitudCompraRepository->getProductoById($lpt->idProducto);
                        
                        $lineaSolicitudCompra = $this->lineaSolicitudCompraRepository->attachOrAccumulateLineaSolicitudCompra($producto,$lpt->cantidad);
                        
                        $this->lineaPedidoTransferenciaRepository->setModel($lpt);
                        
                        $this->lineaPedidoTransferenciaRepository->attachLineaSolicitudTransferencia($lineaSolicitudCompra);
                        $this->lineaSolicitudCompraRepository->loadProductoRelationship($lineaSolicitudCompra);
                    }
                                   
                    
                    $this->solicitudCompraRepository->setModel($solicitud);
                    
                    $this->solicitudCompraRepository->loadLineasSolicitudCompraRelationship();
                   

                    $solicitud = $this->solicitudCompraRepository->obtenerModelo();
                    
                    DB::commit();
                    $solicitudCompraResource =  new SolicitudCompraResource($solicitud);
                    $responseResource->title($text);  
                    $responseResource->body($solicitudCompraResource);
                    
                }
            }        
            
            
            return $responseResource;
        }
        catch(\Exception $e){
         
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    
    } 
    public function verPedidosTransferenciaJTO($idAlmacenO)
    {
        {
            try{
                $pedidosTransferencia = $this->pedidoTransferenciaRepository->obtenerPedidosTransferenciaJTO($idAlmacenO);
                foreach ($pedidosTransferencia as $key => $pedidoTransferencia) {
                    $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pedidoTransferencia); 
                    $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pedidoTransferencia); 
                    $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship($pedidoTransferencia);
                    
                }
                
                if (!$pedidosTransferencia){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('No se han realizado pedidos de transferencia');
                    $notFoundResource->notFound(['id' => $idAlmacenO]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
    
                           
                $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia); 
                $responseResource = new ResponseResource(null);
                $responseResource->title('Listado de Pedidos de Transferencia emitidos por esta tienda (Jefe de Tienda)');  
                $responseResource->body($pedidosTransferenciaResource);
                return $responseResource;
            }
            catch(\Exception $e){
             
                
                return (new ExceptionResource($e))->response()->setStatusCode(500);
                
            }
    
        }
    }
    public function verPedidosTransferenciaJAD($idAlmacenD)
    {
        {
            try{
                $pedidosTransferencia = $this->pedidoTransferenciaRepository->obtenerPedidosTransferenciaJAD($idAlmacenD);
                foreach ($pedidosTransferencia as $key => $pedidoTransferencia) {
                    $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pedidoTransferencia); 
                    $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pedidoTransferencia); 
                    $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship($pedidoTransferencia);
                    
                }
                
                if (!$pedidosTransferencia){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('No se han recibido pedidos de transferencia');
                    $notFoundResource->notFound(['id' => $idAlmacenO]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
    
                           
                $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia); 
                $responseResource = new ResponseResource(null);
                $responseResource->title('Listado de Pedidos de Transferencia recibidos por esta tienda (Jefe de Almacen)');  
                $responseResource->body($pedidosTransferenciaResource);
                return $responseResource;
            }
            catch(\Exception $e){
             
                
                return (new ExceptionResource($e))->response()->setStatusCode(500);
                
            }
    
        }
    }
    public function verPedidosTransferenciaJTD($idAlmacenD)
    {
        {
            try{
                $pedidosTransferencia = $this->pedidoTransferenciaRepository->obtenerPedidosTransferenciaJTD($idAlmacenD);
                foreach ($pedidosTransferencia as $key => $pedidoTransferencia) {
                    $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pedidoTransferencia); 
                    $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pedidoTransferencia); 
                    $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship($pedidoTransferencia);
                    
                }
                
                if (!$pedidosTransferencia){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('No se han recibido pedidos de transferencia');
                    $notFoundResource->notFound(['id' => $idAlmacenO]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
    
                           
                $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia); 
                $responseResource = new ResponseResource(null);
                $responseResource->title('Listado de Pedidos de Transferencia recibidos por esta tienda (Jefe de Tienda)');  
                $responseResource->body($pedidosTransferenciaResource);
                return $responseResource;
            }
            catch(\Exception $e){
             
                
                return (new ExceptionResource($e))->response()->setStatusCode(500);
                
            }
    
        }
    }
}




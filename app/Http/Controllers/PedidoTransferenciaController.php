<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PedidoTransferencia;
use App\Models\Transferencia;
use App\Models\Usuario;
use App\Models\Almacen;
use App\Repositories\PedidoTransferenciaRepository;
use App\Repositories\LineaPedidoTransferenciaRepository;
use App\Repositories\SolicitudCompraRepository;
use App\Repositories\LineaSolicitudCompraRepository;
use App\Repositories\MovimientoTipoStockRepository;
use App\Http\Resources\LineaSolicitudCompraResource;
use App\Http\Resources\LineasSolicitudCompraResource;
use Illuminate\Support\Facades\Log;
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
    protected $movimientoTipoStockRepository;

    public function __construct(PedidoTransferenciaRepository $pedidoTransferenciaRepository, SolicitudCompraRepository $solicitudCompraRepository,LineaSolicitudCompraRepository $lineaSolicitudCompraRepository, LineaPedidoTransferenciaRepository $lineaPedidoTransferenciaRepository, MovimientoTipoStockRepository $movimientoTipoStockRepository)
    {
        PedidoTransferenciaResource::withoutWrapping();
        
        SolicitudCompraResource::withoutWrapping();
        LineaSolicitudCompraResource::withoutWrapping();
        
        $this->pedidoTransferenciaRepository = $pedidoTransferenciaRepository;
        $this->solicitudCompraRepository = $solicitudCompraRepository;
        $this->lineaSolicitudCompraRepository = $lineaSolicitudCompraRepository;
        $this->lineaPedidoTransferenciaRepository = $lineaPedidoTransferenciaRepository;
        $this->movimientoTipoStockRepository = $movimientoTipoStockRepository;
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
                            $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pt);
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
                            $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pt);
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
                            $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pt);
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
                            $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pt);
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
                            $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pt);
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
                $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pt);
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
            $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship();
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
        
        ini_set("max_execution_time", 1000 );
        try{
            $dataArray=$data->all();
            $validator = \Validator::make($dataArray, 
                            [ 
                            'idUsuario' => 'required',
                            'idAlmacen' => 'required',
                            'lineasPedidoTransferencia'=>  'required'
                            ]);
            
            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            $usuario = $this->pedidoTransferenciaRepository->getUsuarioById($data['idUsuario']);
     
            if (!$usuario){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Usuario no encontrado');
                $notFoundResource->notFound(['idUsuario'=>$data['idUsuario']]);
                return $notFoundResource->response()->setStatusCode(404);
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
            
            
            $this->pedidoTransferenciaRepository->setAlmacenModel($almacen);
            $tienda = $this->pedidoTransferenciaRepository->getTiendaDeAlmacenOwnModel();
            if (!$tienda){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Tienda no encontrada');
                $notFoundResource->notFound(['idTienda'=>$tienda->id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $idUsuario =  $data['idUsuario'];
            $usuario = $this->pedidoTransferenciaRepository->getUsuarioById($idUsuario);
            
            if (!$usuario){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('No existe este usuario');
                $notFoundResource->notFound(['id' => $idUsuario]);
                return $notFoundResource->response()->setStatusCode(404);
            }

            $this->pedidoTransferenciaRepository->setUsuarioModel($usuario);
           
            if (!($this->pedidoTransferenciaRepository->usuarioEsJefeDeAlmacenDe($tienda))){
                
                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de validación');
                $errorResource->message('No tiene los privilegios para crear el pedido de transferencia con el almacen solicitado');
                return $errorResource->response()->setStatusCode(400);
            }
            
            DB::beginTransaction();
            $dataArray['idAlmacenO']=$almacen->id;
            
            $almacenService =  new AlmacenService;
            $list = $data['lineasPedidoTransferencia'];
            $list_collection = new Collection($list);
            
            /*
            $almacenCercano= $almacenService->obtenerAlmacenCercanoConStockAlt($almacen,1,$data['lineasPedidoTransferencia']);
            
            if(!$almacenCercano){ //no hay almacen cercano por algoritmo, buscaremos en stock central
                
                $almacenCentral = $this->pedidoTransferenciaRepository->getAlmacenCentral();
                
                if (!$almacenService->tieneStock($almacenCentral,$list_collection)){//no hay stock en almacen central tampoco
                    $text = 'Pedido de transferencia creado exitosamente, aunque no se encontró ningun almacén destino para él';
                    $dataArray['idAlmacenD']=null;
                    $dataArray['fase']=null;
                    $dataArray['JTO']=$dataArray['JAD']=$dataArray['JTD']=false;
                    
                    $this->pedidoTransferenciaRepository->guarda($dataArray);
                    $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
                    $this->pedidoTransferenciaRepository->setModel($pedidoTransferencia);
                    foreach ($list_collection as $key => $elem) {
                        
                        $this->pedidoTransferenciaRepository->setLineaPedidoTransferenciaData($elem);
                        $this->pedidoTransferenciaRepository->attachLineaPedidoTransferenciaWithOwnModels();
                                    
                    }
                    DB::commit();
                            
                    
                    $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship();
                    $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship();
                    $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship();
                    $pedidoTransferenciaCreado = $this->pedidoTransferenciaRepository->obtenerModelo();
                    $pedidoTransferenciaResource =  new PedidoTransferenciaResource($pedidoTransferenciaCreado);
                    $responseResource = new ResponseResource(null);
                    $responseResource->title($text);       
                    $responseResource->body($pedidoTransferenciaResource);       
                    return $responseResource;


                    

                    $this->pedidoTransferenciaRepository->setLineasPedidoTransferenciaByOwnModel();
                    $lineasPedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerLineasPedidoTransferenciaFromOwnModel();
                    $transferenciaData['estado']='Denegado';
                    $transferenciaData['deleted']=false;
                    $this->pedidoTransferenciaRepository->setTransferenciaData($transferenciaData);
                    $this->pedidoTransferenciaRepository->attachTransferenciaWithOwnModels();
            
                    //si el almacen central no tiene stock se envia de frente la solicitud de compra
                    $text= "Solicitud de compra creada directamente, aunque tambien se creo uno pedido de tranferencia y su respectiva transferencia con almacen destino vacío";
                    
                    $solicitud = $this->enviarSolicitudCompra($dataArray,$text, $lineasPedidoTransferencia);
                    
                    DB::commit();
                    $solicitudCompraResource =  new SolicitudCompraResource($solicitud);
                    $responseResource = new ResponseResource(null);
                    $responseResource->title($text);  
                    $responseResource->body($solicitudCompraResource);
                    return $responseResource; //Esta es una salida de emergencia
                    
                }
                else{
                    $text= "Pedido de transferencia creado directamente en fase 3 para el almacen central, por no haber un almacen cercano";
                  
                    $dataArray['idAlmacenD']=$almacenCentral->id;
                    $dataArray['fase']=3;
                   
                }
                
            }
            else{
                $dataArray['idAlmacenD']=$almacenCercano->id;
                $dataArray['fase']=1;
                $text='Pedido de transferencia creado exitosamente';
            }
            */
            
            // inicializacion de los flags
            // $dataArray['aceptoJTO'] = false;
            // $dataArray['aceptoJAD'] = false;
            // $dataArray['aceptoJTD'] = false;
            
            $this->pedidoTransferenciaRepository->guarda($dataArray);
            $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
            
            foreach ($list_collection as $key => $elem) {
                
                $this->pedidoTransferenciaRepository->setLineaPedidoTransferenciaData($elem);
                $this->pedidoTransferenciaRepository->attachLineaPedidoTransferenciaWithOwnModels();
                            
            }
            
            DB::commit();
                       
            //return $this->pedidoTransferenciaRepository->obtenerModelo();
            $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship();
            $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship();
            //$this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship();
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
                    $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pedidoTransferencia);
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
            $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pedidoTransferencia);             
            $pedidoTransferenciaResource =  new PedidoTransferenciaResource($pedidoTransferencia); 
            $responseResource = new ResponseResource(null);
            $responseResource->title('Pedido de Transferencia encontrado');  
            $responseResource->body($pedidoTransferenciaResource);
            return $responseResource;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

    }

    public function evaluarPedidoTransferencia($idPedidoTransferencia,Request $data)
    {
        try{
            ini_set("max_execution_time", 1000 );
            
            

            $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerPedidoTransferenciaConTransferenciaPorId($idPedidoTransferencia);
            
            $dataArray=$data->all();
            $dataArray= Algorithm::quitNullValuesFromArray($dataArray);
            $validator = \Validator::make($dataArray, 
                            [ 
                            'evaluacion' => 'required',
                            'idUsuario'=>'required'
                            ]);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            $idUsuario =  $data['idUsuario'];
            $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerPedidoTransferenciaConTransferenciaPorId($idPedidoTransferencia);
            /* Validaciones generales */
            if (!$pedidoTransferencia){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('No existe este pedido de transferencia');
                $notFoundResource->notFound(['id' => $idPedidoTransferencia]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            if ($pedidoTransferencia->fueEvaluado()){
                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de validación');
                $errorResource->message('El pedido de transferencia ya culminó su ciclo de evaluación');
                return $errorResource->response()->setStatusCode(400);
            }
            if (!$pedidoTransferencia->fueAceptadoJTO()){
                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de validación');
                $errorResource->message('El pedido de transferencia aún no fue aceptado por su jefe de tienda correspondiente');
                return $errorResource->response()->setStatusCode(400);
            }
            $this->pedidoTransferenciaRepository->setModel($pedidoTransferencia);
            $usuario = $this->pedidoTransferenciaRepository->getUsuarioById($idUsuario);
            
            if (!$usuario){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('No existe este usuario');
                $notFoundResource->notFound(['id' => $idUsuario]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            /* Fin de validaciones generales */
            $evaluacion = $data['evaluacion'];
            $responseResource = new ResponseResource(null);
            DB::beginTransaction();
            
            $this->pedidoTransferenciaRepository->setLineasPedidoTransferenciaByOwnModel();
            $this->pedidoTransferenciaRepository->setUsuarioModel($usuario);
            $almacenCentral = null;
            
            //Para los de fase 1
            if ($pedidoTransferencia->estaEnPrimerIntento()){
                
                $tienda = $this->pedidoTransferenciaRepository->getTiendaDeAlmacenDestino();
                /* Validaciones de fase 1(es igual que para fase 2)*/
                if (!$tienda){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('No existe la tienda del almacén destino asociado al pedido de 
                                             transferencia');
                    $notFoundResource->notFound(['id' => $idPedidoTransferencia]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
                if($usuario->noEsJefe()){
                    //si usuario es trabajador
                    $errorResource = new ErrorResource(null);
                    $errorResource->title('Error de autorización');
                    $errorResource->message('El usuario no es jefe de tienda o de almacen');
                    return $errorResource->response()->setStatusCode(400);
                }

                if ($usuario->esJefeDeTiendaAsignado()){
                    if(!$this->pedidoTransferenciaRepository->usuarioEsJefeDeTiendaDe($tienda)){
                        $errorResource = new ErrorResource(null);
                        
                        $errorResource->title('Error de validación');
                        $errorResource->message('El usuario es jefe de tienda, pero no de la tienda relacionada con el almacen destino del pedido de transferencia');
                        return $errorResource->response()->setStatusCode(400);
                    }   
                    else{
                        //verificar si el jefe de almacen destino acepto el pedido
                        if(!$pedidoTransferencia->fueAceptadoJAD()){
                            $errorResource = new ErrorResource(null);
                            $errorResource->title('Error de validación');
                            $errorResource->message('El jefe de almacen de la tienda relacionada al almacén destino del pedido de transferencia, no ha evaluado aún');
                            return $errorResource->response()->setStatusCode(400);
                        }
                    }
                }
             
                if ($usuario->esJefeDeAlmacenAsignado()){
                    
                    //return json_encode($this->pedidoTransferenciaRepository->usuarioEsJefeDeAlmacenDe($tienda));

                    if( $pedidoTransferencia->fueAceptadoJAD()){
                        $errorResource = new ErrorResource(null);
                        $errorResource->title('Error de autorización');
                        $errorResource->message('El pedido de transferencia ya fue validado por el jefe de almacén de su almacén destino');
                        return $errorResource->response()->setStatusCode(400);     
                    }
                    else if (!$this->pedidoTransferenciaRepository->usuarioEsJefeDeAlmacenDe($tienda)){
                        // si usuario es jefe de almacen
                        $errorResource = new ErrorResource(null);
                        $errorResource->title('Error de autorización');
                        $errorResource->message('El usuario es jefe de almacén, pero no de la tienda relacionada con el almacen destino del pedido de transferencia');
                        return $errorResource->response()->setStatusCode(400);
                    } 

                }
                /* Fin de validaciones de fase 1*/  
                if ($evaluacion){
                    
                    if ($usuario->esJefeDeAlmacenAsignado()){
                        
                        $this->pedidoTransferenciaRepository->actualiza(['aceptoJAD'=>true]);
                        DB::commit();
                        $text= "Pedido de transferencia aceptado por el jefe de almacen en el intento {$pedidoTransferencia->fase}";

                    }
                    else{
                        
                        Log::info("Test1");
                        $this->pedidoTransferenciaRepository->actualiza(['aceptoJTD'=>true]);

                        $dataArray['estado']='Aceptado';
                        $dataArray['deleted']=false;
                        $this->pedidoTransferenciaRepository->setTransferenciaData($dataArray);
                        $this->pedidoTransferenciaRepository->attachTransferenciaWithOwnModels();
                        Log::info("Test2");
                        $this->pedidoTransferenciaRepository->loadTransferenciaRelationShip();
                        $lineasPedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerLineasPedidoTransferenciaFromOwnModel();
                        // Log::info("Test3");
                        // $this->pedidoTransferenciaRepository->actualizaSumaRestaStocks($pedidoTransferencia->almacenOrigen,$pedidoTransferencia->almacenDestino,$lineasPedidoTransferencia);
                        // Log::info("Test4");

                        /**/
                        $tipoStockPrincipal = $this->pedidoTransferenciaRepository->obtenerStockPrincipal();
                        foreach ($lineasPedidoTransferencia as $key => $lt) {
                            $this->movimientoTipoStockRepository->crear(['idAlmacen' => $pedidoTransferencia->almacenOrigen->id,
                                                                         'idProducto'=>$lt->idProducto,
                                                                         'idTipoStock'=> $tipoStockPrincipal->id,
                                                                         'idUsuario'=>$usuario->idPersonaNatural,
                                                                         'cantidad'=>$lt->cantidad,
                                                                         'signo'=> '+',
                                                                         'tipo'=> 'transferencia',
                                                                         'deleted'=>false]);
                            $this->movimientoTipoStockRepository->crear(['idAlmacen' => $pedidoTransferencia->almacenDestino->id,
                                                                         'idProducto'=>$lt->idProducto,
                                                                         'idTipoStock'=> $tipoStockPrincipal->id,
                                                                         'idUsuario'=>$usuario->idPersonaNatural,
                                                                         'cantidad'=>$lt->cantidad,
                                                                         'signo'=> '-',
                                                                         'tipo'=> 'transferencia',
                                                                         'deleted'=>false]);
                        }
                        /**/
                        $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
                        Log::info("Test5");
                        DB::commit();
                        $text= "Pedido de transferencia aceptado por el jefe de tienda en el intento {$pedidoTransferencia->fase}";
                        
                    }
                    $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship();
                    
                    $pedidoTransferenciaResource =  new PedidoTransferenciaResource($pedidoTransferencia);
                    $responseResource->title($text);  
                    $responseResource->body($pedidoTransferenciaResource);
                    
                }
                else{
                    
                    if ($usuario->esJefeDeAlmacenAsignado()){
                        $this->pedidoTransferenciaRepository->actualiza(['aceptoJAD'=>false]);
                        $text= "Pedido de transferencia denegado por el jefe de almacen en el intento {$pedidoTransferencia->fase}, se generó un nuevo pedido de transferencia al segundo almacén más cercano";

                    }
                    
                    
                    $nuevaFase=2;
                    $dataArray['estado']='Denegado';
                    $dataArray['deleted']=false;
                    $this->pedidoTransferenciaRepository->setTransferenciaData($dataArray);
                    $this->pedidoTransferenciaRepository->attachTransferenciaWithOwnModels();
                    $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
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
                    /*-------------------------------*/
                    $almacenCercano = $almacenService->obtenerAlmacenCercanoConStock($almacenOrigen,2,$lineasPedidoTransferencia);
                    if(!$almacenCercano){
                        $almacenService = new AlmacenService;
                        $almacenCentral = $this->pedidoTransferenciaRepository->getAlmacenCentral();
                        /*##############################*/
                        if (!$almacenService->tieneStock($almacenCentral,$lineasPedidoTransferencia)){
                            //si el almacen central no tiene stock se envia de frente la solicitud de compra
                            $text= "Pedido de transferencia denegado por el sistema en el intento {$pedidoTransferencia->fase}, se crearán o acumularán las respectivas lineas por producto en la solicitud de compra, ya que el almacen central no tiene stock para alguno(s) de los productos";
                            return $solicitud = $this->enviarSolicitudCompra($dataArray,$text,$lineasPedidoTransferencia);
                            DB::commit();
                            $solicitudCompraResource =  new SolicitudCompraResource($solicitud);
                            $responseResource->title($text);  
                            $responseResource->body($solicitudCompraResource);
                            return $responseResource; //Esta es una salida de emergencia
                        }
                        else{
                            $text= "Pedido de transferencia denegado por el jefe de tienda en el intento {$pedidoTransferencia->fase}, se generó un nuevo pedido de transferencia al almacén central directamente, ya que no había almacen cercanos con stock para alguno(s) de los productos";
                            $pedidoTransferencia->idAlmacenD = $almacenCentral->id;
                            $nuevaFase = 3;//nos pasamos de frente a la fase 3
                           
                        }
                        
                    }
                    else{
                        $text= "Pedido de transferencia denegado por el jefe de tienda en el intento {$pedidoTransferencia->fase}, se generó un nuevo pedido de transferencia al segundo almacén más cercano";
                        $pedidoTransferencia->idAlmacenD = $almacenCercano->id;
                    }
                    
                    
                    $nuevoPedidoTransferenciaArray = $pedidoTransferenciaService->nuevaInstancia($pedidoTransferencia,$nuevaFase);
                    $nuevasListasArray = $pedidoTransferenciaService->nuevasLineasPedidoTransferencia($lineasPedidoTransferencia);
                    
                    $nuevoPedidoTransferenciaArray['aceptoJTO'] = true;
                    $nuevoPedidoTransferenciaArray['aceptoJAD'] = false;
                    $nuevoPedidoTransferenciaArray['aceptoJTD'] = false;
                    //return $nuevoPedidoTransferenciaArray;
                    $this->pedidoTransferenciaRepository->guarda($nuevoPedidoTransferenciaArray);
                    
                
                    
                    $list_collection = new Collection($nuevasListasArray);
                            
                    foreach ($list_collection as $key => $elem) {
                        
                        $this->pedidoTransferenciaRepository->setLineaPedidoTransferenciaData($elem);
                        $this->pedidoTransferenciaRepository->attachLineaPedidoTransferenciaWithOwnModels();
                                    
                    }
                    
                    $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
                    /*------------*/
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
            //Para los de fase 2
            else if ($pedidoTransferencia->estaEnSegundoIntento()){
                
                $tienda = $this->pedidoTransferenciaRepository->getTiendaDeAlmacenDestino();
                /* Validaciones de fase 2(es igual que para fase 1)*/
                if (!$tienda){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('No existe la tienda del almacén destino asociado al pedido de 
                                             transferencia');
                    $notFoundResource->notFound(['id' => $idPedidoTransferencia]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
                if($usuario->noEsJefe()){
                    //si usuario es trabajador
                    $errorResource = new ErrorResource(null);
                    $errorResource->title('Error de autorización');
                    $errorResource->message('El usuario no es jefe de tienda o de almacen');
                    return $errorResource->response()->setStatusCode(400);
                }

                if ($usuario->esJefeDeTiendaAsignado()){
                    if(!$this->pedidoTransferenciaRepository->usuarioEsJefeDeTiendaDe($tienda)){
                        $errorResource = new ErrorResource(null);
                        
                        $errorResource->title('Error de validación');
                        $errorResource->message('El usuario es jefe de tienda, pero no de la tienda 
                                                relacionada con el almacen destino del pedido de 
                                                transferencia');
                        return $errorResource->response()->setStatusCode(400);
                    }   
                    else{
                        //verificar si el jefe de almacen destino acepto el pedido
                        if(!$pedidoTransferencia->fueAceptadoJAD()){
                            $errorResource = new ErrorResource(null);
                            $errorResource->title('Error de validación');
                            $errorResource->message('El jefe de almacen de la tienda relacionada al almacén destino del pedido de transferencia, no ha evaluado aún');
                            return $errorResource->response()->setStatusCode(400);
                        }
                    }
                }
             
                if ($usuario->esJefeDeAlmacenAsignado()){
                    if( $pedidoTransferencia->fueAceptadoJAD()){
                        $errorResource = new ErrorResource(null);
                        $errorResource->title('Error de autorización');
                        $errorResource->message('El pedido de transferencia ya fue validado por el jefe de almacén de su almacén destino');
                        return $errorResource->response()->setStatusCode(400);     
                    }
                    else if (!$this->pedidoTransferenciaRepository->usuarioEsJefeDeAlmacenDe($tienda)){
                        // si usuario es jefe de almacen
                        $errorResource = new ErrorResource(null);
                        $errorResource->title('Error de autorización');
                        $errorResource->message('El usuario es jefe de almacén, pero no de la tienda relacionada con el almacen destino del pedido de transferencia');
                        return $errorResource->response()->setStatusCode(400);
                    } 

                }
                /* Fin de validaciones de fase 2 */
                if ($evaluacion){

                    if ($usuario->esJefeDeAlmacenAsignado()){
                        $this->pedidoTransferenciaRepository->actualiza(['aceptoJAD'=>true]);
                        DB::commit();
                        $text= "Pedido de transferencia aceptado por el jefe de almacen en el intento {$pedidoTransferencia->fase}";

                    }
                    else{
                      
                        $this->pedidoTransferenciaRepository->actualiza(['aceptoJTD'=>true]);
                        $dataArray['estado']='Aceptado';
                        $dataArray['deleted']=false;
                        $this->pedidoTransferenciaRepository->setTransferenciaData($dataArray);
                        $this->pedidoTransferenciaRepository->attachTransferenciaWithOwnModels();
                        $this->pedidoTransferenciaRepository->loadTransferenciaRelationShip();
                        $lineasPedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerLineasPedidoTransferenciaFromOwnModel();
                        $this->pedidoTransferenciaRepository->actualizaSumaRestaStocks($pedidoTransferencia->almacenOrigen,$pedidoTransferencia->almacenDestino,$lineasPedidoTransferencia);
                        $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
                        DB::commit();
                        
                        $text= "Pedido de transferencia aceptado por el jefe de tienda en el intento {$pedidoTransferencia->fase}";
                        
                    }
                    $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship();
                    
                    $pedidoTransferenciaResource =  new PedidoTransferenciaResource($pedidoTransferencia);
                    $responseResource->title($text);  
                    $responseResource->body($pedidoTransferenciaResource);
                    

                }
                else{
                    
                    if ($usuario->esJefeDeAlmacenAsignado()){
                        $this->pedidoTransferenciaRepository->actualiza(['aceptoJAD'=>false]);
                        Log::info(json_encode($pedidoTransferencia));
                        $text= "Pedido de transferencia denegado por el jefe de almacen en el intento {$pedidoTransferencia->fase}";

                    }
                    else{
                        
                        $text= "Pedido de transferencia denegado por el jefe de tienda en el intento {$pedidoTransferencia->fase}, se generó un nuevo pedido de transferencia al almacen central";
                    }
                    $dataArray['estado']='Denegado';
                    $dataArray['deleted']=false;
                    $this->pedidoTransferenciaRepository->setTransferenciaData($dataArray);
                    $this->pedidoTransferenciaRepository->attachTransferenciaWithOwnModels();
                    
                    $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
                    
                    $lineasPedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerLineasPedidoTransferenciaFromOwnModel();
                    $almacenOrigen = $this->pedidoTransferenciaRepository->getAlmacenById($pedidoTransferencia->idAlmacenO);
                    
                    if (!$almacenOrigen){
                        $notFoundResource = new NotFoundResource(null);
                        $notFoundResource->title('Almacen no encontrado');
                        $notFoundResource->notFound(['idAlmacen'=>$pedidoTransferencia->idAlmacenO]);
                        return $notFoundResource->response()->setStatusCode(404);
                    }
                    $almacenService = new AlmacenService;
                    $almacenCentral = $this->pedidoTransferenciaRepository->getAlmacenCentral();
                    /*##############################*/
                    
                    if (!$almacenService->tieneStock($almacenCentral,$lineasPedidoTransferencia)){
                        //si el almacen central no tiene stock se envia de frente la solicitud de compra
                        
                        $text= "Pedido de transferencia denegado por el sistema en el intento {$pedidoTransferencia->fase}, se crearán o acumularán las respectivas lineas por producto en la solicitud de compra, ya que el almacen central no tiene stock para alguno(s) de los productos";
                        
                        $solicitud = $this->enviarSolicitudCompra($dataArray,$text,$lineasPedidoTransferencia);
                   
                        DB::commit();
                        $solicitudCompraResource =  new SolicitudCompraResource($solicitud);
                        $responseResource->title($text);  
                        $responseResource->body($solicitudCompraResource);
                        return $responseResource; //Esta es una salida de emergencia
                    }
                    /*################################*/
                    
                    $pedidoTransferenciaService = new PedidoTransferenciaService;
                    $pedidoTransferencia->idAlmacenD = $almacenCentral->id;
                    $nuevoPedidoTransferenciaArray = $pedidoTransferenciaService->nuevaInstancia($pedidoTransferencia,3);
                    $nuevasListasArray = $pedidoTransferenciaService->nuevasLineasPedidoTransferencia($lineasPedidoTransferencia);

                    $nuevoPedidoTransferenciaArray['aceptoJTO'] = true;
                    $nuevoPedidoTransferenciaArray['aceptoJAD'] = false;
                    $nuevoPedidoTransferenciaArray['aceptoJTD'] = false;
                    
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
            //Para los de fase 3
            else if ($pedidoTransferencia->estaEnTercerIntento()){
                
                $almacenCentral = $this->pedidoTransferenciaRepository->getAlmacenDestino();
                $almacenService  = new AlmacenService;
                $lineasPedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerLineasPedidoTransferenciaFromOwnModel();
                
                
                /*##############################*/
                if (!$almacenService->tieneStock($almacenCentral,$lineasPedidoTransferencia)){
                    $dataArray['estado']='Aceptado';
                    $dataArray['deleted']=false;
                    $this->pedidoTransferenciaRepository->setTransferenciaData($dataArray);
                    $this->pedidoTransferenciaRepository->attachTransferenciaWithOwnModels();
                    
                    $text= "Pedido de transferencia denegado por el sistema en el intento {$pedidoTransferencia->fase}, se crearán o acumularán las respectivas lineas por producto en la solicitud de compra, ya que el almacen central no tiene stock para alguno(s) de los productos";
                    $solicitud = $this->enviarSolicitudCompra($dataArray,$text,$lineasPedidoTransferencia);
                    DB::commit();
                    $solicitudCompraResource =  new SolicitudCompraResource($solicitud);
                    $responseResource->title($text);  
                    $responseResource->body($solicitudCompraResource);
                    return $responseResource; //Esta es una salida de emergencia
                }
                /* Validaciones de fase 3 */
                if ( ( !$almacenCentral || !($almacenCentral->nombre=='Central') )){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('No se encontró el almacen central');
                    $notFoundResource->notFound(['idAlmacenCentral' => $pedidosTransferencia->almacenDestino->id]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
                if (!$usuario->esJefeDeAlmacenCentral() ||($usuario->esJefeDeAlmacenCentral() && !$this->pedidoTransferenciaRepository->usuarioEsJefeDeAlmacenCentralDe($almacenCentral) ) ){
                    $text= '';
                    if(!$usuario->esJefeDeAlmacenCentral()){
                        $text='El usuario no es jefe de almacen central';
                    }
                    else if ($usuario->esJefeDeAlmacenCentral() && !$this->pedidoTransferenciaRepository->usuarioEsJefeDeAlmacenCentralDe($almacenCentral) ){
                        $text='El usuario es jefe de almacén central, pero el almacen del cupal es jefe
                               no coincide con el almacen central que se encuentra registrado en el sistema';
                    }
                    $errorResource = new ErrorResource(null);
                    $errorResource->title('Error de autorización');
                    $errorResource->message($text);
                    return $errorResource->response()->setStatusCode(400);
                }
                /* Fin de validaciones de fase 3*/
                if ($evaluacion){
                    $this->pedidoTransferenciaRepository->actualiza(['aceptoJAD'=>true,'aceptoJTD'=>true]);
                    $dataArray['estado']='Aceptado';
                    $dataArray['deleted']=false;
                    $this->pedidoTransferenciaRepository->setTransferenciaData($dataArray);
                    $this->pedidoTransferenciaRepository->attachTransferenciaWithOwnModels();
                    $this->pedidoTransferenciaRepository->loadTransferenciaRelationShip();
                    $lineasPedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerLineasPedidoTransferenciaFromOwnModel();
                    $this->pedidoTransferenciaRepository->actualizaSumaRestaStocks($pedidoTransferencia->almacenOrigen,$pedidoTransferencia->almacenDestino,$lineasPedidoTransferencia);
                    $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
                    DB::commit();
                    
                    $text= "Pedido de transferencia aceptado por el jefe del almacen central en el intento {$pedidoTransferencia->fase}";
                    
                    
                    $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship();
                    $pedidoTransferenciaResource =  new PedidoTransferenciaResource($pedidoTransferencia);
                    $responseResource->title($text);  
                    $responseResource->body($pedidoTransferenciaResource);
                    
                }
                else{
                    $lineasPedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerLineasPedidoTransferenciaFromOwnModel();
                    $dataArray['estado']='Denegado';
                    $dataArray['deleted']=false;
                    $this->pedidoTransferenciaRepository->setTransferenciaData($dataArray);
                    $this->pedidoTransferenciaRepository->attachTransferenciaWithOwnModels();
                    
                    $text= "Pedido de transferencia denegado por el jefe del almacen central en el intento {$pedidoTransferencia->fase}, se crearán o acumularán las respectivas lineas por producto en la solicitud de compra";
                    $solicitud = $this->enviarSolicitudCompra($dataArray,$text,$lineasPedidoTransferencia);
                    DB::commit();
                    $solicitudCompraResource =  new SolicitudCompraResource($solicitud);
                    $responseResource->title($text);  
                    $responseResource->body($solicitudCompraResource);
                    
                }
            }        
            return $responseResource;
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    
    }

    protected function enviarSolicitudCompra($dataArray,$text,$lineasPedidoTransferencia)
    {
        
            
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
        return $solicitud;
        
    }

    public function verPedidosTransferenciaJTO($idAlmacenO)
    {
        {
            try{
                $pedidosTransferencia = $this->pedidoTransferenciaRepository->obtenerPedidosTransferenciaJTO($idAlmacenO);
                foreach ($pedidosTransferencia as $key => $pedidoTransferencia) {
                    $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pedidoTransferencia); 
                    $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pedidoTransferencia);
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
        try{
            $pedidosTransferencia = $this->pedidoTransferenciaRepository->obtenerPedidosTransferenciaJAD($idAlmacenD);
            foreach ($pedidosTransferencia as $key => $pedidoTransferencia) {
                $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pedidoTransferencia); 
                $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pedidoTransferencia);
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
        }catch(\Exception $e){   
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    public function verPedidosTransferenciaJTD($idAlmacenD)
    {
        {
            try{
                $pedidosTransferencia = $this->pedidoTransferenciaRepository->obtenerPedidosTransferenciaJTD($idAlmacenD);
                foreach ($pedidosTransferencia as $key => $pedidoTransferencia) {
                    $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pedidoTransferencia); 
                    $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pedidoTransferencia);
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
    public function verPedidosTransferenciaJT($idAlmacen)
    {
        try{
            $pedidosTransferencia = $this->pedidoTransferenciaRepository->obtenerPedidosTransferenciaJT($idAlmacen);
            foreach ($pedidosTransferencia as $key => $pedidoTransferencia) {
                $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pedidoTransferencia); 
                $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pedidoTransferencia);
                $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pedidoTransferencia); 
                $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship($pedidoTransferencia);
            }
            if (!$pedidosTransferencia){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('No se han realizado ni recibido pedidos de transferencia');
                $notFoundResource->notFound(['id' => $idAlmacenO]);
                return $notFoundResource->response()->setStatusCode(404);
            }        
            $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia); 
            $responseResource = new ResponseResource(null);
            $responseResource->title('Listado de Pedidos de Transferencia emitidos y recibidos por esta tienda (Jefe de Tienda)');  
            $responseResource->body($pedidosTransferenciaResource);
            return $responseResource;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }
    public function obtenerPedidosTransferenciaJefeTienda($idAlmacen)
    {
        try{
            $pedidosTransferencia = $this->pedidoTransferenciaRepository->obtenerPedidosTransferenciaJefeTienda($idAlmacen);
            foreach ($pedidosTransferencia as $key => $pedidoTransferencia) {
                $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pedidoTransferencia);
                $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pedidoTransferencia); 
                $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pedidoTransferencia); 
                $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship($pedidoTransferencia);
            }
            if (!$pedidosTransferencia){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('No se han realizado ni recibido pedidos de transferencia');
                $notFoundResource->notFound(['id' => $idAlmacenO]);
                return $notFoundResource->response()->setStatusCode(404);
            }        
            $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia); 
            $responseResource = new ResponseResource(null);
            $responseResource->title('Listado de Pedidos de Transferencia emitidos y recibidos por esta tienda (Jefe de Tienda)');  
            $responseResource->body($pedidosTransferenciaResource);
            return $responseResource;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    public function aceptaPedidoJTO($idPedidoTransferencia, Request $pedidoTransferenciaData)
    {
        
        try{
            $dataArray=$pedidoTransferenciaData->all();
            $validator = \Validator::make($dataArray, 
                            [ 
                            'idUsuario' => 'required',
                            'aceptoJTO' => 'required'
                            
                            ]);
            
            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            
            DB::beginTransaction();
            /* Validaciones */
            
                       
            $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerPorId($idPedidoTransferencia);
            $this->pedidoTransferenciaRepository->setModel($pedidoTransferencia);
            if (!$pedidoTransferencia){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Pedido de transferencia no encontrado');
                $notFoundResource->notFound(['idPedidoTransferencia'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            else if ($pedidoTransferencia->fueEvaluadoJTO() || $pedidoTransferencia->fueEvaluado()) {
                
                if ($pedidoTransferencia->fueEvaluadoJTO()){
                    
                    $text = 'El pedido de transferencia ya fue evaluado por el jede de tienda origen';
                    
                }
                else if ($pedidoTransferencia->fueEvaluado()){
                    
                    $text = 'El pedido de transferencia ya culminó su ciclo de evaluación';
                    

                }
                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de validación');
                $errorResource->message($text);
                return $errorResource->response()->setStatusCode(400);
            }
            $usuario = $this->pedidoTransferenciaRepository->getUsuarioById($pedidoTransferenciaData['idUsuario']);
            
            if (!$usuario){
                
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Usuario no encontrado');
                $notFoundResource->notFound(['idUsuario'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            else{
                $usuario->tipoUsuario;
                
                if ($usuario->esJefeDeTienda()){
                    
                    $tienda = $this->pedidoTransferenciaRepository->getTiendaDeAlmacenOrigen();
                    // Log::info(json_encode($tienda));
                    // Log::info(json_encode($tienda->jefeDeTienda));
                    // Log::info(json_encode($usuario));
                    
                    if (!($tienda->jefeDeTienda->idPersonaNatural == $usuario->idPersonaNatural)){ //si no es el jefe de tienda de la tienda
                        $errorResource = new ErrorResource(null);
                        $errorResource->title('Error de validación');
                        $errorResource->message('Es jefe de tienda, pero no corresponde al almacén del pedido de transferencia actual');
                        return $errorResource->response()->setStatusCode(400);
                    }
                }
                else{
                        $errorResource = new ErrorResource(null);
                        $errorResource->title('Error de validación');
                        $errorResource->message('El usuario no es jefe de tienda');
                        return $errorResource->response()->setStatusCode(400);
                }
                unset($dataArray['idUsuario']);
            }
            /* Fin de validaciones*/
                     
            
            $evaluacion = $pedidoTransferenciaData['aceptoJTO'];
                       
            if(!$evaluacion){ // no acepto
                
                $text = 'Pedido de transferencia rechazado por el jefe de tienda origen';
                
            }
            else{//acepto
                
                $almacenService = new AlmacenService;
                $almacenOrigen = $this->pedidoTransferenciaRepository->getAlmacenOrigen();
                $this->pedidoTransferenciaRepository->setLineasPedidoTransferenciaByOwnModel();
                $lineasPedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerLineasPedidoTransferenciaFromOwnModel();
                $almacenCercano= $almacenService->obtenerAlmacenCercanoConStock($almacenOrigen,1,$lineasPedidoTransferencia);
                
                if(!$almacenCercano){ //no hay almacen cercano por algoritmo, buscaremos en stock central
                    
                    $almacenCentral = $this->pedidoTransferenciaRepository->getAlmacenCentral();
                    
                    if (!$almacenService->tieneStock($almacenCentral,$lineasPedidoTransferencia)){//no hay stock en almacen central tampoco
                        $dataArray['aceptoJTO']=true;
                        $this->pedidoTransferenciaRepository->actualiza($dataArray);//actualizado el pedido de transferencia
                        
                        $transferenciaData['estado'] = 'Denegado';
                        $transferenciaData['deleted']=false;
                        $this->pedidoTransferenciaRepository->setTransferenciaData($transferenciaData);
                        $this->pedidoTransferenciaRepository->attachTransferenciaWithOwnModels();
                
                        //si el almacen central no tiene stock se envia de frente la solicitud de compra
                        $text= "Pedido de transferencia evaluado exitosamente por el jefe de tienda origen, aunque no se encontro almacén para el pedido y se creó una solicitud de compra";
                        $solicitud = $this->enviarSolicitudCompra(null,$text, $lineasPedidoTransferencia);
                        $this->solicitudCompraRepository->setModel($solicitud);
                        $this->solicitudCompraRepository->loadLineasSolicitudCompraRelationship();
                        DB::commit();
                        $solicitudCompraResource =  new SolicitudCompraResource($solicitud);
                        $responseResource = new ResponseResource(null);
                        $responseResource->title($text);  
                        $responseResource->body($solicitudCompraResource);
                        return $responseResource; //Esta es una salida de emergencia
                        
                    }
                    else{//no hay almacen cercano, pero está el almacen central
                        
                        $text= "Evaluado exitosamente por el jefe de tienda origen, aunque pasa directo fase 3 para el almacen central, por no haber un almacen cercano";
                      
                        $dataArray['idAlmacenD']=$almacenCentral->id;
                        $dataArray['fase']=3;
                        $dataArray['aceptoJTO']=true;
                       
                    }
                    
                }
                else{
                    
                    // el pedido de transferencia es aprobado por el jefe de tieneda y hay almacen cercano
                    $dataArray['idAlmacenD']=$almacenCercano->id;
                    $dataArray['fase']=1;
                    $dataArray['aceptoJTO']=true;
                    $text='Pedido de transferencia evaluado exitosamente por el jefe de tienda origen y llegó a un almacen cercano';
                    
                   
                    
                    
                }
            }
            
            $this->pedidoTransferenciaRepository->actualiza($dataArray);
            $this->pedidoTransferenciaRepository->setModel($pedidoTransferencia);
            
            
            $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship();
            $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship();
            $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship();
            
            // $pedidoTransferencia->transferencia;
            // $pedidoTransferencia->almacenDestino;
            
            DB::commit();
            $pedidoTransferenciaResource =  new PedidoTransferenciaResource($pedidoTransferencia);
            $responseResource = new ResponseResource(null);
            $responseResource->title($text);       
            $responseResource->body($pedidoTransferenciaResource);     
            return $responseResource;
            
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }
    public function aceptaPedidoJAD($idPedidoTransferencia, Request $pedidoTransferenciaData)
    { //ESTO NO SERVIRIA EN EL FLUJO QUE HA SIDO CONSIDERADO
        try{
            DB::beginTransaction();
            $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerPorId($idPedidoTransferencia);
            $evaluacion = $pedidoTransferenciaData['aceptoJAD'];
            if (!$pedidoTransferencia){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Pedido de transferencia no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->pedidoTransferenciaRepository->setModel($pedidoTransferencia);
            $pedidoTransferenciaDataArray= Algorithm::quitNullValuesFromArray($pedidoTransferenciaData->all());
            $this->pedidoTransferenciaRepository->actualiza($pedidoTransferenciaDataArray);
            $pedidoTransferencia = $this->pedidoTransferenciaRepository->obtenerModelo();
            if(!$evaluacion)$this->pedidoTransferenciaRepository->softDelete();
            DB::commit();
            $pedidoTransferenciaResource =  new PedidoTransferenciaResource($pedidoTransferencia);
            $responseResource = new ResponseResource(null);
            
            $responseResource->title('Pedido de transferencia aceptado por el jefe de almacen destino exitosamente.');       
            $responseResource->body($pedidoTransferenciaResource);     
            
            return $responseResource;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }
    public function obtenerHistorialPedidosTransferencia($idAlmacen) {
        try{
            $responseResource = new ResponseResource(null);
            $filter = strtolower(Input::get('estado'));
            
            if ($filter){
                
                switch ($filter) {
                    case 'en_transito':
                                     
                        $pedidosTransferencia = $this->pedidoTransferenciaRepository->buscarPorFiltroPorTransferenciaPorAlmacen($idAlmacen,'estado', 'en transito');
                        foreach ($pedidosTransferencia as $key => $pt) {
                            $this->pedidoTransferenciaRepository->loadTransferenciaRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pt);
                            $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship($pt);
                            
                        }
                        $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia);
                        $responseResource->title('Lista de pedidos de transferencia filtrados por estado');       
                        $responseResource->body($pedidosTransferenciaResource);
                        break;
    
                    case 'aceptado':
                        $pedidosTransferencia = $this->pedidoTransferenciaRepository->buscarPorFiltroPorTransferenciaPorAlmacen($idAlmacen,'estado', 'aceptado');
                        foreach ($pedidosTransferencia as $key => $pt) {
                            $this->pedidoTransferenciaRepository->loadTransferenciaRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pt);
                            $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship($pt);
                            
                        }
                        $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia);
                        $responseResource->title('Lista de pedidos de transferencia filtrados por estado');       
                        $responseResource->body($pedidosTransferenciaResource);
                        break;
                    
    
                    case 'realizado':
                        $pedidosTransferencia = $this->pedidoTransferenciaRepository->buscarPorFiltroPorTransferenciaPorAlmacen($idAlmacen,'estado', 'realizado');
                        foreach ($pedidosTransferencia as $key => $pt) {
                            $this->pedidoTransferenciaRepository->loadTransferenciaRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pt);
                            $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship($pt);
                            
                        }
                        $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia);
                        $responseResource->title('Lista de pedidos de transferencia filtrados por estado');       
                        $responseResource->body($pedidosTransferenciaResource);
                        break;
                    case 'denegado':
                        $pedidosTransferencia = $this->pedidoTransferenciaRepository->buscarPorFiltroPorTransferenciaPorAlmacen($idAlmacen,'estado', 'denegado');
                        foreach ($pedidosTransferencia as $key => $pt) {
                            $this->pedidoTransferenciaRepository->loadTransferenciaRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pt);
                            $this->pedidoTransferenciaRepository->loadLineasPedidoTransferenciaRelationship($pt);
                            
                        }
                        $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia);
                        $responseResource->title('Lista de pedidos de transferencia filtrados por estado');       
                        $responseResource->body($pedidosTransferenciaResource);
                        break;
                    case 'cancelado':
                        $pedidosTransferencia = $this->pedidoTransferenciaRepository->buscarPorFiltroPorTransferenciaPorAlmacen($idAlmacen,'estado', 'cancelado');
                        foreach ($pedidosTransferencia as $key => $pt) {
                            $this->pedidoTransferenciaRepository->loadTransferenciaRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pt);
                            $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pt);
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

            $pedidosTransferencia = $this->pedidoTransferenciaRepository->obtenerTodosPorAlmacen($idAlmacen);
           
            foreach ($pedidosTransferencia as $key => $pt) {
                $this->pedidoTransferenciaRepository->loadTransferenciaRelationship($pt);
                $this->pedidoTransferenciaRepository->loadAlmacenOrigenRelationship($pt);
                $this->pedidoTransferenciaRepository->loadAlmacenDestinoRelationship($pt);
                $this->pedidoTransferenciaRepository->loadAlmacenDestino2Relationship($pt);
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
}





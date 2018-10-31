<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PedidoTransferencia;
use App\Models\Usuario;
use App\Repositories\PedidoTransferenciaRepository;
use App\Repositories\UsuarioRepository;
use App\Services\AlmacenService;
use App\Http\Controllers\Controller;
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

    public function __construct(PedidoTransferenciaRepository $pedidoTransferenciaRepository){
        PedidoTransferenciaResource::withoutWrapping();
        $this->pedidoTransferenciaRepository = $pedidoTransferenciaRepository;
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
                            
                        }
                        $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia);
                        $responseResource->title('Lista de pedidos de transferencia filtrados por estado');       
                        $responseResource->body($pedidosTransferenciaResource);
                        break;
    
                    case 'aceptado':
                        $pedidosTransferencia = $this->pedidoTransferenciaRepository->buscarPorFiltroPorTransferencia('estado', 'aceptado');
                        foreach ($pedidosTransferencia as $key => $pt) {
                            $this->pedidoTransferenciaRepository->loadTransferenciaRelationship($pt);
                            
                        }
                        $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia);
                        $responseResource->title('Lista de pedidos de transferencia filtrados por estado');       
                        $responseResource->body($pedidosTransferenciaResource);
                        break;
                    
    
                    case 'realizado':
                        $pedidosTransferencia = $this->pedidoTransferenciaRepository->buscarPorFiltroPorTransferencia('estado', 'realizado');
                        foreach ($pedidosTransferencia as $key => $pt) {
                            $this->pedidoTransferenciaRepository->loadTransferenciaRelationship($pt);
                            
                        }
                        $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia);
                        $responseResource->title('Lista de pedidos de transferencia filtrados por estado');       
                        $responseResource->body($pedidosTransferenciaResource);
                        break;
                    case 'denegado':
                        $pedidosTransferencia = $this->pedidoTransferenciaRepository->buscarPorFiltroPorTransferencia('estado', 'denegado');
                        foreach ($pedidosTransferencia as $key => $pt) {
                            $this->pedidoTransferenciaRepository->loadTransferenciaRelationship($pt);
                            
                        }
                        $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia);
                        $responseResource->title('Lista de pedidos de transferencia filtrados por estado');       
                        $responseResource->body($pedidosTransferenciaResource);
                        break;
                    case 'cancelado':
                        $pedidosTransferencia = $this->pedidoTransferenciaRepository->buscarPorFiltroPorTransferencia('estado', 'cancelado');
                        foreach ($pedidosTransferencia as $key => $pt) {
                            $this->pedidoTransferenciaRepository->loadTransferenciaRelationship($pt);
                            
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
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar pedido de transferencia');  
            $responseResourse->body($pedidoTransferenciaResource);
            return $responseResourse;
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

            //comparing_dates
            
          
            
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
            $responseResourse = new ResponseResource(null);
            
            $responseResourse->title('Pedido de transferencia actualizado exitosamente');       
            $responseResourse->body($pedidoTransferenciaResource);     
            
            return $responseResourse;
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
            

              
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Pedido de transferencia eliminado');  
            $responseResourse->body(['id' => $id]);
            DB::commit();
            return $responseResourse;
        }
        catch(\Exception $e){
         
            DB::rollback();
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

       
    }

    public function verPedidosTransferenciaRecibidos($idAlmacenD){
        {
            try{
                $pedidosTransferencia = $this->pedidoTransferenciaRepository->obtenerPedidosTransferenciaPorAlmacenD($idAlmacenD);
             
                
                if (!$pedidosTransferencia){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('Este almacen no tiene Pedidos de Transferencia ');
                    $notFoundResource->notFound(['id' => $idAlmacenD]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
    
                              
                $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia); 
                $responseResourse = new ResponseResource(null);
                $responseResourse->title('Listado de Pedidos de Transferencia recibidos');  
                $responseResourse->body($pedidosTransferenciaResource);
                return $responseResourse;
            }
            catch(\Exception $e){
             
                
                return (new ExceptionResource($e))->response()->setStatusCode(500);
                
            }
    
        }
    }


 
}

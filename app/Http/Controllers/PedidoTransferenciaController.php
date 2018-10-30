<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PedidoTransferencia;
use App\Models\Usuario;
use App\Repositories\PedidoTransferenciaRepository;
use App\Repositories\UsuarioRepository;
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

    public function store(Request $pedidoTransferenciaData) 
    {
        
        try{
            
            $validator = \Validator::make($pedidoTransferenciaData->all(), 
                            ['idUsuario' => 'required',
                            'idAlmacenO' => 'required',
                            'idAlmacenD'=>'required']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            DB::beginTransaction();
            $pedidoTransferencia = $this->pedidoTransferenciaRepository->guarda($pedidoTransferenciaData->all());
            DB::commit();
            $pedidoTransferenciaResource =  new PedidoTransferenciaResource($pedidoTransferencia);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Pedido de transferencia creado exitosamente');       
            $responseResourse->body($pedidoTransferenciaResource);       
            return $responseResourse;
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

    


 
}

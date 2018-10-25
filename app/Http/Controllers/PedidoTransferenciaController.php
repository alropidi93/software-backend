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
            $pedidosTransferencia = $this->pedidoTransferenciaRepository->obtenerTodos();
            
            $pedidosTransferenciaResource =  new PedidosTransferenciaResource($pedidosTransferencia);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de pedidos de transferencia');  
            $responseResourse->body($pedidosTransferenciaResource);
            return $responseResourse;
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

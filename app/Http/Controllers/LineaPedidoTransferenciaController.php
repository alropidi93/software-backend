<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LineaPedidoTransferencia;
use App\Models\Usuario;
use App\Repositories\LineaPedidoTransferenciaRepository;
use App\Repositories\UsuarioRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\LineaPedidoTransferenciaResource;
use App\Http\Resources\LineasPedidoTransferenciaResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

class LineaPedidoTransferenciaController extends Controller {

    protected $lineaPedidoTransferenciaRepository;

    public function __construct(LineaPedidoTransferenciaRepository $lineaPedidoTransferenciaRepository){
        LineaPedidoTransferenciaResource::withoutWrapping();
        $this->lineaPedidoTransferenciaRepository = $lineaPedidoTransferenciaRepository;
    }

    public function index() 
    {
        try{
            $lineasPedidoTransferencia = $this->lineaPedidoTransferenciaRepository->obtenerTodos();
            
            $lineasPedidoTransferenciaResource =  new lineasPedidoTransferenciaResource($lineasPedidoTransferencia);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de lineas de pedido de transferencia');  
            $responseResourse->body($lineasPedidoTransferenciaResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

       
    }
  
    public function show($id) 
    {
        try{
            $lineaPedidoTransferencia = $this->lineaPedidoTransferenciaRepository->obtenerPorId($id);
            
            if (!$lineaPedidoTransferencia){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Linea de Pedido de transferencia no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->lineaPedidoTransferenciaRepository->setModel($lineaPedidoTransferencia);
           
            $lineaPedidoTransferenciaResource =  new LineaPedidoTransferenciaResource($lineaPedidoTransferencia);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar linea de pedido de transferencia');  
            $responseResourse->body($lineaPedidoTransferenciaResource);
            return $responseResourse;
        }
        catch(\Exception $e){
            
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }

    public function store(Request $lineaPedidoTransferenciaData) 
    {
        
        try{
            
            $validator = \Validator::make($lineaPedidoTransferenciaData->all(), 
                            ['idProducto' => 'required',
                            'cantidad' => 'required']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            DB::beginTransaction();
            $lineaPedidoTransferencia = $this->lineaPedidoTransferenciaRepository->guarda($lineaPedidoTransferenciaData->all());
            DB::commit();
            $lineaPedidoTransferenciaResource =  new LineaPedidoTransferenciaResource($lineaPedidoTransferencia);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Linea de Pedido de transferencia creada exitosamente');       
            $responseResourse->body($lineaPedidoTransferenciaResource);       
            return $responseResourse;
        }
        catch(\Exception $e){
            DB::rollback();
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
        
    }

    public function update($id,Request $lineaPedidoTransferenciaData) 
    {
        
        try{
            DB::beginTransaction();
            $lineaPedidoTransferencia = $this->lineaPedidoTransferenciaRepository->obtenerPorId($id);
            
            if (!$lineaPedidoTransferencia){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Linea de Pedido de transferencia no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            

            
            
            $this->lineaPedidoTransferenciaRepository->setModel($lineaPedidoTransferencia);
            $lineaPedidoTransferenciaDataArray= Algorithm::quitNullValuesFromArray($lineaPedidoTransferenciaData->all());
            $this->lineaPedidoTransferenciaRepository->actualiza($pedidoTransferenciaDataArray);
            $lineaPedidoTransferencia = $this->lineaPedidoTransferenciaRepository->obtenerModelo();
            
            DB::commit();
            $lineaPedidoTransferenciaResource =  new LineaPedidoTransferenciaResource($lineaPedidoTransferencia);
            $responseResourse = new ResponseResource(null);
            
            $responseResourse->title('Linea de Pedido de transferencia actualizada exitosamente');       
            $responseResourse->body($lineaPedidoTransferenciaResource);     
            
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
            $lineaPedidoTransferencia = $this->lineaPedidoTransferenciaRepository->obtenerPorId($id);
            
            if (!$lineaPedidoTransferencia){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Linea de Pedido de transferencia no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->lineaPedidoTransferenciaRepository->setModel($lineaPedidoTransferencia);
            $this->lineaPedidoTransferenciaRepository->softDelete();
            

              
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Linea de Pedido de transferencia eliminado');  
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
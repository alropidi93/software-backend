<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transferencia;
use App\Models\Usuario;
use App\Repositories\TransferenciaRepository;
use App\Repositories\UsuarioRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransferenciaResource;
use App\Http\Resources\TransferenciasResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

class TransferenciaController extends Controller {

    protected $transferenciaRepository;

    public function __construct(TransferenciaRepository $transferenciaRepository){
        TransferenciaResource::withoutWrapping();
        $this->transferenciaRepository = $transferenciaRepository;
    }

    public function index() 
    {
        try{
            $transferencias = $this->transferenciaRepository->obtenerTodos();
            
            $transferenciasResource =  new TransferenciasResource($transferencias);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de transferencias');  
            $responseResourse->body($transferenciasResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

       
    }
  
    public function show($id) 
    {
        try{
            $transferencia = $this->transferenciaRepository->obtenerPorId($id);
            
            if (!$transferencia){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Transferencia no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->transferenciaRepository->setModel($transferencia);
           
            $transferenciaResource =  new TransferenciaResource($transferencia);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar transferencia');  
            $responseResourse->body($transferenciaResource);
            return $responseResourse;
        }
        catch(\Exception $e){
            
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }

    public function store(Request $transferenciaData) 
    {
        
        try{
            
            $validator = \Validator::make($transferenciaData->all(), 
                            ['idPedidoTransferencia' => 'required',
                            'estado' => 'required']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            DB::beginTransaction();
            $transferencia = $this->transferenciaRepository->guarda($transferenciaData->all());
            DB::commit();
            $transferenciaResource =  new TransferenciaResource($transferencia);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Transferencia creada exitosamente');       
            $responseResourse->body($transferenciaResource);       
            return $responseResourse;
        }
        catch(\Exception $e){
            DB::rollback();
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
        
    }

    public function update($id,Request $transferenciaData) 
    {
        
        try{
            DB::beginTransaction();
            $transferencia = $this->transferenciaRepository->obtenerPorId($id);
            
            if (!$transferencia){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Transferencia no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            

            
            
            $this->transferenciaRepository->setModel($transferencia);
            $transferenciaDataArray= Algorithm::quitNullValuesFromArray($transferenciaData->all());
            $this->transferenciaRepository->actualiza($transferenciaDataArray);
            $transferencia = $this->transferenciaRepository->obtenerModelo();
            
            DB::commit();
            $transferenciaResource =  new TransferenciaResource($transferencia);
            $responseResourse = new ResponseResource(null);
            
            $responseResourse->title('Transferencia actualizada exitosamente');       
            $responseResourse->body($transferenciaResource);     
            
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
            $transferencia = $this->transferenciaRepository->obtenerPorId($id);
            
            if (!$transferencia){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Transferencia no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->transferenciaRepository->setModel($transferencia);
            $this->transferenciaRepository->softDelete();
            

              
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Transferencia eliminada');  
            $responseResourse->body(['id' => $id]);
            DB::commit();
            return $responseResourse;
        }
        catch(\Exception $e){
         
            DB::rollback();
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

       
    }

    public function listarEstados() 
    {
        try{
            $estados_transferencia=['En transito', 'Aceptado','Realizado','Denegado','Cancelado'];
            
           
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de estados de transferencias');  
            $responseResourse->body($estados_transferencia);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

       
    }

    


 
}

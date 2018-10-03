<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tienda;
use App\Repositories\TiendaRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\TiendaResource;
use App\Http\Resources\TiendasResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;

class TiendaController extends Controller {

    protected $tiendaRepository;

    public function __construct(TiendaRepository $tiendaRepository){
        TiendaResource::withoutWrapping();
        $this->tiendaRepository = $tiendaRepository;
    }

    public function index() 
    {
        try{
            $tiendasResource =  new TiendasResource($this->tiendaRepository->obtenerTodos());  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de tiendas');  
            $responseResourse->body($tiendasResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

       
    }
  
    public function show($id) 
    {
        try{
            $tienda = $this->tiendaRepository->obtenerPorId($id);
            
            if (!$tienda){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Tienda no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $tiendaResource =  new TiendaResource($tienda);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar tienda');  
            $responseResourse->body($tiendaResource);
            return $responseResourse;
        }
        catch(\Exception $e){
            
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }

    public function store(Request $tiendaData) //Tienda $tienda
    {
        
        try{
            
            $validator = \Validator::make($tiendaData->all(), 
                            ['nombre' => 'required',
                            'distrito' => 'required',
                            'ubicacion'=>'required', 
                            'direccion' => 'required',
                            'telefono' => 'required']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            DB::beginTransaction();
            $tienda = $this->tiendaRepository->guarda($tiendaData->all());
            DB::commit();
            $tiendaResource =  new TiendaResource($tienda);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Tienda creada exitosamente');       
            $responseResourse->body($tiendaResource);       
            return $responseResourse;
        }
        catch(\Exception $e){
            DB::rollback();
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
        
    }

    public function update($id,Request $tiendaData) 
    {
        
        try{
            DB::beginTransaction();
            $tienda = $this->tiendaRepository->obtenerPorId($id);
            
            if (!$tienda){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Tienda no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            

            
            
            $this->tiendaRepository->setModel($tienda);
            $tiendaDataArray= Algorithm::quitNullValuesFromArray($tiendaData->all());
            $this->tiendaRepository->actualiza($tiendaDataArray);
            $tienda = $this->tiendaRepository->obtenerModelo();
            
            DB::commit();
            $tiendaResource =  new TiendaResource($tienda);
            $responseResourse = new ResponseResource(null);
            
            $responseResourse->title('Tienda actualizada exitosamente');       
            $responseResourse->body($tiendaResource);     
            
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
            $tienda = $this->tiendaRepository->obtenerPorId($id);
            
            if (!$tienda){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Tienda no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->tiendaRepository->setModel($tienda);
            $this->tiendaRepository->softDelete();
            

              
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Tienda eliminada');  
            $responseResourse->body(['id' => $id]);
            DB::commit();
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

       
    }

  



 
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Almacen;
use App\Models\Usuario;
use App\Repositories\AlmacenRepository;
use App\Repositories\UsuarioRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\AlmacenResource;
use App\Http\Resources\AlmacenesResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

class AlmacenController extends Controller {

    protected $almacenRepository;

    public function __construct(AlmacenRepository $almacenRepository){
        AlmacenResource::withoutWrapping();
        $this->almacenRepository = $almacenRepository;
    }

    public function index() 
    {
        try{
            $almacenes = $this->almacenRepository->obtenerTodos();
            $almacenesResource =  new AlmacenesResource($almacenes);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de almacenes');  
            $responseResourse->body($almacenesResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

       
    }
  
    public function show($id) 
    {
        try{
            $almacen = $this->almacenRepository->obtenerPorId($id);
            
            if (!$almacen){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Almacen no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->almacenRepository->setModel($almacen);
            $almacenResource =  new AlmacenResource($almacen);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar Almacen');  
            $responseResourse->body($almacenResource);
            return $responseResourse;
        }
        catch(\Exception $e){
            
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }

   // Los almacenes se crean automáticamente con cada tienda, no tendrán STORE ni UPDATE

    

    public function destroy($id) 
    {
              
 
        try{
            DB::beginTransaction();
            $almacen = $this->almacenRepository->obtenerPorId($id);
            
            if (!$almacen){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Almacen no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->almacenRepository->setModel($almacen);
            $this->almacenRepository->softDelete();
            

              
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Almacen eliminado');  
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

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

    public function cargarProductosStock(Request $data){
        set_time_limit ( 1000 );
        try{
     
            $num_almacenes =  $this->almacenRepository->cantidadElementos();
      
            if ($num_almacenes==0){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Almacenes no encontrados');
                $notFoundResource->notFound(['num_almacenes'=>$num_almacenes]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            DB::beginTransaction();


            $almacenes = $this->almacenRepository->obtenerTodos();
            foreach ($almacenes as $key => $almacen) {
                $this->almacenRepository->setModel($almacen);
                $productosNoStockeadosTipoStock1 = $this->almacenRepository->getProductosNoStockedosByOwnModelAndKeyTipoStock(1);
            
                foreach ($productosNoStockeadosTipoStock1 as $key => $producto) {
                    $this->almacenRepository->attachProductoStockRndByTipoStock($producto,1);
                }
               

                $productosNoStockeadosTipoStock2 = $this->almacenRepository->getProductosNoStockedosByOwnModelAndKeyTipoStock(2);
                foreach ($productosNoStockeadosTipoStock2 as $key => $producto) {
                    $this->almacenRepository->attachProductoStockRndByTipoStock($producto,2);
                }

                $productosNoStockeadosTipoStock3 = $this->almacenRepository->getProductosNoStockedosByOwnModelAndKeyTipoStock(3);
                foreach ($productosNoStockeadosTipoStock3 as $key => $producto) {
                    $this->almacenRepository->attachProductoStockRndByTipoStock($producto,3);
                }
                $this->almacenRepository->loadProductosRelationship();
                
               
            }
            DB::commit();
            
       
            
           

            
            $almacenesResource =  new AlmacenesResource($almacenes);
            $responseResource = new ResponseResource(null);
            $responseResource->title('Stock de productos en los almacenes, generados correctamente');  
            $responseResource->body($almacenesResource);
            
            return $responseResource;
        }
        catch(\Exception $e){
         
            DB::rollback();
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }
 
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Repositories\ProductoRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Http\Resources\ProductosResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;

class ProductoController extends Controller
{
    public function __construct(ProductoRepository $productoRepository){
        ProductoResource::withoutWrapping();
        $this->productoRepository = $productoRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $productoResource =  new ProductoResource($this->productoRepository->obtenerTodos());  
            $responseResource = new ResponseResource(null);
            $responseResource->title('Lista de productos');  
            $responseResource->body($productoResource);
            return $responseResource;
        }
        catch(\Exception $e){
         
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $productoData)
    {
        try{
            
            $validator = \Validator::make($productoData->all(), 
                            ['nombre' => 'required',
                            'stockMin' => 'required',
                            'descripcion'=>'required',
                            'categoria' => 'required',
                            'precio' => 'required']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            DB::beginTransaction();
            
            $producto = $this->productoRepository->guarda($productoData->all());
            DB::commit();
            $productoResource =  new ProductoResource($producto);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Producto creada exitosamente');       
            $responseResourse->body($productoResource);       
            return $responseResourse;
        }
        catch(\Exception $e){
            DB::rollback();
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $producto = $this->productoRepository->obtenerPorId($id);
            
            if (!$producto){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Producto no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $productoResource =  new ProductoResource($producto);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar producto');  
            $responseResourse->body($productoResource);
            return $responseResourse;
        }
        catch(\Exception $e){
            
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $productoData)
    {
        try{
            DB::beginTransaction();
            $producto = $this->productoRepository->obtenerPorId($id);
            
            if (!$producto){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Producto no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            

            
            
            $this->productoRepository->setModel($producto);
            $productoDataArray= Algorithm::quitNullValuesFromArray($productoData->all());
            $this->productoRepository->actualiza($productoDataArray);
            $producto = $this->productoRepository->obtenerModelo();
            
            DB::commit();
            $productoRepository =  new ProductoResource($producto);
            $responseResourse = new ResponseResource(null);
            
            $responseResourse->title('Producto actualizada exitosamente');       
            $responseResourse->body($productoRepository);     
            
            return $responseResourse;
        }
        catch(\Exception $e){
            DB::rollback();
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            DB::beginTransaction();
            $producto = $this->productoRepository->obtenerPorId($id);
            
            if (!$producto){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Producto no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->productoRepository->setModel($producto);
            $this->productoRepository->softDelete();
            

              
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Producto eliminada');  
            $responseResourse->body(['id' => $id]);
            DB::commit();
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }
}

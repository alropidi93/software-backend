<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Repositories\ProductoRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Http\Resources\ProductosResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

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
            $productos = $this->productoRepository->obtenerTodos();
            
            foreach ($productos as $key => $producto) {
                $this->productoRepository->loadTipoProductoModel($producto);
                
                
            }

            $productosResource =  new ProductosResource($productos);  
            $responseResource = new ResponseResource(null);
            $responseResource->title('Lista de productos');  
            $responseResource->body($productosResource);
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
            $responseResource = new ResponseResource(null);
            $responseResource->title('Producto creada exitosamente');       
            $responseResource->body($productoResource);       
            return $responseResource;
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
            $this->productoRepository->loadTipoProductoModel($producto);
            $productoResource =  new ProductoResource($producto);  
            $responseResource = new ResponseResource(null);
            $responseResource->title('Mostrar producto');  
            $responseResource->body($productoResource);
            return $responseResource;
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
            $validator = \Validator::make($productoData->all(), 
                            ['idTipoProducto' => 'exists:tipoProducto,id']
                        );
            
            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            DB::beginTransaction();
            $producto = $this->productoRepository->obtenerPorId($id);
            
            if (!$producto){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Producto no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            

            
            
            $this->productoRepository->setModel($producto);
            $productoDataArray= Algorithm::quitNullValuesFromArray($productoData->all());
            $this->productoRepository->actualiza($productoDataArray);
            $this->productoRepository->loadTipoProductoModel();
            $producto = $this->productoRepository->obtenerModelo();
            
            DB::commit();
            $productoResource =  new ProductoResource($producto);
            $responseResource = new ResponseResource(null);
            
            $responseResource->title('Producto actualizado exitosamente');       
            $responseResource->body($productoResource);     
            
            return $responseResource;
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
            

              
            $responseResource = new ResponseResource(null);
            $responseResource->title('Producto eliminada');  
            $responseResource->body(['id' => $id]);
            DB::commit();
            return $responseResource;
        }
        catch(\Exception $e){
         
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }

    public function busquedaPorFiltro()
    {
        try{
            $producto = $this->productoRepository->obtenerModelo();
            $filter = strtolower(Input::get('filterBy'));
            $value = strtolower(Input::get('value'));
            $responseResource = new ResponseResource(null);
            if (!$filter || !$value){
                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de búsqueda');
                $errorResource->message('Parámetros inválidos para la búsqueda');
                return $errorResource->response()->setStatusCode(400);

            }
          
            switch ($filter) {
                case 'nombre':
                                  
                    $productos = $this->productoRepository->buscarPorFiltro($filter, $value);
                    return $productos;  
                    $productosResource =  new ProductosResource($productos);
                    $responseResource->title('Lista de productos filtrados por nombre');       
                    $responseResource->body($productosResource);
                    break;

                case 'categoria':
                    $productos = $this->productoRepository->buscarPorFiltro($filter, $value);
                    $productosResource =  new ProductosResource($productos);
                    $responseResource->title('Lista de productos filtrados por categoria');       
                    $responseResource->body($productosResource);
                    break;

                default:
                    $errorResource = new ErrorResource(null);
                    $errorResource->title('Error de búsqueda');
                    $errorResource->message('Valor de filtro inválido');
                    return $errorResource->response()->setStatusCode(400);
                    
            }
            
            return $responseResource; 
        }
        catch(\Exception $e){
                  
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    
    }
}

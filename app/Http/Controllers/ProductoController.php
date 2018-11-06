<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Repositories\ProductoRepository;
use App\Repositories\CategoriaRepository;
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
    protected $productoRepository;
    protected $categoriaRepository;

    public function __construct(ProductoRepository $productoRepository, CategoriaRepository $categoriaRepository){
        ProductoResource::withoutWrapping();
        $this->productoRepository = $productoRepository;
        $this->categoriaRepository = $categoriaRepository;
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
                $this->productoRepository->loadTipoProductoRelationship($producto);
                $this->productoRepository->loadUnidadMedidaRelationship($producto);
                $this->productoRepository->loadProveedoresRelationship($producto);
                $this->productoRepository->loadCategoriaRelationship($producto);
                
                
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
                            'idCategoria' => 'required',
                            'precio' => 'required']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
          
            //$producto = $this->productoRepository->obtenerPorId(9);
            // $producto->unidadMedida;
            // $producto->categoria;
            // $producto->tipoProducto;
            //return $producto;

            $idCategoria= $productoData['idCategoria'];
            $categoria = $this->categoriaRepository->obtenerPorId($idCategoria);
            
            if (!$categoria){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Categoria no encontrada');
                $notFoundResource->notFound(['idCategoria'=>$idCategoria]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            DB::beginTransaction();
            
            $producto = $this->productoRepository->guarda($productoData->all());
            // $producto->unidadMedida;
            // $producto->categoria;
            // $producto->tipoProducto;
            // return $producto;

            DB::commit();
            $this->productoRepository->setModel($producto);
            $this->productoRepository->loadCategoriaRelationship();
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
            $this->productoRepository->loadTipoProductoRelationship($producto);
            $this->productoRepository->loadUnidadMedidaRelationship($producto);
            $this->productoRepository->loadProveedoresRelationship($producto);
            $this->productoRepository->loadCategoriaRelationship($producto);
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
    
            $productoDataArray= Algorithm::quitNullValuesFromArray($productoData->all());
            
            $validator = \Validator::make($productoDataArray, 
                            ['idTipoProducto' => 'exists:tipoProducto,id']
                        );
            
            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
           
            
         
            if (array_key_exists('idCategoria',$productoDataArray)){
                
                $idCategoria = $productoDataArray['idCategoria'];
                $categoria = $this->categoriaRepository->obtenerPorId($idCategoria);
            
                if (!$categoria){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('Categoria no encontrada');
                    $notFoundResource->notFound(['idCategoria'=>$idCategoria]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
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
            
            $this->productoRepository->actualiza($productoDataArray);
            $this->productoRepository->loadTipoProductoRelationship();
            $this->productoRepository->loadUnidadMedidaRelationship();
            $this->productoRepository->loadCategoriaRelationship();
            $producto = $this->productoRepository->obtenerModelo();
            
            DB::commit();
         
            //$producto->categoria;
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
            $filter = Input::get('filterBy');
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
                    foreach ($productos as $key => $producto) {
                        $this->productoRepository->loadTipoProductoRelationship($producto);
                        $this->productoRepository->loadUnidadMedidaRelationship($producto);
                        $this->productoRepository->loadCategoriaRelationship($producto);
                    }
                    $productosResource =  new ProductosResource($productos);
                    $responseResource->title('Lista de productos filtrados por nombre');       
                    $responseResource->body($productosResource);
                    break;

                case 'categoria':
                    $productos = $this->productoRepository->buscarPorCategoria($value);
                    foreach ($productos as $key => $producto) {
                        $this->productoRepository->loadTipoProductoRelationship($producto);
                        $this->productoRepository->loadUnidadMedidaRelationship($producto);
                        $this->productoRepository->loadCategoriaRelationship($producto);
                    }
                    $productosResource =  new ProductosResource($productos);
                    $responseResource->title('Lista de productos filtrados por categoria');       
                    $responseResource->body($productosResource);
                    break;
                

                case 'tipo':
                    $productos = $this->productoRepository->buscarPorTipo($value);
                    foreach ($productos as $key => $producto) {
                        $this->productoRepository->loadTipoProductoRelationship($producto);
                        $this->productoRepository->loadUnidadMedidaRelationship($producto);
                        $this->productoRepository->loadCategoriaRelationship($producto);
                    }
                    $productosResource =  new ProductosResource($productos);
                    $responseResource->title('Lista de productos filtrados por categoria');       
                    $responseResource->body($productosResource);
                    break;
                case 'stockMin':
                    $productos = $this->productoRepository->buscarPorFiltroNum($filter,$value);
                    foreach ($productos as $key => $producto) {
                        $this->productoRepository->loadTipoProductoRelationship($producto);
                        $this->productoRepository->loadUnidadMedidaRelationship($producto);
                        $this->productoRepository->loadCategoriaRelationship($producto);
                    }
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

    public function listarConStock(){

        try{
            set_time_limit(1000);
            $productos =$this->productoRepository->listarConStock();
            $productosResource =  new ProductosResource($productos); 
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Listado de productos con información de stock');  
            $responseResourse->body($productosResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
        
    }

    public function listarConStockMinimo(){

        try{
            set_time_limit(1000);
           
            $productos =$this->productoRepository->listarConStockMinimo();
            $productosResource =  new ProductosResource($productos); 
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Listado de productos cuyo stock principal es menor o igual a su stock mínimo');  
            $responseResourse->body($productosResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
        
    }


    public function asignarProveedor($idProducto, Request $data){
        try{
           
            DB::beginTransaction();
            $producto = $this->productoRepository->obtenerPorId($idProducto);
         
            $idProveedor = $data['idProveedor'];
            if (!$producto){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Producto no encontrado');
                $notFoundResource->notFound(['id' => $idProducto]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            
            $proveedor =  $this->productoRepository->obtenerProveedorPorId($idProveedor);
            
            if (!$proveedor){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Proveedor no encontrado');
                $notFoundResource->notFound(['idProveedor' => $idProveedor]);
                return $notFoundResource->response()->setStatusCode(404);
            }

            
            
           
            $this->productoRepository->setModel($producto);
             
            $this->productoRepository->setProveedorModel($proveedor);
            if ($this->productoRepository->checkProductoProveedorOwnModelsRelationship()){
                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de integridad');
                $errorResource->message('Proveedor ya fue asignado a producto');
                return $errorResource->response()->setStatusCode(400);
            }
            
            $this->productoRepository->attachProveedor($proveedor);
                  
            DB::commit();
            $this->productoRepository->loadProveedoresRelationship();
            $producto =  $this->productoRepository->obtenerModelo();
          
            $productoResource =  new ProductoResource($producto);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Proveedor agregado a producto como uno de sus proveedores');  
            $responseResourse->body($productoResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

    }

    public function modificarStock($idProducto, Request $data){
        // $data con tiene: idTipoStock, idAlmacen, idProducto y cantidad
        try{
            DB::beginTransaction();
            $idTipoStock = $data['idTipoStock'];
            $idAlmacen = $data['idAlmacen'];
            #$idProducto = $data['idProducto'];
            $cantidad = $data['cantidad'];

            $producto = $this->productoRepository->obtenerPorId($idProducto);
            $this->productoRepository->setModel($producto);
            return $this->productoRepository->updateStock( $idTipoStock, $idAlmacen, $cantidad);

            DB::commit();
            
            #$productoResource =  new ProductoResource($producto);  
            $responseResource = new ResponseResource(null);
            $responseResource->title('Stock del producto modificado correctamente');  
            $responseResource->body('body test');
            return $responseResource;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }
}

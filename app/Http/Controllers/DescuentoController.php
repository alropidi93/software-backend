<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Descuento;
use App\Models\Tienda;
use App\Models\Producto;
use App\Models\Categoria;
use App\Repositories\DescuentoRepository;
use App\Repositories\TiendaRepository;
use App\Repositories\ProductoRepository;
use App\Repositories\CategoriaRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\DescuentoResource;
use App\Http\Resources\DescuentosResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Collection;

class DescuentoController extends Controller
{   
    protected $descuentoRepository;
    protected $tiendaRepository;
    protected $productoRepository;
    protected $categoriaRepository;
  
    public function __construct(DescuentoRepository $descuentoRepository=null, TiendaRepository $tiendaRepository=null, ProductoRepository $productoRepository=null, CategoriaRepository $categoriaRepository=null){
        DescuentoResource::withoutWrapping();
        $this->descuentoRepository = $descuentoRepository;
        $this->tiendaRepository = $tiendaRepository;
        $this->productoRepository = $productoRepository;
        $this->categoriaRepository = $categoriaRepository;
        }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        try{
            $descuentos = $this->descuentoRepository->obtenerTodos();
            foreach ($descuentos as $key => $descuento) {
              $this->descuentoRepository->loadTiendaRelationship($descuento);
              $this->descuentoRepository->loadProductoRelationship($descuento);
              $this->descuentoRepository->loadCategoriaRelationship($descuento);
            }
            $descuentosResource =  new DescuentosResource($descuentos);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de descuentos');  
            $responseResourse->body($descuentosResource);
            return $responseResourse;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }
    
    //not using store because it's easier to make 2 separately
    public function store(Request $descuentoData){
        try{
            $validator = \Validator::make($descuentoData->all(), 
                            ['fechaIni' => 'required',
                            'fechaFin'=>  'required',
                            'idTienda'=>  'required',
                            ]);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }

            //validaciones
            if(true){
                //verificar que existe la tienda
                $tienda = $this->tiendaRepository->obtenerPorId($descuentoData['idTienda']);
                if(!$tienda){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('No existe esta tienda');
                    $notFoundResource->notFound(['id' => $descuentoData['idTienda']]);
                    return $notFoundResource->response()->setStatusCode(404);
                }

                $existeIdProducto = array_key_exists('idProducto', $descuentoData) ? "si":"no";

                //verificar que hay producto o categoria en el request
                //THIS SHIT WONT WORK FOR SOME REASON, IT KEEPS RETURNING NULL EVEN THO I'M VALIDATING IT
                // $idProducto = array_key_exists('idProducto', $descuentoData)? $descuentoData['idProducto']:null;
                $idProducto = $descuentoData['idProducto'];  //idk why but the line above doesnt work ???
                // return $descuentoData['idCategoria'];
                $idCategoria = $descuentoData['idCategoria'];
                $idCategoria = array_key_exists('idCategoria', $descuentoData)? $descuentoData['idCategoria']:null;
                // if(!(array_key_exists('idProducto', $descuentoData) || array_key_exists('idCategoria', $descuentoData))){
                //     $errorResource = new ErrorResource(null);
                //     $errorResource->title('Error de validacion');
                //     $errorResource->message('Debe indicar un producto o categoria.');
                //     return $errorResource->response()->setStatusCode(403);
                // }

                //verificar que existe el producto solo si se mando un id valido
                if($idProducto){
                    $producto = $this->productoRepository->obtenerPorId($descuentoData['idProducto']);
                    if (!$producto){
                        $notFoundResource = new NotFoundResource(null);
                        $notFoundResource->title('No existe este producto');
                        $notFoundResource->notFound(['id' => $descuentoData['idProducto']]);
                        return $notFoundResource->response()->setStatusCode(404);
                    }
                }

                //verificar que existe la categoria solo si se mando un id valido
                if($idCategoria){
                    $categoria = $this->categoriaRepository->obtenerPorId($descuentoData['idCategoria']);
                    if (!$categoria){
                        $notFoundResource = new NotFoundResource(null);
                        $notFoundResource->title('No existe esta categoria');
                        $notFoundResource->notFound(['id' => $descuentoData['idCategoria']]);
                        return $notFoundResource->response()->setStatusCode(404);
                    }
                }

                //verificar que hay 2x1 o porcentaje
                // if(!array_key_exists('es2x1', $descuentoData) && !array_key_exists('porcentaje', $descuentoData)){
                //     $errorResource = new ErrorResource(null);
                //     $errorResource->title('Error de validacion');
                //     $errorResource->message('Debe indicar si es 2x1 o por porcentaje.');
                //     return $errorResource->response()->setStatusCode(403);
                // }
            }

            $es2x1=array_key_exists('es2x1', $descuentoData)? $descuentoData['es2x1']:false;
            $porcentaje=array_key_exists('porcentaje', $descuentoData)? $descuentoData['porcentaje']:0;
            
            DB::beginTransaction();
            // $this->descuentoRepository->setTiendaModel($tienda);
            // if($categoria){
            //     $this->descuentoRepository->setCategoriaModel($categoria);
            // }else if($producto){
            //     $this->descuentoRepository->setProductoModel($producto);
            // }
            $descuento = $this->descuentoRepository->guarda($descuentoData->all());
            // DB::commit();
            $this->descuentoRepository->setModel($descuento);
            $this->descuentoRepository->loadTiendaRelationship();            
            $this->descuentoRepository->loadProductoRelationship();            
            $this->descuentoRepository->loadCategoriaRelationship();            
                                 
            $descuentoResource =  new DescuentoResource($descuento);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Descuento creado exitosamente');       
            $responseResourse->body($descuentoResource);       
            return $responseResourse;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    public function crearDescuentoPorcentualCategoria(Request $descuentoData){
        try{
            $validator = \Validator::make($descuentoData->all(), 
                            ['idCategoria' => 'required',
                            'idTienda'=>  'required',
                            'fechaIni'=>  'required',
                            'fechaFin'=>  'required',
                            'porcentaje'=>  'required',
                            ]);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }

            //validaciones
            if(true){
                //verificar que la tienda existe
                $tienda = $this->tiendaRepository->obtenerPorId($descuentoData['idTienda']);
                if(!$tienda){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('No existe esta tienda');
                    $notFoundResource->notFound(['id' => $descuentoData['idTienda']]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
                //verificar que la categoria existe
                $categoria = $this->categoriaRepository->obtenerPorId($descuentoData['idCategoria']);
                if (!$categoria){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('No existe esta categoria');
                    $notFoundResource->notFound(['id' => $descuentoData['idCategoria']]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
            }

            DB::beginTransaction();
            $descuento = $this->descuentoRepository->guarda($descuentoData->all());
            DB::commit();

            $this->descuentoRepository->setModel($descuento);
            $this->descuentoRepository->loadTiendaRelationship();
            $this->descuentoRepository->loadCategoriaRelationship();
                                 
            $descuentoResource =  new DescuentoResource($descuento);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Descuento porcentual por categoria creado exitosamente');       
            $responseResourse->body($descuentoResource);       
            return $responseResourse;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    public function crearDescuentoPorcentualProducto(Request $descuentoData){
        try{
            $validator = \Validator::make($descuentoData->all(), 
                            ['idProducto' => 'required',
                            'idTienda'=>  'required',
                            'fechaIni'=>  'required',
                            'fechaFin'=>  'required',
                            'porcentaje'=>  'required',
                            ]);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }

            //validaciones
            if(true){
                //verificar que la tienda existe
                $tienda = $this->tiendaRepository->obtenerPorId($descuentoData['idTienda']);
                if(!$tienda){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('No existe esta tienda');
                    $notFoundResource->notFound(['id' => $descuentoData['idTienda']]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
                //verificar que la categoria existe
                $producto = $this->productoRepository->obtenerPorId($descuentoData['idProducto']);
                if (!$producto){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('No existe este producto');
                    $notFoundResource->notFound(['id' => $descuentoData['idProducto']]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
            }

            DB::beginTransaction();
            $descuento = $this->descuentoRepository->guarda($descuentoData->all());
            DB::commit();

            $this->descuentoRepository->setModel($descuento);
            $this->descuentoRepository->loadTiendaRelationship();
            $this->descuentoRepository->loadProductoRelationship();
                                 
            $descuentoResource =  new DescuentoResource($descuento);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Descuento porcentual por producto creado exitosamente');       
            $responseResourse->body($descuentoResource);       
            return $responseResourse;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    public function crearDescuento2x1Producto(Request $descuentoData){
        try{
            $validator = \Validator::make($descuentoData->all(), 
                            ['idProducto' => 'required',
                            'idTienda'=>  'required',
                            'fechaIni'=>  'required',
                            'fechaFin'=>  'required'
                            ]);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }

            //validaciones
            if(true){
                //verificar que la tienda existe
                $tienda = $this->tiendaRepository->obtenerPorId($descuentoData['idTienda']);
                if(!$tienda){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('No existe esta tienda');
                    $notFoundResource->notFound(['id' => $descuentoData['idTienda']]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
                //verificar que la categoria existe
                $producto = $this->productoRepository->obtenerPorId($descuentoData['idProducto']);
                if (!$producto){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('No existe este producto');
                    $notFoundResource->notFound(['id' => $descuentoData['idProducto']]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
            }
            $descuentoData['es2x1'] = true;
            DB::beginTransaction();
            $descuento = $this->descuentoRepository->guarda($descuentoData->all());
            // DB::commit();

            $this->descuentoRepository->setModel($descuento);
            $this->descuentoRepository->loadTiendaRelationship();
            $this->descuentoRepository->loadProductoRelationship();
                                 
            $descuentoResource =  new DescuentoResource($descuento);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Descuento 2x1 para el producto creado exitosamente');       
            $responseResourse->body($descuentoResource);       
            return $responseResourse;
        }catch(\Exception $e){
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
            $descuento = $this->descuentoRepository->obtenerPorId($id);
            if (!$descuento){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Descuento no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }

            $this->descuentoRepository->setModel($descuento);
            $this->descuentoRepository->loadProductoRelationship();
            $this->descuentoRepository->loadCategoriaRelationship();

            $descuentoResource =  new DescuentoResource($descuento);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar descuento');  
            $responseResourse->body($descuentoResource);
            return $responseResourse;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }
    
    public function update($id,Request $descuentoData)
    {
        try{
            $descuentoData= Algorithm::quitNullValuesFromArray($descuentoData->all());             
            DB::beginTransaction();
            $descuento= $this->descuentoRepository->obtenerPorId($id);
            if (!$descuento){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Descuento no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }            
            $this->descuentoRepository->setDescuentoModel($descuento);            
            $this->descuentoRepository->actualiza($descuentoData);            
            $descuento = $this->descuentoRepository->obtenerModelo();
           
            DB::commit();
            $this->descuentoRepository->setModel($descuento);
            $this->descuentoRepository->loadProductoRelationship();
            $this->descuentoRepository->loadCategoriaRelationship();
            $descuentoResource =  new DescuentoResource($descuento);
            $responseResourse = new ResponseResource(null);            
            $responseResourse->title('Descuento actualizado exitosamente');       
            $responseResourse->body($descuentoResource);     
            
            return $responseResourse;            
        }catch(\Exception $e){
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
            $descuento = $this->descuentoRepository->obtenerPorId($id);
            if (!$descuento){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Descuento no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->descuentoRepository->setModel($descuento);
            $this->descuentoRepository->softDelete();
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Descuento eliminado');  
            $responseResourse->body(['id' => $id]);
            DB::commit();

            return $responseResourse;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }
    
}

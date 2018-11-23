<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MovimientoTipoStock;
use App\Models\Usuario;
use App\Models\Producto;
use App\Models\Almacen;
use App\Models\TipoStock;
use App\Repositories\MovimientoTipoStockRepository;
use App\Repositories\UsuarioRepository;
use App\Repositories\ProductoRepository;
use App\Repositories\AlmacenRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\MovimientoTipoStockResource;
use App\Http\Resources\MovimientosTipoStockResource;

use App\Http\Resources\ExceptionResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;

use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

/*
Clase usada para la trazabilidad, solo se puede registrar un movimiento
y listar los movimientos. No se puede editar ni eliminar.
*/
class MovimientoTipoStockController extends Controller
{
    protected $movimientoTipoStockRepository;
    protected $productoRepository;
    protected $almacenRepository;
    protected $tipoStockRepository;
    protected $usuarioRepository;

    public function __construct(MovimientoTipoStockRepository $movimientoTipoStockRepository=null, ProductoRepository $productoRepository=null, AlmacenRepository $almacenRepository=null, UsuarioRepository $usuarioRepository=null){
        MovimientoTipoStockResource::withoutWrapping();
        $this->movimientoTipoStockRepository = $movimientoTipoStockRepository;
        $this->productoRepository = $productoRepository;
        $this->almacenRepository = $almacenRepository;
        $this->usuarioRepository = $usuarioRepository;
    }

    public function index(){
        try{
            $movimientos = $this->movimientoTipoStockRepository->obtenerTodos();
            foreach ($movimientos as $key => $movimiento) {
                $this->movimientoTipoStockRepository->loadUsuarioRelationship($movimiento);
                $usuario = $this->movimientoTipoStockRepository->obtenerUsuarioModel();
                // $this->usuarioRepository->loadTipoUsuarioRelationship($usuario);
                $this->movimientoTipoStockRepository->loadProductoRelationship($movimiento);
                $this->movimientoTipoStockRepository->loadAlmacenRelationship($movimiento);
                $this->movimientoTipoStockRepository->loadTipoStockRelationship($movimiento);
            }

            $movimientoResource =  new MovimientosTipoStockResource($movimientos);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de movimientos de tipo stock');  
            $responseResourse->body($movimientoResource);
            return $responseResourse;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }  
    }

    public function store(Request $movimientoData){
        try{
            $validator = \Validator::make($movimientoData->all(), 
                            ['idProducto' => 'required',
                            'idAlmacen' => 'required',
                            'idTipoStock' => 'required',
                            'idUsuario' => 'required',
                            'cantidad' => 'required',
                            'signo' => 'required']);
            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            $producto = $this->productoRepository->obtenerPorId($movimientoData['idProducto']);
            if (!$producto){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Producto no encontrado');
                $notFoundResource->notFound(['id'=>$movimientoData['idProducto']]);
                return $notFoundResource->response()->setStatusCode(404);;
            }

            $almacen = $this->almacenRepository->obtenerPorId($movimientoData['idAlmacen']);
            if (!$almacen){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Almacen no encontrado');
                $notFoundResource->notFound(['id'=>$movimientoData['idAlmacen']]);
                return $notFoundResource->response()->setStatusCode(404);;
            }

            $usuario =  $this->movimientoTipoStockRepository->getUsuarioById($movimientoData['idUsuario']);
            if (!$usuario){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Usuario no encontrado');
                $notFoundResource->notFound(['id'=>$movimientoData['idUsuario']]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            
            DB::beginTransaction();
            $movimiento = $this->movimientoTipoStockRepository->guarda($movimientoData->all());
            DB::commit();

            // $this->movimientoTipoStockRepository->setUsuarioModel($usuario);
            // $this->movimientoTipoStockRepository->loadUsuarioRelationship($movimiento);
            // $this->movimientoTipoStockRepository->loadProductoRelationship($movimiento);
            // $this->movimientoTipoStockRepository->loadAlmacenRelationship($movimiento);
            // $this->movimientoTipoStockRepository->loadTipoStockRelationship($movimiento);
            $movimientoTipoStockResource = new MovimientoTipoStockResource($movimiento);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Movimiento tipo stock registrado exitosamente');       
            $responseResourse->body($movimientoTipoStockResource);
            return $responseResourse;
        }catch(\Exception $e){
            DB::rollback();   
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    // public function update($id,Request $movimientoData) {
    //     //no se debe editar un movimiento
    // }

    // public function destroy($id) {
    //     //no se debe eliminar un movimiento
    //     try{
    //         DB::beginTransaction();
    //         $movimiento = $this->movimientoRepository->obtenerPorId($id);
            
    //         if (!$movimiento){
    //             $notFoundResource = new NotFoundResource(null);
    //             $notFoundResource->title('Movimiento no encontrado');
    //             $notFoundResource->notFound(['id'=>$id]);
    //             return $notFoundResource->response()->setStatusCode(404);
    //         }
    //         $this->movimientoRepository->setModel($movimiento);
    //         $this->movimientoRepository->softDelete();
            
    //         $responseResource = new ResponseResource(null);
    //         $responseResource->title('Movimiento eliminado');  
    //         $responseResource->body(['id' => $id]);
    //         DB::commit();
    //         return $responseResource;
    //     }
    //     catch(\Exception $e){
    //         return (new ExceptionResource($e))->response()->setStatusCode(500);
    //     }
    // }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\MovimientosResource;
use App\Http\Resources\MovimientoResource;
use App\Http\Resources\MovimientoTipoStockResource;
use App\Http\Resources\MovimientosTipoStockResource;

use App\Http\Resources\ExceptionResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Models\Usuario;
use App\Repositories\MovimientoRepository;
use App\Repositories\MovimientoTipoStockRepository;
use App\Repositories\UsuarioRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

/*
Clase usada para la trazabilidad, solo se puede registrar un movimiento
y listar los movimientos. No se puede editar ni eliminar.
*/
class MovimientoController extends Controller
{
    protected $movimientoRepository;
    protected $movimientoTipoStockRepository;

    public function __construct(MovimientoRepository $movimientoRepository, MovimientoTipoStockRepository $movimientoTipoStockRepository){
        MovimientoResource::withoutWrapping();
        MovimientoTipoStockResource::withoutWrapping();
        $this->movimientoRepository = $movimientoRepository;
        $this->movimientoTipoStockRepository = $movimientoTipoStockRepository;
        //falta crear el repository
    }

    public function index() 
    {
        try{
            $movimientos = $this->movimientoRepository->obtenerTodos();
            
            /*PARTE DE TUTORIAL PARA RELATIONSHIPS */
            //a continuacion, cargamos la relacion de usuario a cada movimiento
            $usuarioRepository =  new UsuarioRepository(new Usuario);
            foreach ($movimientos as $key => $movimiento) {
               
                $this->movimientoRepository->loadUsuarioRelationship($movimiento);
                $usuario = $this->movimientoRepository->obtenerUsuarioModel();
                
                $usuarioRepository->loadTipoUsuarioRelationship($usuario);
       
            }
           
            /*FIN DE PARTE DE TUTORIAL PARA RELATIONSHIPS */

            $movimientoResource =  new MovimientosResource($movimientos);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de movimientos');  
            $responseResourse->body($movimientoResource);
            return $responseResourse;
        }
        catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }  
    }

    public function store(Request $movimientoData)
    {
        try{
            $validator = \Validator::make($movimientoData->all(), 
                            ['descripcion' => 'required',
                             'idUsuario' => 'required']);
            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            DB::beginTransaction();
            $movimiento = $this->movimientoRepository->guarda($movimientoData->all());
            DB::commit();
            $movimientoResource =  new MovimientoResource($movimiento);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Movimiento registrado exitosamente');       
            $responseResourse->body($movimientoResource);       
            return $responseResourse;
        }
        catch(\Exception $e){
            DB::rollback();   
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    public function update($id,Request $movimientoData) {
        //no se debe editar un movimiento
    }

    public function destroy($id) {
        //no se debe eliminar un movimiento
        try{
            DB::beginTransaction();
            $movimiento = $this->movimientoRepository->obtenerPorId($id);
            
            if (!$movimiento){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Movimiento no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->movimientoRepository->setModel($movimiento);
            $this->movimientoRepository->softDelete();
            
            $responseResource = new ResponseResource(null);
            $responseResource->title('Movimiento eliminado');  
            $responseResource->body(['id' => $id]);
            DB::commit();
            return $responseResource;
        }
        catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    public function listarPorTienda($idTienda){
        try{
            ini_set('max_execution_time', 2800);
            $tienda = $this->movimientoTipoStockRepository->obtenerPorId($idTienda);
            
            if (!$tienda){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Tienda no encontrada');
                $notFoundResource->notFound(['id'=>$idTienda]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $movimientos = $this->movimientoTipoStockRepository->listarPorTienda($idTienda);
            
            /*PARTE DE TUTORIAL PARA RELATIONSHIPS */
            //a continuacion, cargamos la relacion de usuario a cada movimiento
            $usuarioRepository =  new UsuarioRepository(new Usuario);
            Log::info(count($movimientos));
            foreach ($movimientos as $key => $movimiento) {
                Log::info($key);
                $this->movimientoTipoStockRepository->loadUsuarioRelationship($movimiento);
                $usuario = $this->movimientoTipoStockRepository->obtenerUsuarioModel();
                $usuarioRepository->loadTipoUsuarioRelationship($usuario);
                $this->movimientoTipoStockRepository->loadAlmacenRelationship($movimiento);
                $this->movimientoTipoStockRepository->loadTipoStockRelationship($movimiento);
                $this->movimientoTipoStockRepository->loadProductoRelationship($movimiento);
                

       
            }
           
           
            /*FIN DE PARTE DE TUTORIAL PARA RELATIONSHIPS */

            $movimientosResource =  new MovimientosTipoStockResource($movimientos);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de movimientos por id de tienda');  
            $responseResourse->body($movimientosResource);
            return $responseResourse;
        }
        catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }  
    }
}

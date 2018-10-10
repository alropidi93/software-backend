<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tienda;
use App\Models\Usuario;
use App\Repositories\TiendaRepository;
use App\Repositories\UsuarioRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\TiendaResource;
use App\Http\Resources\TiendasResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

class TiendaController extends Controller {

    protected $tiendaRepository;

    public function __construct(TiendaRepository $tiendaRepository){
        TiendaResource::withoutWrapping();
        $this->tiendaRepository = $tiendaRepository;
    }

    public function index() 
    {
        try{
            $tiendas = $this->tiendaRepository->obtenerTodos();
            foreach ($tiendas as $key => $tienda) {
                $this->tiendaRepository->loadJefeDeTiendaRelationship($tienda);
                $this->tiendaRepository->loadJefeDeAlmacenRelationship($tienda);
            }
            $tiendasResource =  new TiendasResource($tiendas);  
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
            $this->tiendaRepository->setModel($tienda);
            $this->tiendaRepository->loadJefeDeTiendaRelationship();
            $this->tiendaRepository->loadJefeDeAlmacenRelationship();
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
         
            DB::rollback();
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

       
    }

    public function asignarJefeDeTienda($idTienda, Request $data){
        try{
           
            DB::beginTransaction();
            $tienda = $this->tiendaRepository->obtenerPorId($idTienda);
         
            $idUsuario = $data['idUsuario'];
            if (!$tienda){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Tienda no encontrada');
                $notFoundResource->notFound(['id' => $idTienda]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $usuarioRepository =  new UsuarioRepository(new Usuario);
            $usuario =  $usuarioRepository->obtenerUsuarioPorId($idUsuario);
            
            if (!$usuario){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Usuario no encontrado');
                $notFoundResource->notFound(['idUsuario' => $idUsuario]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            
            $usuarioEsJefeDeTienda = $usuario->esJefeDeTienda();
            if (!$usuarioEsJefeDeTienda){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Jefe de tienda no encontrado');
                $notFoundResource->notFound(['idJefeTienda'=>$idUsuario]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            
            $this->tiendaRepository->setModel($tienda);
            $this->tiendaRepository->setJefeDeTiendaModel($usuario);
            $this->tiendaRepository->loadJefeDeAlmacenRelationship();
            
            

            $this->tiendaRepository->attachJefeTienda();
           
            DB::commit();
            $this->tiendaRepository->loadJefeDeTiendaRelationship();
            $tienda =  $this->tiendaRepository->obtenerModelo();
          
            $tiendaResource =  new TiendaResource($tienda);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Jefe de tienda asignado satisfactoriamente');  
            $responseResourse->body($tiendaResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

    }

    public function asignarJefeDeAlmacen($idTienda, Request $data){
        try{
           
            DB::beginTransaction();
            $tienda = $this->tiendaRepository->obtenerPorId($idTienda);
         
            $idUsuario = $data['idUsuario'];
            if (!$tienda){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Tienda no encontrada');
                $notFoundResource->notFound(['id' => $idTienda]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $usuarioRepository =  new UsuarioRepository(new Usuario);
            $usuario =  $usuarioRepository->obtenerUsuarioPorId($idUsuario);
            
            if (!$usuario){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Usuario no encontrado');
                $notFoundResource->notFound(['idUsuario' => $idUsuario]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            
            $usuarioEsJefeDeAlmacen = $usuario->esJefeDeAlmacen();
            if (!$usuarioEsJefeDeAlmacen){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Jefe de almacen no encontrado');
                $notFoundResource->notFound(['idJefeAlmacen'=>$idUsuario]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            
            $this->tiendaRepository->setModel($tienda);
            $this->tiendaRepository->setJefeDeAlmacenModel($usuario);
            $this->tiendaRepository->loadJefeDeTiendaRelationship();    
            $this->tiendaRepository->attachJefeAlmacen();
        
            DB::commit();
            $this->tiendaRepository->loadJefeDeAlmacenRelationship();
            
            $tienda =  $this->tiendaRepository->obtenerModelo();
          
            $tiendaResource =  new TiendaResource($tienda);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Jefe de almacen asignado satisfactoriamente');  
            $responseResourse->body($tiendaResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

    }

    public function asignarTrabajador($idTienda, Request $data){
        try{
           
            DB::beginTransaction();
            $tienda = $this->tiendaRepository->obtenerPorId($idTienda);
         
            $idUsuario = $data['idUsuario'];
            if (!$tienda){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Tienda no encontrada');
                $notFoundResource->notFound(['id' => $idTienda]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $usuarioRepository =  new UsuarioRepository(new Usuario);
            $usuario =  $usuarioRepository->obtenerUsuarioPorId($idUsuario);
            
            if (!$usuario){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Usuario no encontrado');
                $notFoundResource->notFound(['idUsuario' => $idUsuario]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            
            $usuarioEsJefeDeTienda = $usuario->esJefeDeTienda();
            $usuarioEsJefeDeAlmacen = $usuario->esJefeDeAlmacen();
            $usuarioEsAdmin = $usuario->esAdmin();
            if ($usuarioEsJefeDeTienda || $usuarioEsJefeDeAlmacen || $usuarioEsAdmin){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Usuario trabajador no encontrado');
                $notFoundResource->notFound(['idUsuario'=>$idUsuario]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            
            $this->tiendaRepository->setModel($tienda);
            $this->tiendaRepository->attachTrabajador($usuario);
                    
            DB::commit();
            $this->tiendaRepository->loadTrabajadoresRelationship();
            $tienda =  $this->tiendaRepository->obtenerModelo();
          
            $tiendaResource =  new TiendaResource($tienda);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Trabajador agregado a tienda satisfactoriamente');  
            $responseResourse->body($tiendaResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

    }

  
    public function busquedaPorFiltro()
    {
        try{
            $tienda = $this->tiendaRepository->obtenerModelo();
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
                                  
                    $tiendas = $this->tiendaRepository->buscarPorFiltro($filter, $value);
                    
                    $tiendasResource =  new TiendasResource($tiendas);
                    $responseResource->title('Lista de tiendas filtradas por nombre');       
                    $responseResource->body($tiendasResource);
                    break;

                case 'distrito':
                    $tiendas = $this->tiendaRepository->buscarPorFiltro($filter, $value);
                    
                    $tiendasResource =  new TiendasResource($tiendas);
                    $responseResource->title('Lista de tiendas filtradas por distrito');       
                    $responseResource->body($tiendasResource);
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

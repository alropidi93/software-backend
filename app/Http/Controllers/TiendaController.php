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
use App\Http\Resources\ErrorResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

class TiendaController extends Controller {

    protected $tiendaRepository;


    public function __construct(TiendaRepository $tiendaRepository){
        TiendaResource::withoutWrapping();
      ;
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
            $this->tiendaRepository->loadAlmacenRelationship();
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

    public function desasignarJefeDeTienda($idTienda){
        try{
           
            DB::beginTransaction();
            $tienda = $this->tiendaRepository->obtenerPorId($idTienda);
         
          
            if (!$tienda){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Tienda no encontrada');
                $notFoundResource->notFound(['id' => $idTienda]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->tiendaRepository->setModel($tienda);
           
            if(!$this->tiendaRepository->checkIfOwnModelTiendaHasJefeTienda()){

                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de integridad');
                $errorResource->message('La tienda no cuenta con un jefe de tienda asociado');
                return $errorResource->response()->setStatusCode(400);
            }
                      
            $this->tiendaRepository->actualiza(['idJefeTienda'=>null]);
            

          
           
            DB::commit();
            $this->tiendaRepository->loadJefeDeAlmacenRelationship();
            $this->tiendaRepository->loadJefeDeTiendaRelationship();
            $tienda = $this->tiendaRepository->obtenerModelo();
            
          
          
            $tiendaResource =  new TiendaResource($tienda);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Jefe de tienda desasignado satisfactoriamente de la tienda');  
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

    public function desasignarJefeDeAlmacen($idTienda){
        try{
           
            DB::beginTransaction();
            $tienda = $this->tiendaRepository->obtenerPorId($idTienda);
         
          
            if (!$tienda){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Tienda no encontrada');
                $notFoundResource->notFound(['id' => $idTienda]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->tiendaRepository->setModel($tienda);
           
            if(!$this->tiendaRepository->checkIfOwnModelTiendaHasJefeAlmacen()){

                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de integridad');
                $errorResource->message('La tienda no cuenta con un jefe de almacen asociado');
                return $errorResource->response()->setStatusCode(400);
            }
                      
            $this->tiendaRepository->actualiza(['idJefeAlmacen'=>null]);
            

          
           
            DB::commit();
            $this->tiendaRepository->loadJefeDeAlmacenRelationship();
            $this->tiendaRepository->loadJefeDeTiendaRelationship();
            $tienda = $this->tiendaRepository->obtenerModelo();
            
          
          
            $tiendaResource =  new TiendaResource($tienda);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Jefe de almacen desasignado satisfactoriamente de la tienda');  
            $responseResourse->body($tiendaResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

    }

    public function asignarTrabajador($idTienda, Request $data){
        ini_set('max_execution_time', 180);
        
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
            // $tiendasCargoJefeTienda = $tiendasCargoJedeAlmacen = 0;
            // if ($usuario->tiendasCargoJefeTienda){ //check if is in charge in some 'tiendas' as 'jefe de tienda'
            //     $tiendasCargoJefeTienda = count($usuario->tiendasCargoJefeTienda);
            // }
            // if ($usuario->tiendasCargoJefeAlmacen){//check if is in charge in some 'tiendas' as 'jefe de almacen'
            //     $tiendasCargoJefeAlmacen = count($usuario->tiendasCargoJefeAlmacen);
            // }
              
            $miembroPrincipalFlag = strval(Input::get('miembroPrincipal'));

            
            // return $miembroPrincipalFlag;
            // return intval( $miembroPrincipalFlag==false|| $miembroPrincipalFlag==null);
            //return json_encode($miembroPrincipalFlag);
            if (!($miembroPrincipalFlag == "true"  || $miembroPrincipalFlag=="false"|| $miembroPrincipalFlag==null)){
                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de filtro');
                $errorResource->message('El valor del filtro no es el adecuado');
                return $errorResource->response()->setStatusCode(400);
            }
            if ($miembroPrincipalFlag == "true")
                $miembroPrincipalFlag = true;
            else if ($miembroPrincipalFlag=="false")
                $miembroPrincipalFlag= false;
            else{
                $miembroPrincipalFlag="null";
            }
          
            if ( $usuario->esAdmin() ||  ( $miembroPrincipalFlag == "true" &&  ($usuario->esJefeDeTienda()|| $usuario->esJefeDeAlmacen()) )){
                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de seguridad');
                $errorResource->message('Usuario con rol no permitido para esta acción');
                return $errorResource->response()->setStatusCode(422);
            }
            // return json_encode($miembroPrincipalFlag == null);
            //return json_encode($miembroPrincipalFlag);
            
            //return  json_encode($usuario->esJefeDeTienda()|| $usuario->esJefeDeAlmacen());     
            $data['miembroPrincipal']= ($usuario->esJefeDeTienda()|| $usuario->esJefeDeAlmacen()) ? false: ($miembroPrincipalFlag == "null" ? true:$miembroPrincipalFlag);
            //$data['miembroPrincipal']=false;
            //return json_encode($data['miembroPrincipal']);
                      
            $this->tiendaRepository->setModel($tienda);
            if ($this->tiendaRepository->checkIfUsuarioAttachedBefore($usuario)){
                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de integridad');
                $errorResource->message('Ya existe un usuario-trabajador con el id solicitado, asociado a la tienda');
                return $errorResource->response()->setStatusCode(400);
            }
            $this->tiendaRepository->attachTrabajador($usuario,$data);
                    
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

    public function desasignarTrabajador($idTienda,Request $data){
        ini_set('max_execution_time', 180);
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
            $this->tiendaRepository->setModel($tienda);
            
            //return $tienda->trabajadores()->where('usuario.idPersonaNatural',$id)->first();

            if(!$this->tiendaRepository->checkIfUsuarioAttachedBefore($usuario)){

                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de integridad');
                $errorResource->message('La tienda no cuenta con este trabajador asociado');
                return $errorResource->response()->setStatusCode(400);
            }
            //$usuarioRepository->setModel($usuario);
            
            //$usuarioRepository->actualizaSoloUsuario(['idTienda'=>null]);          
            $status = $this->tiendaRepository->deleteUsuarioRelationship($usuario);          
            
            if ($status || !$status){
                DB::commit();
                $this->tiendaRepository->loadJefeDeAlmacenRelationship();
                $this->tiendaRepository->loadJefeDeTiendaRelationship();
                $this->tiendaRepository->loadTrabajadoresRelationship();
                $tienda = $this->tiendaRepository->obtenerModelo();
                
            
            
                $tiendaResource =  new TiendaResource($tienda);  
                $responseResourse = new ResponseResource(null);
                $responseResourse->title('Trabajador desasignado satisfactoriamente de la tienda');  
                $responseResourse->body($tiendaResource);
                return $responseResourse;
            }

          
           
            
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

    public function obtenerTiendasFuncionales(){
        try{
            $tiendas = $this->tiendaRepository->obtenerTiendasFuncionales();
            foreach ($tiendas as $key => $tienda) {
                $this->tiendaRepository->loadJefeDeTiendaRelationship($tienda);
                $this->tiendaRepository->loadJefeDeAlmacenRelationship($tienda);
            }
            $tiendasResource =  new TiendasResource($tiendas);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de tiendas');  
            $responseResourse->body($tiendasResource);
            return $responseResourse;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }


 
}

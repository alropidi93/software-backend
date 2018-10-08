<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UsuarioResource;
use App\Http\Resources\UsuariosResource;
use App\Http\Resources\TipoUsuarioResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Models\Usuario;
use App\Repositories\UsuarioRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;


class UsuarioController extends Controller
{
    protected $usuarioRepository;

    public function __construct(UsuarioRepository $usuarioRepository){
        UsuarioResource::withoutWrapping();
        $this->usuarioRepository = $usuarioRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            //return $this->usuarioRepository->listarUsuariosSinTipo();
            $usuarios = $this->usuarioRepository->listarUsuarios();
            foreach ($usuarios as $key => $usuario) {
                $this->usuarioRepository->loadTipoUsuarioRelationShip($usuario);
            }
           
            $tiposUsuarioResource =  new UsuariosResource($usuarios); 
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de usuarios');  
            $responseResourse->body($tiposUsuarioResource);
            return $responseResourse;
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
    public function store(Request $usuarioData)
    {
        try{
            
            $validator = \Validator::make($usuarioData->all(), 
                            ['nombre' => 'required',
                            'apellidos' => 'required',
                            'genero'=>  'required',
                            'fechaNac' => 'required',
                            'email' => 'required|email',
                            'dni' => 'required|min:8|max:12|unique:personaNatural,dni',
                            'password' => 'required|min:6|max:60',
                            'direccion' => 'required'
                            
                            ]);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }

            $usuario= $this->usuarioRepository->obtenerUsuarioPorEmail($usuarioData['email']);
            if ($usuario){
                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de validación del email');
                $errorResource->message('El email ya se encuentra en uso');
                return $errorResource->response()->setStatusCode(422);
            }

            DB::beginTransaction();
            $this->usuarioRepository->guarda($usuarioData->all());
            $usuarioCreated = $this->usuarioRepository->obtenerModelo();
            
             
            DB::commit();
            
            
            $usuarioResource =  new UsuarioResource($usuarioCreated);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Usuario creado exitosamente');       
            $responseResourse->body($usuarioResource);       
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
            $usuario = $this->usuarioRepository->obtenerUsuarioPorId($id);
            
            if (!$usuario){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Usuario no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->usuarioRepository->setModelUsuario($usuario);
            $this->usuarioRepository->loadTipoUsuarioRelationship();
            $usuarioResource =  new UsuarioResource($usuario);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar usuario');  
            $responseResourse->body($usuarioResource);
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
    public function update($id,Request $usuarioData)
    {
        try{
            
            
            DB::beginTransaction();
            $usuario= $this->usuarioRepository->obtenerUsuarioPorId($id);
            if (!$usuario){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Usuario no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            
            $this->usuarioRepository->setModelUsuario($usuario);
            $usuarioDataArray= Algorithm::quitNullValuesFromArray($usuarioData->all());
            $this->usuarioRepository->actualiza($usuarioDataArray);
            $usuario = $this->usuarioRepository->obtenerModelo();
            DB::commit();
            $this->usuarioRepository->loadTipoUsuarioRelationship();

            $usuarioResource =  new UsuarioResource($usuario);
            $responseResourse = new ResponseResource(null);
            
            $responseResourse->title('Usuario actualizado exitosamente');       
            $responseResourse->body($usuarioResource);     
            
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
            $usuario = $this->usuarioRepository->obtenerUsuarioPorId($id);
            
            if (!$usuario){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Usuario no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->usuarioRepository->setModelUsuario($usuario);
            $this->usuarioRepository->softDelete();
            

              
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Usuario eliminado');  
            $responseResourse->body(['id' => $id]);
            DB::commit();
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }

    public function listarUsuariosSinTipo(){

        try{
            //return $this->usuarioRepository->listarUsuariosSinTipo();
            
            $tiposUsuarioResource =  new UsuariosResource($this->usuarioRepository->listarUsuariosSinTipo()); 
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de usuarios sin rol asignado');  
            $responseResourse->body($tiposUsuarioResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
        
    }

    public function asignarRol($idUsuario, Request $data){
        try{
           
            DB::beginTransaction();
            $usuario = $this->usuarioRepository->obtenerUsuarioPorId($idUsuario);
            
            if (!$usuario){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Usuario no encontrado');
                $notFoundResource->notFound(['id'=>$idUsuario]);
                return $notFoundResource->response()->setStatusCode(404);
            }

            $tipoUsuario =  $this->usuarioRepository->obtenerRolPorKey($data['tipoUsuarioKey']);
            if (!$tipoUsuario){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Tipo usuario no encontrado');
                $notFoundResource->notFound(['id'=>$data['idTipoUsuario']]);
                return $notFoundResource->response()->setStatusCode(404);
            }

            if ($usuario->esJefeDeTiendaAsignado()){
                $errorResource =  new ErrorResource (null);
                $errorResource->title("Error de asignación de rol");
                $errorResource->message("Debe desasignar al jefe de tienda, de la tienda que tiene a su cargo");
                return $errorResource;
            }
            if ($usuario->esJefeDeAlmacenAsignado() ){
                $errorResource =  new ErrorResource (null);
                $errorResource->title("Error de asignación de rol");
                $errorResource->message("Debe desasignar al jefe de almacen, de la tienda cuyo almacen tiene a su cargo");
                return $errorResource;
            }
            
            $this->usuarioRepository->setModelUsuario($usuario);
            $this->usuarioRepository->setTipoUsuarioModel($tipoUsuario);

            
            $this->usuarioRepository->attachRolWithOwnModels();
            // $obj = $this->usuarioRepository->obtenerModelo();
            // $obj->tipoUsuario;
            // return $obj;
            DB::commit();
            $this->usuarioRepository->loadTipoUsuarioRelationship();
            
          
            $tiposUsuarioResource =  new UsuarioResource($usuario);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Rol asignado satisfactoriamente');  
            $responseResourse->body($tiposUsuarioResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

    }

    public function login (Request $request){
    

        try {
          $validator = \Validator::make($request->all(),
                 ['email' => 'required|email',
                  'password' =>'required|min:6|max:60']);
    
          if ($validator->fails()) {
            return (new ValidationResource($validator))->response()->setStatusCode(422);
            
          }
          $usuario= $this->usuarioRepository->obtenerUsuarioPorEmail($request['email']);
          
          
       
          if ($usuario != null ) {
                $this->usuarioRepository->setModel($usuario);
                $password = $this->usuarioRepository->getPassword();
                if (Hash::check($request['password'], $password)){
                    Log:info("paso el login");
                    $this->usuarioRepository->loadTipoUsuarioRelationship();
                    $usuarioResource =  new UsuarioResource($usuario);
                    $responseResourse = new ResponseResource(null);
                    $responseResourse->title('Usuario logueado exitosamente');       
                    $responseResourse->body($usuarioResource);       
                    return $responseResourse;
                } 
            
            
          }
          else {
            Log::info("no paso el login");
            $errorResource =  new ErrorResource(null);
            
            $errorResource->title('Error de logueo');       
            $errorResource->message('Credenciales no válidas');       
            return $errorResource->response()->setStatusCode(400);
            
          }
    
          
       
        } catch(\Exception $e) {
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    public function listarPorRol()
    {
        try{
           
            $rol = Input::get('rol');
           
            $responseResource = new ResponseResource(null);
            if (!$rol){
                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de búsqueda');
                $errorResource->message('Parámetro inválido para la búsqueda');
                return $errorResource->response()->setStatusCode(400);

            }
        
            switch ($rol) {
                case 0:
                    $usuarios = $this->usuarioRepository->listarAdmin();
                    
                  
                    break;
                case 1:
                    $usuarios = $this->usuarioRepository->listarJefesTienda();
                
                
                break;

                case 2:
                    $usuarios = $this->usuarioRepository->listarCompradores();
                
                
                    break;

                case 3:
                    $usuarios = $this->usuarioRepository->listarJefesAlmacen();
                
                
                    break;

                case 4:
                    $usuarios = $this->usuarioRepository->listarCajerosVentas();
                  
                    break;
                case 5:
                    $usuarios = $this->usuarioRepository->listarCajerosDevoluciones();
                   
                    
                    break;

                default:
                    $errorResource = new ErrorResource(null);
                    $errorResource->title('Error de búsqueda');
                    $errorResource->message('Valor de rol inválido');
                    return $errorResource->response()->setStatusCode(400);
                    
            }
            foreach ($usuarios as $key => $usuario) {
                $this->usuarioRepository->loadTipoUsuarioRelationship($usuario);
                
                
            }
            $usuariosResource =  new UsuariosResource($usuarios);
            $responseResource->title('Lista de usuarios por rol');       
            $responseResource->body($usuariosResource);
            
            return $responseResource; 
        }
        catch(\Exception $e){
                
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    
    }

    public function listarJefesDeTiendaSinTienda(){
        try{
                        
            $usuarios = $this->usuarioRepository->listarJefesTiendaSinTienda();
            foreach ($usuarios as $key => $usuario) {
                $this->usuarioRepository->loadTipoUsuarioRelationship($usuario);
                //$this->usuarioRepository->loadTiendasCargoJefeTiendaRelationship($usuario);
                
                
            }
            
            $tiposUsuarioResource =  new UsuariosResource($usuarios); 
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de jefes de tienda no asignados a tiendas');  
            $responseResourse->body($tiposUsuarioResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

    }


    public function listarJefesDeAlmacenSinTienda(){
        try{
                        
            $usuarios = $this->usuarioRepository->listarJefesAlmacenSinTienda();
            foreach ($usuarios as $key => $usuario) {
                $this->usuarioRepository->loadTipoUsuarioRelationship($usuario);
                //$this->usuarioRepository->loadTiendasCargoJefeTiendaRelationship($usuario);
                
                
            }
            
            $tiposUsuarioResource =  new UsuariosResource($usuarios); 
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de jefes de almacenes no asignados a tiendas');  
            $responseResourse->body($tiposUsuarioResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

    }

    public function listarCajeros(){
        try{
                        
            $usuarios = $this->usuarioRepository->listarCajeros();
            foreach ($usuarios as $key => $usuario) {
                $this->usuarioRepository->loadTipoUsuarioRelationship($usuario);
                //$this->usuarioRepository->loadTiendasCargoJefeTiendaRelationship($usuario);
                
                
            }
            
            $tiposUsuarioResource =  new UsuariosResource($usuarios); 
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de jefes de cajeros en total');  
            $responseResourse->body($tiposUsuarioResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

    }
}

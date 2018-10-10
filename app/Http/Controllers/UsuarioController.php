<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\UsuarioService; 
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
           
            $usuariosResource =  new UsuariosResource($usuarios); 
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Listado de usuarios');  
            $responseResourse->body($usuariosResource);
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
            $usuarioDataArray= Algorithm::quitNullValuesFromArray($usuarioData->all());
            if (array_key_exists('dni',$usuarioData)){
                $usuarioAux= $this->usuarioRepository->obtenerUsuarioPorDni($usuarioData['dni']);
                if ($id != $usuarioDataAux->id){
                    $validator = ['dni'=>'El dni ya está siendo usado por otro usuario'];
                    return (new ValidationResource($validator))->response()->setStatusCode(422);
                }
            }
    
         
            DB::beginTransaction();
            $usuario= $this->usuarioRepository->obtenerUsuarioPorId($id);
            if (!$usuario){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Usuario no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            
            $this->usuarioRepository->setModelUsuario($usuario);
            
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
            
            $usuariosResource =  new UsuariosResource($this->usuarioRepository->listarUsuariosSinTipo()); 
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Listado de usuarios sin rol asignado');  
            $responseResourse->body($usuariosResource);
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
            
          
            $usuarioResource =  new UsuarioResource($usuario);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Rol asignado satisfactoriamente');  
            $responseResourse->body($usuarioResource);
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
          
            Log::info("No paso el login");
            $errorResource =  new ErrorResource(null);
            $errorResource->title('Error de logueo');       
            $errorResource->message('Credenciales no válidas');       
            return $errorResource->response()->setStatusCode(400);
                
          
    
          
       
        } catch(\Exception $e) {
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    public function listarPorRol()
    {
        try{
           
            $rol = Input::get('rol');
           
            $responseResource = new ResponseResource(null);
            
        
            switch ($rol) {
                case 0:
                    $usuarios = $this->usuarioRepository->listarAdmins();
                    $responseResource->title('Listado por rol - Admins');
                  
                    break;
                case 1:
                    $usuarios = $this->usuarioRepository->listarJefesTienda();
                    $responseResource->title('Listado por rol - Jefes de tienda');
                
                
                break;

                case 2:
                    $usuarios = $this->usuarioRepository->listarCompradores();
                    $responseResource->title('Listado por rol - Compradores');
                
                
                    break;

                case 3:
                    $usuarios = $this->usuarioRepository->listarJefesAlmacen();
                    $responseResource->title('Listado por rol - Jefes de almacén');
                
                
                    break;

                case 4:
                    $usuarios = $this->usuarioRepository->listarCajerosVentas();
                    $responseResource->title('Listado por rol - Cajeros de ventas');
                  
                    break;
                case 5:
                    $usuarios = $this->usuarioRepository->listarCajerosDevoluciones();
                    $responseResource->title('Listado por rol - Cajeros de devoluciones');
                   
                    
                    break;
                case 6:
                    $usuarios = $this->usuarioRepository->listarAlmaceneros();
                    $responseResource->title('Listado por rol - Almaceneros');
                    
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
                
            $responseResource->body($usuariosResource);
            
            return $responseResource; 
        }
        catch(\Exception $e){
                
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    
    }

    public function listarPorRolSinTiendaAsignada()
    {
        try{
            
            $rol = Input::get('rol');
            
            if (!$rol ){
                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de búsqueda');
                $errorResource->message('Parámetros inválidos para la búsqueda');
                return $errorResource->response()->setStatusCode(400);

            }
            $filter = Input::get('filterBy');
            $value = strtolower(Input::get('value'));
            
           
            $responseResource = new ResponseResource(null);
            $usuarioService = new UsuarioService;
        
            switch ($rol) {
                case 0:
                    $title = 'Listado por rol sin tienda asignada - Admins';
                    $usuarios = $this->usuarioRepository->listarAdminSinTienda();
                    if ($filter && $value){
                        
                        switch ($filter) {
                            case 'nombre':
                                $title .= ' (filtrada por nombre)';
                                $usuarios = $usuarioService->filterUsuarioCollectionByName($usuarios, $value);
                                
                                break;
                            case 'apellidos':
                                $title .= ' (filtrada por apellido)';
                                $usuarios = $usuarioService->filterUsuarioCollectionByApellido($usuarios, $value);
                                break;
                        }
                    }
                    $responseResource->title($title);
                  
                    break;
                case 1:
                    $title = 'Listado por rol sin tienda asignada - Jefes de tienda';
                    $usuarios = $this->usuarioRepository->listarJefesTiendaSinTienda();
                    if ($filter && $value){
                        
                        switch ($filter) {
                            case 'nombre':
                                $title .= ' (filtrada por nombre)';
                                $usuarios = $usuarioService->filterUsuarioCollectionByName($usuarios, $value);
                                
                                break;
                            case 'apellidos':
                                $title .= ' (filtrada por apellido)';
                                $usuarios = $usuarioService->filterUsuarioCollectionByApellido($usuarios, $value);
                                break;
                        }
                    }
                    $responseResource->title($title);
                
                
                    break;

                case 2:
                    $title = 'Listado por rol sin tienda asignada - Compradores';
                    $usuarios = $this->usuarioRepository->listarCompradoresSinTienda();
                    if ($filter && $value){
                        
                        switch ($filter) {
                            case 'nombre':
                                $title .= ' (filtrada por nombre)';
                                $usuarios = $usuarioService->filterUsuarioCollectionByName($usuarios, $value);
                                
                                break;
                            case 'apellidos':
                                $title .= ' (filtrada por apellido)';
                                $usuarios = $usuarioService->filterUsuarioCollectionByApellido($usuarios, $value);
                                break;
                        }
                    }
                    $responseResource->title($title);
                
                
                    break;

                case 3:
                $title = 'Listado por rol sin tienda asignada - Jefes de almacén';
                    $usuarios = $this->usuarioRepository->listarJefesAlmacenSinTienda();
                    if ($filter && $value){
                        
                        switch ($filter) {
                            case 'nombre':
                                $title .= ' (filtrada por nombre)';
                                $usuarios = $usuarioService->filterUsuarioCollectionByName($usuarios, $value);
                                
                                break;
                            case 'apellidos':
                                $title .= ' (filtrada por apellido)';
                                $usuarios = $usuarioService->filterUsuarioCollectionByApellido($usuarios, $value);
                                break;
                        }
                    }
                    $responseResource->title($title);
                
                
                    break;

                case 4:
                    $title = 'Listado por rol sin tienda asignada - Cajeros de ventas';
                    $usuarios = $this->usuarioRepository->listarCajerosVentasSinTienda();
                    if ($filter && $value){
                        
                        switch ($filter) {
                            case 'nombre':
                                $title .= ' (filtrada por nombre)';
                                $usuarios = $usuarioService->filterUsuarioCollectionByName($usuarios, $value);
                                
                                break;
                            case 'apellidos':
                                $title .= ' (filtrada por apellido)';
                                $usuarios = $usuarioService->filterUsuarioCollectionByApellido($usuarios, $value);
                                break;
                        }
                    }
                    $responseResource->title($title);
                  
                    break;
                case 5:
                    $title = 'Listado por rol sin tienda asignada - Cajeros de devoluciones';
                    $usuarios = $this->usuarioRepository->listarCajerosDevolucionesSinTienda();
                    if ($filter && $value){
                        
                        switch ($filter) {
                            case 'nombre':
                                $title .= ' (filtrada por nombre)';
                                $usuarios = $usuarioService->filterUsuarioCollectionByName($usuarios, $value);
                                
                                break;
                            case 'apellidos':
                                $title .= ' (filtrada por apellido)';
                                $usuarios = $usuarioService->filterUsuarioCollectionByApellido($usuarios, $value);
                                break;
                        }
                    }
                    $responseResource->title($title);
                   
                    
                    break;
                case 6:
                    $title = 'Listado por rol sin tienda asignada - Almaceneros';
                    $usuarios = $this->usuarioRepository->listarAlmacenerosSinTienda();
                    if ($filter && $value){
                        
                        switch ($filter) {
                            case 'nombre':
                                $title .= ' (filtrada por nombre)';
                                $usuarios = $usuarioService->filterUsuarioCollectionByName($usuarios, $value);
                                
                                break;
                            case 'apellidos':
                                $title .= ' (filtrada por apellido)';
                                $usuarios = $usuarioService->filterUsuarioCollectionByApellido($usuarios, $value);
                                break;
                        }
                    }
                    $responseResource->title($title);
                    
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
            
            $usuariosResource =  new UsuariosResource($usuarios); 
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de jefes de tienda no asignados a tiendas');  
            $responseResourse->body($usuariosResource);
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
            
            $usuariosResource =  new UsuariosResource($usuarios); 
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de jefes de almacenes no asignados a tiendas');  
            $responseResourse->body($usuariosResource);
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
            
            $usuariosResource =  new UsuariosResource($usuarios); 
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de cajeros (de ventas y de devoluciones)');  
            $responseResourse->body($usuariosResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

    }

    public function listarCajerosSinTiendaAsignada(){
        try{
            $filter = Input::get('filterBy');
            $value = strtolower(Input::get('value'));
            
           
            
            $usuarioService = new UsuarioService;
            $title = 'Listado de cajeros sin tienda asignada (de ventas y de devoluciones)';         
            $usuarios = $this->usuarioRepository->listarCajerosSinTienda();
            if ($filter && $value){
                        
                switch ($filter) {
                    case 'nombre':
                        $title .= ' (filtrada por nombre)';
                        //$usuario = $usuarios[0];
                        //return $usuario->personaNatural;
                        //$name = $usuario->personaNatual->name;

                        $usuarios = $usuarioService->filterUsuarioCollectionByName($usuarios, $value);
                        
                        break;
                    case 'apellidos':
                        $title .= ' (filtrada por apellido)';
                        $usuarios = $usuarioService->filterUsuarioCollectionByApellido($usuarios, $value);
                        break;
                }
            }
            foreach ($usuarios as $key => $usuario) {
                $this->usuarioRepository->loadTipoUsuarioRelationship($usuario);
                //$this->usuarioRepository->loadTiendasCargoJefeTiendaRelationship($usuario);
                
                
            }
            
            $usuariosResource =  new UsuariosResource($usuarios); 
            $responseResourse = new ResponseResource(null);
            $responseResourse->title($title);  
            $responseResourse->body($usuariosResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

    }

    public function obtenerPorRolPorTienda($idTienda)
    {
        try{
            $tienda = $this->usuarioRepository->obtenerTiendaPorId($idTienda);
         
            
            if (!$tienda){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Tienda no encontrada');
                $notFoundResource->notFound(['id' => $idTienda]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $rol = Input::get('rol');
           
            $responseResource = new ResponseResource(null);
            
            
            switch ($rol) {
                case 0:
                    $usuarios = $this->usuarioRepository->listarAdminsPorTienda($idTienda);
                    if (!$usuarios || count($usuarios)==0){
                        $notFoundResource = new NotFoundResource(null);
                        $notFoundResource->title('Admins de la tienda no encontrados');
                        $notFoundResource->notFound(['idTienda' => $idTienda]);
                        return $notFoundResource->response()->setStatusCode(404);
                    }
                    $responseResource->title('Listado de admins por tienda');
                    foreach ($usuarios as $key => $usuario) {
                        $this->usuarioRepository->loadTipoUsuarioRelationship($usuario);
                                 
                    }
                    $usuariosResource =  new UsuariosResource($usuarios);
                
                    $responseResource->body($usuariosResource);
                  
                    break;
                case 1:
                    
                    $usuario = $this->usuarioRepository->obtenerJefeTiendaPorTienda($idTienda);
                    if (!$usuario){
                        $notFoundResource = new NotFoundResource(null);
                        $notFoundResource->title('Jefe de tienda de la tienda no encontrado');
                        $notFoundResource->notFound(['idTienda' => $idTienda]);
                        return $notFoundResource->response()->setStatusCode(404);
                    }
                    
                    $responseResource->title('Jefe de tienda de determinada tienda');
                    
                    $this->usuarioRepository->loadTipoUsuarioRelationship($usuario);
                    $usuarioResource =  new UsuarioResource($usuario);
                
                    $responseResource->body($usuarioResource);
                                 
                    
                
                
                break;

                case 2:
                    $usuarios = $this->usuarioRepository->listarCompradoresPorTienda($idTienda);
                    if (!$usuarios || count($usuarios)==0){
                        $notFoundResource = new NotFoundResource(null);
                        $notFoundResource->title('Compradores de la tienda no encontrados');
                        $notFoundResource->notFound(['idTienda' => $idTienda]);
                        return $notFoundResource->response()->setStatusCode(404);
                    }
                    $responseResource->title('Listado de compradores por tienda');
                    foreach ($usuarios as $key => $usuario) {
                        $this->usuarioRepository->loadTipoUsuarioRelationship($usuario);
                                 
                    }
                    $usuariosResource =  new UsuariosResource($usuarios);
                
                    $responseResource->body($usuariosResource);
                
                
                    break;

                case 3:
                    $usuario = $this->usuarioRepository->obtenerJefeAlmacenPorTienda($idTienda);
                    if (!$usuario ){
                        $notFoundResource = new NotFoundResource(null);
                        $notFoundResource->title('Jefe de almacen de la tienda no encontrado');
                        $notFoundResource->notFound(['idTienda' => $idTienda]);
                        return $notFoundResource->response()->setStatusCode(404);
                    }
                    $responseResource->title('Jefes de almacén de determinada tienda');
                    $this->usuarioRepository->loadTipoUsuarioRelationship($usuario);
                    $usuarioResource =  new UsuarioResource($usuario);
                
                    $responseResource->body($usuarioResource);
                
                
                    break;

                case 4:
                    $usuarios = $this->usuarioRepository->listarCajerosVentasPorTienda($idTienda);
                    if (!$usuarios || count($usuarios)==0){
                        $notFoundResource = new NotFoundResource(null);
                        $notFoundResource->title('Cajeros de ventas de la tienda no encontrados');
                        $notFoundResource->notFound(['idTienda' => $idTienda]);
                        return $notFoundResource->response()->setStatusCode(404);
                    }
                    $responseResource->title('Listado cajeros de ventas por tienda');
                    foreach ($usuarios as $key => $usuario) {
                        $this->usuarioRepository->loadTipoUsuarioRelationship($usuario);
                                 
                    }
                    $usuariosResource =  new UsuariosResource($usuarios);
                
                    $responseResource->body($usuariosResource);
                  
                    break;
                case 5:
                    $usuarios = $this->usuarioRepository->listarCajerosDevolucionesPorTienda($idTienda);
                    if (!$usuarios || count($usuarios)==0){
                        $notFoundResource = new NotFoundResource(null);
                        $notFoundResource->title('Cajeros de devoluciones de la tienda no encontrados');
                        $notFoundResource->notFound(['idTienda' => $idTienda]);
                        return $notFoundResource->response()->setStatusCode(404);
                    }
                    $responseResource->title('Listado cajeros de devoluciones por tienda');
                    foreach ($usuarios as $key => $usuario) {
                        $this->usuarioRepository->loadTipoUsuarioRelationship($usuario);
                                 
                    }
                    $usuariosResource =  new UsuariosResource($usuarios);
                
                    $responseResource->body($usuariosResource);
                   
                    
                    break;
                case 6:
                    $usuarios = $this->usuarioRepository->listarAlmacenerosPorTienda($idTienda);
                    if (!$usuarios || count($usuarios)==0){
                        $notFoundResource = new NotFoundResource(null);
                        $notFoundResource->title('Almaceneros de la tienda no encontrados');
                        $notFoundResource->notFound(['idTienda' => $idTienda]);
                        return $notFoundResource->response()->setStatusCode(404);
                    }
                    $responseResource->title('Listado de almaceneros por tienda');
                    foreach ($usuarios as $key => $usuario) {
                        $this->usuarioRepository->loadTipoUsuarioRelationship($usuario);
                                 
                    }
                    $usuariosResource =  new UsuariosResource($usuarios);
                
                    $responseResource->body($usuariosResource);
                    
                    break;

                default:
                    $errorResource = new ErrorResource(null);
                    $errorResource->title('Error de búsqueda');
                    $errorResource->message('Valor de rol inválido');
                    return $errorResource->response()->setStatusCode(400);
                    
            }
            
            
            
            return $responseResource; 
        }
        catch(\Exception $e){
                
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    
    }


    public function obtenerCajerosPorTienda($idTienda)
    {
        try{
            $tienda = $this->usuarioRepository->obtenerTiendaPorId($idTienda);
         
            
            if (!$tienda){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Tienda no encontrada');
                $notFoundResource->notFound(['id' => $idTienda]);
                return $notFoundResource->response()->setStatusCode(404);
            }

            $usuarios = $this->usuarioRepository->listarCajerosPorTienda($idTienda);
            if (!$usuarios){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Cajeros de la tienda no encontrados');
                $notFoundResource->notFound(['idTienda' => $idTienda]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            foreach ($usuarios as $key => $usuario) {
                $this->usuarioRepository->loadTipoUsuarioRelationship($usuario);
                //$this->usuarioRepository->loadTiendasCargoJefeTiendaRelationship($usuario);
                
                
            }
            
            $usuariosResource =  new UsuariosResource($usuarios); 
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Listado de cajeros (de ventas y de devoluciones) de determinada tienda');  
            $responseResourse->body($usuariosResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }

    }
}

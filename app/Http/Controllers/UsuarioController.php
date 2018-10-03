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
            
            $tiposUsuarioResource =  new UsuariosResource($this->usuarioRepository->listarUsuarios()); 
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
        //
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
        //
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

            $this->usuarioRepository->setUsuarioModel($usuario);
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
}

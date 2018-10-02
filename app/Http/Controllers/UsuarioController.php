<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UsuarioResource;
use App\Http\Resources\UsuariosResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Models\Usuario;
use App\Repositories\UsuarioRepository;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\UsuarioRelationshipResource;


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
        //
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
                            'direccion' => 'required']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            DB::beginTransaction();
            $this->usuarioRepository->guarda($usuarioData->all());
            $usuarioCreated = $this->usuarioRepository->obtenerModelo(); 
            DB::commit();
            return "exito";
            
            // $usuarioResource =  new UsuarioResource($usuarioCreated);
            // return $usuarioResource;
            // $responseResourse = new ResponseResource(null);
            // $responseResourse->title('Usuario creado exitosamente');       
            // $responseResourse->body($usuarioResource);       
            // return $responseResourse;
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
    public function update(Request $request, $id)
    {
        //
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
}

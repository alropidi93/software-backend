<?php

namespace App\Http\Controllers;
use App\Http\Resources\TipoUsuarioResource;
use App\Http\Resources\TiposUsuarioResource;

use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Models\TipoUsuario;
use App\Repositories\TipoUsuarioRepository;

use Illuminate\Http\Request;

class TipoUsuarioController extends Controller
{

    protected $tipoUsuarioRepository;

    public function __construct(TipoUsuarioRepository $tipoUsuarioRepository){
        TipoUsuarioResource::withoutWrapping();
        $this->tipoUsuarioRepository = $tipoUsuarioRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $tiposUsuarioResource =  new TiposUsuarioResource($this->tipoUsuarioRepository->obtenerTodos());  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Listar tipos de usuario');  
            $responseResourse->body($tiposUsuarioResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }

    
   
}

<?php

namespace App\Http\Controllers;
use App\Http\Resources\UnidadMedidaResource;
use App\Http\Resources\UnidadesMedidaResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Models\UnidadMedida;
use App\Repositories\UnidadMedidaRepository;

use Illuminate\Http\Request;

class UnidadMedidaController extends Controller
{

    protected $unidadMedidaRepository;

    public function __construct(UnidadMedidaRepository $unidadMedidaRepository){
        UnidadMedidaResource::withoutWrapping();
        $this->unidadMedidaRepository = $unidadMedidaRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $unidadesMedidaResource =  new UnidadesMedidaResource($this->unidadMedidaRepository->obtenerTodos());  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Listar unidades de medida');  
            $responseResourse->body($unidadesMedidaResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }

    
   
}

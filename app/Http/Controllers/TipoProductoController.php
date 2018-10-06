<?php

namespace App\Http\Controllers;
use App\Http\Resources\TipoProductoResource;
use App\Http\Resources\TiposProductoResource;

use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Models\TipoProducto;
use App\Repositories\TipoProductoRepository;

use Illuminate\Http\Request;

class TipoProductoController extends Controller
{

    protected $tipoProductoRepository;

    public function __construct(TipoProductoRepository $tipoProductoRepository){
        TipoProductoResource::withoutWrapping();
        $this->tipoProductoRepository = $tipoProductoRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $tiposProductoResource =  new TiposProductoResource($this->tipoProductoRepository->obtenerTodos());  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Listar tipos de producto');  
            $responseResourse->body($tiposProductoResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }

    
   
}

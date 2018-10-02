<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnidadMedidaController extends Controller
{
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
            $responseResourse->title('Lista de unidades de medida');  
            $responseResourse->body($unidadMedidasResource);
            return $responseResourse;
        }
        catch(\Exception $e){
         
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }

    
   
}

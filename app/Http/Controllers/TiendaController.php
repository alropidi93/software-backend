<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tienda;
use App\Repositories\TiendaRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\TiendaResource;


class TiendaController extends Controller {

    protected $tiendaRepository;

    public function __construct(TiendaRepository $tiendaRepository){
        $this->tiendaRepository = $tiendaRepository;
    }
  
    public function show($tienda) //Tienda $tienda
    {
   
        $this->tiendaRepository->setId($tienda);
        $tienda = $this->tiendaRepository->obtenerModelo();
        TiendaResource::withoutWrapping();
        return response()->json(['status' => true,  'body'=> new TiendaResource($tienda)],200);
    }

  



 
}

<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
class ProductoService {
    
    public function obtenerIdArray($productoCollection){
        return $productoCollection->pluck('id')->all();
        
    }

    
}
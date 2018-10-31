<?php
namespace App\Services;
use App\Models\Almacen;
use App\Models\Producto;
	
class AlmacenService {
    
    public function obtenerAlmacenCercano($almacen, $rank){
        //rank es mayor a 1
        $almacenesDisponibles = Almacen::where('deleted',false)->where('nombre',"<>",'Central')->orderBy('id')->get();
        $num_elements=  count($almacenesDisponibles)-1;
        if ($rank>$num_elements){
            $rank = $num_elements;
        }

        $list_ids = $almacenesDisponibles->pluck('id');
        $list_ids =  $list_ids->all();
        $pos = array_search($almacen->id, $list_ids);
        $pos =($pos+$rank)%($num_elements+1);
    
        return $almacenesDisponibles[$pos];

    }
    
}
<?php
namespace App\Services;
use App\Models\Almacen;
use App\Models\Producto;
use Illuminate\Support\Facades\Log;
class AlmacenService {
    
    public function obtenerAlmacenCercano($almacen, $rank){
        
        $almacenesDisponibles = Almacen::where('deleted',false)->where('nombre',"<>",'Central')->orderBy('id')->whereHas('tienda',function($q){
            $q->where('tienda.deleted',false);
        })->get();
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
    public function obtenerAlmacenCercanoConStock($almacen, $rank,$lineasTransferencia){
        
        $almacenesDisponibles = Almacen::where('deleted',false)->where('nombre',"<>",'Central')->orderBy('id')->whereHas('tienda',function($q){
            $q->where('tienda.deleted',false);
        })->get();

        $almacenesDisponibles = $almacenesDisponibles->filter(function ($alm, $key) use ($lineasTransferencia,$almacen) {
            $aceptado = true;
            if ($alm['id']==$almacen->id) return true;  
            foreach ($lineasTransferencia as $key => $lt) {
                $productos= $alm->productos()->where(function($q) use($lt){
                        $q->where('producto.id',$lt->idProducto)
                        ->where('productoxalmacen.cantidad','>',$lt->cantidad)
                        ->where('productoxalmacen.deleted',false);
                    })
                    ->join('tipoStock', 'tipoStock.id', '=', 'productoxalmacen.idTipoStock')
                    ->where('tipoStock.key',1)->where('tipoStock.deleted',false)
                    ->get();
                $condition =  count($productos) > 0;
                
                $aceptado = $aceptado && $condition;
                if(!$aceptado) break;
            }
            
            return $aceptado;
        });

        $almacenesDisponibles = $almacenesDisponibles->values();    


        
        $num_elements=  count($almacenesDisponibles)-1;
        if ($rank > $num_elements){
            $rank = $num_elements;
        }
        $list_ids = $almacenesDisponibles->pluck('id');
        $list_ids =  $list_ids->all();
        $pos = array_search($almacen->id, $list_ids);
        $pos =($pos+$rank)%($num_elements+1);
    
        return $almacenesDisponibles[$pos];

    }

    public function obtenerAlmacenCercanoConStockAlt($almacen, $rank,$lineasObj){
        
        $almacenesDisponibles = Almacen::where('deleted',false)->where('nombre',"<>",'Central')->orderBy('id')->whereHas('tienda',function($q){
            $q->where('tienda.deleted',false);
        })->get();

        $almacenesDisponibles = $almacenesDisponibles->filter(function ($alm, $key) use ($lineasObj,$almacen) {
            $aceptado = true;
            if ($alm['id']==$almacen->id) return true;  
            foreach ($lineasObj as $key => $lt) {
                $productos= $alm->productos()->where(function($q) use($lt){
                        $q->where('producto.id',$lt['idProducto'])
                        ->where('productoxalmacen.cantidad','>',$lt['cantidad'])
                        ->where('productoxalmacen.deleted',false);
                    })
                    ->join('tipoStock', 'tipoStock.id', '=', 'productoxalmacen.idTipoStock')
                    ->where('tipoStock.key',1)->where('tipoStock.deleted',false)
                    ->get();
                $condition =  count($productos) > 0;
                
                $aceptado = $aceptado && $condition;
                if(!$aceptado) break;
            }
            
            return $aceptado;
        });

        $almacenesDisponibles = $almacenesDisponibles->values();    
       
        $num_elements=  count($almacenesDisponibles)-1;
        if ($rank > $num_elements){
            $rank = $num_elements;
        }
        $list_ids = $almacenesDisponibles->pluck('id');
        $list_ids =  $list_ids->all();
        $pos = array_search($almacen->id, $list_ids);
        $pos =($pos+$rank)%($num_elements+1);
    
        return $almacenesDisponibles[$pos];

    }

    public function tieneStock($almacen,$lineasTransferencia){
        
        $aceptado =  true;
        foreach ($lineasTransferencia as $key => $lt) {
            $productos= $almacen->productos()->where(function($q) use($lt){
                    $q->where('producto.id',$lt['idProducto'])
                    ->where('productoxalmacen.cantidad','>',$lt['cantidad'])
                    ->where('productoxalmacen.deleted',false);
                })
                ->join('tipoStock', 'tipoStock.id', '=', 'productoxalmacen.idTipoStock')
                ->where('tipoStock.key',1)->where('tipoStock.deleted',false)
                ->get();
            $condition =  count($productos) > 0;
            $aceptado = $condition && $aceptado;
            if (!$aceptado) break;
        }
        return $aceptado;
    }

    
}
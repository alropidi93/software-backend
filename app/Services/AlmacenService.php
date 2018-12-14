<?php
namespace App\Services;
use App\Models\Almacen;
use App\Models\Producto;
use Illuminate\Support\Facades\Log;
class AlmacenService {
    
    public function obtenerAlmacenCercano($almacen, $rank){
        if($almacen->esCentral()) return null;
        $almacenesDisponibles = Almacen::where('deleted',false)->where('nombre',"<>",'Central')->orderBy('id')->whereHas('tienda',function($q){
            $q->where('tienda.deleted',false);
        })->get(); //obtengo los almacenes candidatos al $rank-avo almacen más cercano, incluido el mismo

        $num_elements = count($almacenesDisponibles)-1; //obtengo el numero de almacenes disponibles
        if ($rank>$num_elements){
            $rank = $num_elements;
        }
        //$rank no tiene puede ser mayor al numero de elementos a lo mucho puede ser el numero de elementos
        $list_ids = $almacenesDisponibles->pluck('id');
        $list_ids =  $list_ids->all();//obtengo un arreglo de los ids disponibles
        $posOrigen = array_search($almacen->id, $list_ids);//obtengo la posicion en el array del almacen origen


        $almacenFinal = null;
        $distanciaMenor=99999;
        foreach ($almacenesDiponibles as $key => $almacenDisponible) {
            if ($almacen->id==$almacenDisponible) continue;
            $distancia = $this->distanciaAlmacenes($almacen,$almacenDisponible);
            if($distanciaMenor > $distancia){
                $distanciaMenor = $distancia;
                $almacenFinal = $almacenDestino;
            }
        }
        if(!$almacenFinal) return null;


        //$pos =($pos+$rank)%($num_elements+1);//obtengo la posicion del siguiente almacen más cercano
        $posFinal = array_search($almacenFinal->id, $list_ids);
        return $almacenesDisponibles[$pos];

    }

    public function obtenerAlmacenCercanoConStock($almacen, $rank,$lineasTransferencia){
        if($almacen->esCentral()) return null;
        
        $almacenesDisponibles = Almacen::where('deleted',false)->where('nombre',"<>",'Central')->orderBy('id')->whereHas('tienda',function($q){
            $q->where('tienda.deleted',false);
        })->get();
        $almacenesDisponibles = $almacenesDisponibles->filter(function ($alm, $key) use ($lineasTransferencia,$almacen) {
            $aceptado = true ;
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
            return null;
        }
        $list_ids = $almacenesDisponibles->pluck('id');
        $list_ids =  $list_ids->all();
        $posOrigen = array_search($almacen->id, $list_ids);

        $almacenFinal = null;
        $distanciaMenor=99999;
        foreach ($almacenesDisponibles as $key => $almacenDisponible) {
            Log::info("Vuelta");
            Log::info($key);
            if ($almacen->id==$almacenDisponible->id) continue;
            $distancia = $this->distanciaAlmacenes($almacen,$almacenDisponible);
            Log::info($distancia);
            if($distanciaMenor > $distancia){
                $distanciaMenor = $distancia;
                $almacenFinal = $almacenDisponible;
            }
        }
        Log::info("Distancia menor");
        Log::info($distanciaMenor);
        if(!$almacenFinal) return null;

        
        //$pos =($pos+$rank)%($num_elements+1);//obtengo la posicion del siguiente almacen más cercano
        $posFinal = array_search($almacenFinal->id, $list_ids);
    
        return $almacenesDisponibles[$posFinal];

    }

    protected function distanciaAlmacenes($almacenO, $almacenF){
        Log::info(json_encode($almacenO));
        Log::info(json_encode($almacenF));
        
        $tiendaO = $almacenO->tienda;
        $tiendaF = $almacenF->tienda;
        Log::info(json_encode($tiendaO));
        Log::info(json_encode($tiendaF));
        if (($tiendaO->latitud == $tiendaF->latitud) && ($tiendaO->longitud == $tiendaF->longitud)) {
            return 0;
        }
        else{
            $theta = $tiendaO->longitud - $tiendaF->longitud;
            $dist = sin(deg2rad($tiendaO->latitud)) * sin(deg2rad($tiendaF->latitud)) +  cos(deg2rad($tiendaO->latitud)) * cos(deg2rad($tiendaF->latitud)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            return $dist;
        }


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
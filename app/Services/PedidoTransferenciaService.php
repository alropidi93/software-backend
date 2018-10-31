<?php
namespace App\Services;
use App\Models\PedidoTransferencia;
use App\Models\LineaPedidoTransferencia;
	
class PedidoTransferenciaService {
    

    
    public function nuevaInstancia($pedidoTransferencia, $nuevaFase)
    {
        $array = json_decode($pedidoTransferencia, true);
        unset($array['id']);
        unset($array['created_at']);
        unset($array['updated_at']);
        $array['fase']=$nuevaFase;
        return $array;
     
        
        
    }

    public function nuevasLineasPedidoTransferencia($lineasPedidoTransferencia)
    {
        $lineasPedidoTransferencia->each(function ($linea, $key) {
            unset($linea['id']);
            unset($linea['idPedidoTransferencia']);
            unset($linea['deleted']);
            unset($linea['created_at']);
            unset($linea['updated_at']);
        });

        return $lineasPedidoTransferencia->all();
     
        
        
    }

   
    
}
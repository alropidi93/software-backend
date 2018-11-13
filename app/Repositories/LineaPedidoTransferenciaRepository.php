<?php
namespace App\Repositories;

use App\Models\LineaPedidoTransferencia;
use App\Models\LineaSolicitudCompra;

	
class LineaPedidoTransferenciaRepository extends BaseRepository{

    protected $lineaSolicitudCompra;
    protected $lineaPedidoTransferencia;

    public function __construct(LineaPedidoTransferencia $lineaPedidoTransferencia,LineaSolicitudCompra $lineaSolicitudCompra){
        $this->model = $lineaPedidoTransferencia;
        $this->lineaSolicitudCompra = $lineaSolicitudCompra;

    }

    public function attachLineaSolicitudTransferencia($lineaSolicitudCompra=null){
        if (!$lineaSolicitudCompra){
         
            
            $this->model->lineaSolicitudCompra()->associate($this->lineaSolicitudCompra);
        }
        else{
             
            
            $this->model->lineaSolicitudCompra()->associate($lineaSolicitudCompra);
        }
        $this->model->save();
        
    }

    
  
}
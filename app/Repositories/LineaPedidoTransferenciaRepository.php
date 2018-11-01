<?php
namespace App\Repositories;

use App\Models\LineaPedidoTransferencia;
use App\Models\LineaSolicitudCompra;

	
class LineaPedidoTransferenciaRepository extends BaseRepository{

    protected $lineaSolicitudCompra;


    public function __construct(LineaSolicitudCompra $lineaSolicitudCompra){
        $this->model = $lineaSolicitudCompra;
        $this->lineaSolicitudCompra = $lineaSolicitudCompra;

    }

    public function attachLineaSolicitudTransferencia($lineaSolicitudCompra=null){
        if (!$lineaSolicitudCompra){
            $this->model->lineaSolicitudCompra()->associate($this->lineaSolicitudCompra);
        }
        else{
            $this->model->lineaSolicitudCompra()->associate($lineaSolicitudCompra);
        }
        
    }
  
}
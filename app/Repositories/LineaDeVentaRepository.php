<?php
namespace App\Repositories;

use App\Models\LineaDeVenta;

	
class LineaDeVentaRepository extends BaseRepository{
    protected $lineaDeVenta;

    public function __construct(LineaDeVenta $lineaDeVenta){
        $this->model = $lineaDeVenta;
        $this->lineaDeVenta = $lineaDeVenta;

    }

    public function attachLineaDeVenta($lineaDeVenta=null){
        if (!$lineaDeVenta){
            $this->model->lineaDeVenta()->associate($this->lineaDeVenta);
        }
        else{
            $this->model->lineaDeVenta()->associate($lineaDeVenta);
        }
        
    }
  
}
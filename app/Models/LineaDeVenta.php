<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineaDeVenta extends Model
{
    protected $table = 'lineaDeVenta';
    public $timestamps = true;
  
    protected $fillable = [
      'id',
      'idProducto',
      'cantidad',
      'subtotalConIgv',
      'precioUnitarioConIgv',
      'idComprobantePago',
      'idCotizacion',
      'idDevolucion',
      'deleted'
    ];
    public function comprobantePago() {
        return $this->belongsTo('App\Models\ComprobantePago','idComprobantePago','id');
    }

    public function devolucion() {
        return $this->belongsTo('App\Models\Devolucion','idDevolucion','id');
    }

    public function cotizacion(){
        return $this->belongsTo('App\Models\Cotizacion', 'idCotizacion', 'id');
    }

    public function producto() {
        return $this->belongsTo('App\Models\Producto','idProducto','id');
    }
}
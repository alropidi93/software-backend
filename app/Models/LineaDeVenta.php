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
      'idComprobantePago',
      'idCotizacion',
      'deleted'
    ];
    public function comprobantePago() {
        return $this->belongsTo('App\Models\ComprobantePago','idComprobantePago','id');
    }

    public function producto() {
        return $this->belongsTo('App\Models\Producto','idProducto','id');
    }
}
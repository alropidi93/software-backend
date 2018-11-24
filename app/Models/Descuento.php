<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Descuento extends Model
{
    protected $table = 'descuento';   
    public $timestamps = true;

    protected $fillable = [
      'id',
      'idProducto',
      'idCategoria',
      'es2x1',
      'porcentaje',
      'fechaIni',
      'fechaFin',
      'deleted'
    ];

    public function producto() {
      return $this->belongsTo('App\Models\Producto','idComprobantePago','id');
    }

    public function categoria() {
      return $this->belongsTo('App\Models\Categoria','idCliente','id');
    }
}

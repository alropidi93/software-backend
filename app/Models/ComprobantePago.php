<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComprobantePago extends Model
{
    protected $table = 'comprobantePago';
    public $timestamps = true;
  
    protected $fillable = [
      'id',
      'idTienda',
      'idCajero',
      'subtotal',
      'entrega',
      'fechaEnt',
      'entregado',
      'deleted'     
    ];

    public function tienda(){
      return $this->belongsTo('App\Models\Tienda', 'idTienda', 'id');
    }

    public function usuario() {
      return $this->belongsTo('App\Models\Usuario','idCajero','idPersonaNatural');
    }
      
    public function boleta() {
      return $this->hasOne('App\Models\Boleta','idComprobantePago','id');
    }

    public function factura() {
      return $this->hasOne('App\Models\Factura','idComprobantePago','id');
    }

    public function devolucion() {
      return $this->hasOne('App\Models\Devolucion','idComprobantePago','id');
    }

    public function lineasDeVenta() {
      return $this->hasMany('App\Models\LineaDeVenta','idComprobantePago','id');
    } 
}

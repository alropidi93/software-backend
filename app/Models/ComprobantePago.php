<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComprobantePago extends Model
{
    protected $table = 'comprobantePago';
    public $timestamps = true;
  
    protected $fillable = [
      'id',
      'idCajero',
      'subtotal',
      'entrega',
      'fechaEnt',
      'deleted'     
    ];

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

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

    public function lineasVenta() {
      return $this->hasMany('App\Models\LineaVenta','idComprobantePago','id');
    } 
}

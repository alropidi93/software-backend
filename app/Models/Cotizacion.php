<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    protected $table = 'cotizacion';
    public $timestamps = true;
  
    protected $fillable = [
      'id',
      'idCajero',
      'subtotal',
      'nombre',
      'documento',
      'deleted'     
    ];

    public function usuario() {
      return $this->belongsTo('App\Models\Usuario','idCajero','idPersonaNatural');
    }
      
    
    public function lineasDeVenta() {
      return $this->hasMany('App\Models\LineaDeVenta','idCotizacion','id'); //TAMARE LE PUSE EL ID EQUIVOCADO
    } 
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tienda extends Model
{
    protected $table = 'tienda';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'nombre',
      'distrito',
      'ubicacion',
      'direccion',
      'telefono',
      'deleted',
      'idJefeTienda',
      'idJefeAlmacen'
    ];

    public function jefeDeTienda() {
      return $this->belongsTo('App\Models\Usuario','idJefeTienda','idPersonaNatural');
    }

    public function jefeDeAlmacen() {
      return $this->belongsTo('App\Models\Usuario','idJefeAlmacen','idPersonaNatural');
    }

    public function trabajadores(){
      return $this->hasMany('App\Models\Usuario','idTienda','id');
    }

  
  
  
    
  
}

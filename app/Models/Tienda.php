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
      'latitud',
      'longitud',
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
      return $this->belongsToMany('App\Models\Usuario','usuarioxtienda',
        'idTienda','idUsuario')->withPivot('deleted','miembroPrincipal','created_at','updated_at');
    }

    public function almacen() {
      return $this->hasOne('App\Models\Almacen','idTienda','id');
    }  
}

<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UsuarioXTienda extends Pivot
{

    protected $table = 'usuarioxtienda';
    public $timestamps = true;

    public $incrementing = false;
    protected $primaryKey = ['idUsuario','idTienda'];
    protected $fillable = ['idUsuario','idTienda','miembroPrincipal','deleted'];
    
    public function __contruct(){
      $this->usuario;
    }

    public function usuario(){
        return $this->belongsTo('App\Models\Usuario','idUsuario','idPersonaNatural');
    }

    public function tienda(){
      return $this->belongsTo('App\Models\Tienda','idTienda','id');
  }

   



}
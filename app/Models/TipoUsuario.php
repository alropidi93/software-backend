<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoUsuario extends Model
{
    protected $table = 'tipoUsuario';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'nombre',
      'key',
      'deleted'
    ];

    public function usuarios(){
      return $this->hasMany('App\Models\Usuario','idTipoUsuario','id');
    }

    public function esJefeDeTienda(){
        
      return $this->key==1;
    }

    public function esJefeDeAlmacen(){
        
      return $this->key==3;
    }
}

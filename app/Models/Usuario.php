<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'idPersonaNatural'; 
    public $timestamps = true;
    public $incrementing = false;
  
    protected $fillable = [
      'idPersonaNatural',
      'password',
      'idTipoUsuario',
      'idTienda',
      'deleted'
    ];

    public function personaNatural() {
        return $this->belongsTo('App\Models\PersonaNatural','idPersonaNatural','id');
    }

    public function tipoUsuario() {
        return $this->belongsTo('App\Models\TipoUsuario','idTipoUsuario','id');
    }

    public function tiendaCargoJefeTienda() {
        return $this->hasMany('App\Models\Tienda','idJefeTienda','idPersonaNatural');
      }
  
    public function tiendaCargoJefeAlmacen() {
        return $this->hasMany('App\Models\Tienda','idJefeAlmacen','idPersonaNatural');
    }

    public function esJefeDeTienda(){
        
        return $this->tipoUsuario()->where('key',1)->where('deleted',false)->exists();
    }

    public function esJefeDeTiendaAsignado(){
        if ($this->esJefeDeTienda() ){
            return $this->tiendaCargoJefeTienda()->exists();
        }
        return false;
    }

    public function esJefeDeAlmacen(){
        
        return $this->tipoUsuario()->where('key',3)->where('deleted',false)->exists();
    }

    public function esJefeDeAlmacenAsignado(){
        
        if ($this->esJefeDeAlmacen()){
            return $this->tiendaCargoJefeAlmacen()->exists();
        }
        return false;
    }
}

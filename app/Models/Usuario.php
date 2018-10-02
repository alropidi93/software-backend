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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'userId',
      'userPassword',
      'idTipoUsuario',
      'idTienda',
      'deleted'
    ];
}

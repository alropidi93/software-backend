<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $table = 'movimiento';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'descripcion',
      'fecha',
      'idUsuario',
      'deleted'
    ];
}

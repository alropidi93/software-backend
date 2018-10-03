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
      'deleted'
    ];
}

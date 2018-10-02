<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDeUsuario extends Model
{
    protected $table = 'tipoDeUsuario';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'nombre',
      'deleted'
    ];
}

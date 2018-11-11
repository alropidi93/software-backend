<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonaJuridica extends Model
{
    protected $table = 'personaJuridica';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'ruc',
      'email',
      'razonSocial',
      'direccion',
      'telefono',
      'deleted',
    ];

}
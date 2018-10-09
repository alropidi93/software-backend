<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonaNatural extends Model
{
    protected $table = 'personaNatural';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'nombre',
      'apellidos',
      'genero',
      'email',
      'fechaNac',
      'direccion',
      'deleted',
      'dni'
    ];

    public function usuario() {
      return $this->hasOne('App\Models\Usuario','idPersonaNatural','id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
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
      'deleted'
    ];

    public function company() {
      return $this->hasOne('App\Models\Usuario','project_id','id');
    }
}

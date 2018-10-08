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

    //un movimiento esta asociado a un usuario por medio del campo idUsuario, belongsTo es lo que se usa para representar "Esta asociado a", "Pertenece a"
    public function usuario(){
      /*
      *se coloca el modelo a relacionar, el id ques clave foranea que se refiere al id del 
      *modelo al relacionar:idUsuario, y el nombre de la clave pimeria del modelo a relacionar: id
      */
      return $this->belongsTo('App\Models\Usuario','idUsuario','id');
    }
}

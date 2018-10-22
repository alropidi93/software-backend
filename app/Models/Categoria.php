<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categoria';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'nombre',
      'descripcion',
      'deleted',
      
    ];
    /*PARTE DE TUTORIAL PARA RELATIONSHIPS */
    //un movimiento esta asociado a un usuario por medio del campo idUsuario, belongsTo es lo que se usa para representar "Esta asociado a", "Pertenece a"
    public function productos(){
      /*
      *se coloca el modelo a relacionar, el id ques clave foranea que se refiere al id del 
      *modelo al relacionar:idUsuario, y el nombre de la clave pimria del modelo a relacionar (usuario): idPersonaNatural
      */
      return $this->hasMany('App\Models\Producto','idProducto','id');
    }
    /*FIN DE PARTE DE TUTORIAL PARA RELATIONSHIPS */
}

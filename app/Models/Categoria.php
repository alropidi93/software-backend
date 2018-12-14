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

    public function descuentosCategoriaTc(){
      return $this->belongsToMany('App\Models\Descuento','categoriaxtiendaxdescuento',
        'idCategoria','idDescuento')->using('App\Models\CategoriaXTiendaXDescuento')
        ->withPivot('idTienda', 'deleted','created_at','updated_at');
  }
    
}

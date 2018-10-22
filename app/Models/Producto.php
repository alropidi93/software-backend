<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'producto';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'nombre',
      'stockMin',
      'descripcion',
      'idTipoProducto',
      'idUnidadMedida',
      'categoria',
      'idCategoria',
      'precio',
      'deleted'
    ];

    public function tipoProducto() {
        return $this->belongsTo('App\Models\TipoProducto','idTipoProducto','id');
    }

    public function unidadMedida() {
        return $this->belongsTo('App\Models\UnidadMedida','idUnidadMedida','id');
    }
    public function categoria() {
        return $this->belongsTo('App\Models\Categoria','idCategoria','id');
    }

    public function proveedores(){
        return $this->belongsToMany('App\Models\Proveedor','productoxproveedor',
        'idProducto','idProveedor')->withPivot('deleted','created_at','updated_at');
    }
}

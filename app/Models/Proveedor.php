<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedor';
    public $timestamps = true;
  
    protected $fillable = [
      'id',
      'contacto',
      'ruc',
      'razonSocial',
      'direccion',
      'email',
      'telefono',
      'deleted'
    ];

    public function tieneExactamenteProductos($id_array){
      return $this->productos()->where('producto.deleted',false)->whereIn('id',$id_array)->count()==count($id_array);
    }

    public function productos(){
        return $this->belongsToMany('App\Models\Producto','productoxproveedor',
          'idProveedor','idProducto')->withPivot('deleted','precio','created_at','updated_at');
    }
}

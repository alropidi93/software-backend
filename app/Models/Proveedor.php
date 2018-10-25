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
     
      'deleted'
    ];

    public function productos(){
        return $this->belongsToMany('App\Models\Producto','productoxproveedor',
          'idProveedor','idProducto')->withPivot('deleted','created_at','updated_at');
    }

}

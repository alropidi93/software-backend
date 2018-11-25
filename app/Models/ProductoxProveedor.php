<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductoXProveedor extends Pivot
{

    protected $table = 'productoxproveedor';
    public $timestamps = true;

    public $incrementing = false;
    protected $primaryKey = ['idProducto', 'idProveedor'];
    protected $fillable = ['idProducto','idAlmacen', 'deleted'];
    
    public function __contruct(){
      
    }
    
    public function proveedor(){
      return $this->belongsTo('App\Models\Proveedor','idProveedor','id');
  }

    public function producto(){
      return $this->belongsTo('App\Models\Producto','idProducto','id');
  }

   
}
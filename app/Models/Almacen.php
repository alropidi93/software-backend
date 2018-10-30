<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table = 'almacen';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'nombre',
      'idTienda',
      'deleted'
 
      
    ];


    public function tienda() {
        return $this->belongsTo('App\Models\Tienda','idTienda','id');
    }

    public function productoxalmacenes() {
        return $this->hasMany('App\Models\ProductoXAlmacen','idAlmacen','id');
    }

    public function productos(){
        return $this->belongsToMany('App\Models\Producto','productoxalmacen',
          'idAlmacen','idProducto')->using('App\Models\ProductoXAlmacen')->withPivot('idTipoStock','cantidad','deleted','created_at','updated_at');
    }

    public function tipoStocks(){
        return $this->belongsToMany('App\Models\TipoStock','productoxalmacen',
          'idAlmacen','idTipoStock')->using('App\Models\ProductoXAlmacen')->withPivot('idProducto','cantidad','deleted','created_at','updated_at');
    }

    // public function newPivot(Model $parent, array $attributes, $table, $exists)
    // {
    //     if ($parent instanceof Producto) {
    //         return new ProductoXAlmacen($parent, $attributes, $table, $exists);
    //     }
    
    //     return parent::newPivot($parent, $attributes, $table, $exists);
    // }
    
}
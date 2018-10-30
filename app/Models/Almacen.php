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

    public function productos(){
        return $this->belongsToMany('App\Models\Producto','productoxalmacen',
          'idAlmacen','idProducto')->using('App\Models\ProductoXAlmacen')->withPivot('idTipoStock','cantidad','deleted','created_at','updated_at');
    }

    public function tipoStocks(){
        return $this->belongsToMany('App\Models\TipoStock','productoxalmacen',
          'idAlmacen','idTipoStock')->using('App\Models\ProductoXAlmacen')->withPivot('idProducto','cantidad','deleted','created_at','updated_at');
    }

    // public function posts() //tipoStock
    // {
    //     return $this->hasManyThrough(
    //         'App\Models\TipoStock',
    //         'App\Model\ProductoXAlmacen',
    //         'idAlmacen', // Foreign key on users table...
    //         'user_id', // Foreign key on posts table...
    //         'id', // Local key on countries table...
    //         'id' // Local key on users table...
    //     );
    // }
    
    
}
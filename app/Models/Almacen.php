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
          'idAlmacen','idProducto')->withPivot('idTipoStock','cantidad','deleted','created_at','updated_at');
    }

    public function tipoStocks(){
        return $this->belongsToMany('App\Models\TipoStock','productoxalmacen',
          'idAlmacen','idTipoStock')->withPivot('idProducto','cantidad','deleted','created_at','updated_at');
    }
    
    
}
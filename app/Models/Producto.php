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
        'idCategoria',
        'idDescuento',
        'precio',
        'habilitado',
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
        'idProducto','idProveedor')->withPivot('deleted','precio','created_at','updated_at');
    }

    public function almacenes(){
        return $this->belongsToMany('App\Models\Almacen','productoxalmacen',
        'idProducto','idAlmacen')->using('App\Models\ProductoXAlmacen')
        ->withPivot('idTipoStock','cantidad','precio','deleted','created_at','updated_at');
    }
    

    public function tipoStocks(){
        return $this->belongsToMany('App\Models\TipoStock','productoxalmacen',
          'idProducto','idTipoStock')->using('App\Models\ProductoXAlmacen')->withPivot('idAlmacen','cantidad','deleted','created_at','updated_at');
    }

    public function productosxalmacenes()
    {
        return $this->hasMany('App\Models\ProductoXAlmacen', 'idProducto', 'id');
    }

    // public function movimientoTipoStock(){
    //     return $this->belongsTo('App\Models\MovimientoTipoStock', 'idProducto', 'id');
    // }
    // public function descuento(){
    //     return $this->hasOne('App\Models\Descuento', 'id', 'idDescuento');
    // }

    public function descuentos() //1 pruducto puede tener muchos descuentos
    {
        return $this->hasMany('App\Models\Descuento', 'idProducto', 'id');
    }

    public function descuentosTc(){
        return $this->belongsToMany('App\Models\Descuento','productoxdescuento',
          'idProducto','idDescuento')->using('App\Models\ProductoXDescuento')
          ->withPivot('idTienda', 'deleted','created_at','updated_at');
    }
 

    
}

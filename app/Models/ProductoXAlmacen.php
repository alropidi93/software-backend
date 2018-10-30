<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductoXAlmacen extends Pivot
{

  protected $table = 'productoxalmacen';
  public $timestamps = true;
 
  public $incrementing = false;
  protected $primaryKey = ['idProducto','idAlmacen'];
  protected $fillable = ['idProducto','idAlmacen','idTipoStock','cantidad', 'deleted'];

  public function tipoStock(){
      return $this->belongsTo('App\Models\TipoStock','idTipoStock','id');
  }



}
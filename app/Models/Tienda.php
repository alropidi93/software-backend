<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tienda extends Model
{
    protected $table = 'tienda';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'nombre',
      'distrito',
      'ubicacion',
      'direccion',
      'telefono',
      'deleted'
    ];
  
  
    // public function project() {
    //   return $this->belongsTo('App\Http\Models\Project','project_id','id');
    // }
  
}

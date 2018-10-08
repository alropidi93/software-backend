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

}

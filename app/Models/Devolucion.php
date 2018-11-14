<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    protected $table = 'devolucion';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'idComprobantePago',
        'descripcion',
        'deleted'   
    ];

    public function comprobantePago() {
        return $this->belongsTo('App\Models\ComprobantePago','idComprobantePago','id');
    }
}

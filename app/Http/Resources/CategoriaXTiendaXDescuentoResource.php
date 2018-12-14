<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaXTiendaXDescuento extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            
            'type'          => 'Categoria_Descuento',
            'idTienda'      => $this->idTienda,
            'idCategoria'   => $this->idCategoria,
            'idDescuento'   => $this->idDescuento,
            'attributes'    => [
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}

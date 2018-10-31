<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductoXAlmacenResource extends JsonResource
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
            
            'type'          => 'Producto_Almacen',
            'idProducto'            => $this->idProducto,
            'idAlmacen'            => $this->idAlmacen,
            'idTipoStock'            => $this->idTipoStock,
            'attributes'    => [
                'cantidad' => $this->cantidad,
                
              
                'tipoStock' => new TipoStockResource($this->whenLoaded('tipoStock')),
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}

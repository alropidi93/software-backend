<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
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
            
            'type'          => 'Producto',
            'id'            => $this->id,
            'attributes'    => [
                'nombre' => $this->nombre,
                'stockMin' => $this->stockMin,
                'descripcion' => $this->descripcion,
                'tipoProducto' => new TipoProductoResource($this->whenLoaded('tipoProducto')),
                'unidadMedida' => new UnidadMedidaResource($this->whenLoaded('unidadMedida')),
                'proveedores' => new ProveedoresResource($this->whenLoaded('proveedores')),
                'categoria' => $this->categoria,
                'precio' => $this->precio,
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}

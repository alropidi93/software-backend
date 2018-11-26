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
                'categoria' => new CategoriaResource($this->whenLoaded('categoria')),
                'almacenes'=>new AlmacenesResource($this->whenLoaded('almacenes')),
                'productoxalmacen'=> $this->whenLoaded('almacen.pivot'),
                
                'precio' => $this->precio,
                'habilitado' => $this->habilitado,
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AlmacenResource extends JsonResource
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
            
            'type'          => 'Almacen',
            'id'            => $this->id,
            'attributes'    => [
                
                'idTienda' => $this->idTienda,
                'nombre' => $this->nombre,
                'distrito' => $this->distrito,
                'productoxalmacen'=> new ProductoXAlmacenResource($this->whenLoaded('pivot')),
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'productos' => new ProductosResource($this->whenLoaded('productos'))
            ],
        ];
    }
}

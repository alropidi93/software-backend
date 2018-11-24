<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DescuentoResource extends JsonResource
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
            'type'          => 'Descuento',
            'id'            => $this->id,
            'attributes'    => [
                'producto' => new ProductoResource($this->whenLoaded('producto')),
                'categoria' => new CategoriaResource($this->whenLoaded('categoria')),           
                'es2x1' => $this->es2x1,
                'porcentaje' => $this->porcentaje,
                'fechaIni' => $this->fechaIni,
                'fechaFin' => $this->fechaFin,
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
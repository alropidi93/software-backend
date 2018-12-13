<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LineaDeVentaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request){
        return [
            'type'          => 'Linea de venta',
            'id'            => $this->id,
            'attributes'    => [
                'cantidad' => $this->cantidad,
                'subtotalLinea' => $this->subtotalLinea,
                'producto' => new ProductoResource($this->whenLoaded('producto')),
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
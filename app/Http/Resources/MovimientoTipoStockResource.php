<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovimientoTipoStockResource extends JsonResource
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
            'type'          => 'Movimiento Tipo Stock',
            'id'            => $this->id,
            'attributes'    => [
                'cantidad'=>$this->cantidad,
                'signo'=>$this->signo,
                'producto' =>  new ProductoResource($this->whenLoaded('producto')), 
                'almacen' =>  new AlmacenResource($this->whenLoaded('almacen')),
                'tipoStock' => new TipoStockResource($this->whenLoaded('tipoStock')),
                'usuario' => new UsuarioResource($this->whenLoaded('usuario')),
                'deleted' => $this->deleted,
                'created_at' => $this->created_at
            ],
        ];
    }
}
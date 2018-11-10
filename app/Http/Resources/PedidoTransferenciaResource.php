<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PedidoTransferenciaResource extends JsonResource
{
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request){
        return [
            'type'          => 'Pedido de tranferencia',
            'id'            => $this->id,
            'attributes'    => [
                'usuario' => new TransferenciaResource($this->whenLoaded('usuario')),
                'almaceOrigen' => new AlmacenResource($this->whenLoaded('almacenOrigen')),
                'almacenDestino' => new AlmacenResource($this->whenLoaded('almacenDestino')),
                'almacenDestino2' => new AlmacenResource($this->whenLoaded('almacenDestino2')),
                'descripcion' => $this->descripcion,
                'fase' => $this->fase,
                'aceptoJTO' => $this->aceptoJTO,
                'aceptoJAD' => $this->aceptoJAD,
                'aceptoJTD' => $this->aceptoJTD,
                'deleted' => $this->deleted,
                'lineasPedidoTransferencia' => new LineasPedidoTransferenciaResource($this->whenLoaded('lineasPedidoTransferencia')),
                'transferencia'=> new TransferenciaResource($this->whenLoaded('transferencia')),
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]
        ];
    }
}
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

    public function toArray($request)
    {
        return [
            'type'          => 'Pedido de tranferencia',
            'id'            => $this->id,
            'attributes'    => [
                'usuario' => new TransferenciaResource($this->whenLoaded('usuario')),
                'almaceOrigen' => new TransferenciaResource($this->whenLoaded('almacenOrigen')),
                'almacenDestino' => new TransferenciaResource($this->whenLoaded('almacenDestino')),
                'descripcion' => $this->descripcion,
                'deleted' => $this->deleted,
                'lineasPedidoTransfere' => new LineasSolicitudCompraResource($this->whenLoaded('lineasSolicitudCompra')),
                'transferencia'=> new TransferenciaResource($this->whenLoaded('transferencia')),
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]
        ];
    }
}
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LineaSolicitudCompraResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request){
        return [
            'type'          => 'Linea de solicitud de compra',
            'id'            => $this->id,
            'attributes'    => [
                'cantidad' => $this->cantidad,
                'producto' => new ProductoResource($this->whenLoaded('producto')),
                'lineasPedidoTransferencia' => new LineasPedidoTransferenciaResource($this->whenLoaded('lineasPedidoTransferencia')),
                'proveedor' => new ProveedorResource($this->whenLoaded('proveedor')),
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LineasPedidoTransferenciaResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'listaLineasPedidoTransferencia' => LineasPedidoTransferenciaResource::collection($this->collection),
        ];
    }
}

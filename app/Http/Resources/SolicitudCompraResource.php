<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SolicitudCompraResource extends JsonResource
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
            'type'          => 'Solicitud de compra',
            'id'            => $this->id,
            'attributes'    => [
                'idTienda' => $this->idTienda,
                'fecha' => $this->fecha,
                'lineasSolicitudCompra' => new LineasSolicitudCompraResource($this->whenLoaded('lineasSolicitudCompra')),
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
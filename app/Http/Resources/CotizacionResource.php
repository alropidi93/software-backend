<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CotizacionResource extends JsonResource
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
            'type'          => 'Cotizacion',
            'id'            => $this->id,
            'attributes'    => [
                'nombre' => $this->nombre,
                'documento' => $this->documento,
                'subtotal' => $this->subtotal,
                'lineasDeVenta' => new LineasDeVentaResource($this->whenLoaded('lineasDeVenta')),
                'cajero' => new UsuarioResource($this->whenLoaded('usuario')),
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
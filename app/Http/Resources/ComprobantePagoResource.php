<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ComprobantePagoResource extends JsonResource
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
            
            'type'          => 'Comprobante de Pago',
            'id'            => $this->id,
            'attributes'    => [
                'cajero' => new UsuarioResource($this->whenLoaded('usuario')),
                'subtotal' => $this->subtotal,                
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
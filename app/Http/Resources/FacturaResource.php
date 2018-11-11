<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FacturaResource extends JsonResource
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
            
            'type'          => 'Factura',
            'id'            => $this->idComprobantePago,
            'attributes'    => [
                'comprobantePago' => new ComprobantePagoResource($this->whenLoaded('comprobantePago')),
                'cliente' => new PersonaJuridicaResource($this->whenLoaded('personaJuridica')),           
                'igv' => $this->igv,
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
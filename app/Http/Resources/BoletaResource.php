<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BoletaResource extends JsonResource
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
            'type'          => 'Boleta',
            'id'            => $this->idComprobantePago,
            'attributes'    => [
                'comprobantePago' => new ComprobantePagoResource($this->whenLoaded('comprobantePago')),
                'cliente' => new PersonaNaturalResource($this->whenLoaded('personaNatural')),           
                'igv' => $this->igv,
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
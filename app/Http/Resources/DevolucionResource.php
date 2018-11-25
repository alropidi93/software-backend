<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DevolucionResource extends JsonResource
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
            'type'          => 'Devolucion',
            'id'            => $this->id,
            'attributes'    => [
                'descripcion' => $this->descripcion,
                'monto' => $this->monto,
                'idComprobantePago' => $this->idComprobantePago,
                'usuario' => new UsuarioResource($this->whenLoaded('usuario')),
                'personaNatural' => new PersonaNaturalResource($this->whenLoaded('personaNatural')),
                'personaJuridica' => new PersonaJuridicaResource($this->whenLoaded('personaJuridica')),
                'lineasDeVenta' => new LineasDeVentaResource($this->whenLoaded('lineasDeVenta')),
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
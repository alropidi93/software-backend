<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonaJuridicaResource extends JsonResource
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
            
            'type'          => 'Persona Juridica',
            'id'            => $this->id,
            'attributes'    => [
                'ruc' => $this->ruc,
                'email' => $this->email,
                'razonSocial' => $this->razonSocial,
                'direccion' => $this->direccion,
                'telefono' => $this->telefono,
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}

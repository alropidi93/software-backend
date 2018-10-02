<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioRelationshipResource extends JsonResource
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
            'personaNatural'   => [
                'links' => [
                    'self'    => route('usuarios.relationships.personanatural', ['usuario' => $this->id]),
                    'related' => route('usuarios.personaNatural', ['usuario' => $this->id]),
                ],
                'data'  => new PersonaNaturalResource($this->personaNatural),
            ],
            
        ];
    }
}

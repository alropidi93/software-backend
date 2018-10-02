<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonaNaturalResource extends JsonResource
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
            
            'type'          => 'Persona Natural',
            'id'            => $this->id,
            'attributes'    => [
                'nombre' => $this->nombre,
                'apellidos' => $this->apellidos,
                'dni' => $this->dni,
                'genero' => $this->genero,
                'email' => $this->email,
                'direccion' => $this->direccion,
                'fechaNac' => $this->fechaNac,
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}

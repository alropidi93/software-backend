<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TiendaResource extends JsonResource
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
            'type'          => 'tienda',
            'id'            => $this->id,
            'attributes'    => [
                'nombre' => $this->nombre,
                'distrito' => $this->distrito,
                'ubicacion' => $this->ubicacion,
                'direccion' => $this->direccion,
                'telefono' => $this->telefono,
                'deleted' => $this->deleted,
            ],
        ];
    }
}


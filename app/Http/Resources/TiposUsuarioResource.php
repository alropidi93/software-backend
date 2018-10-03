<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TiposUsuarioResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'Lista de tipos de usuario',
            'listaTiposUsuario' => TipoUsuarioResource::collection($this->collection),
        ];
    }
}

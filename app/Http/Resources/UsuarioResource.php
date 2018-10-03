<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PersonaNaturalResource;

class UsuarioResource extends JsonResource
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
            
            'type'          => 'Usuario',
            'id'            => $this->idPersonaNatural,
            'attributes'    => [
                'password' => $this->password,
                'idTienda' => $this->idTienda,
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                //'tipoUsuario' => new TipoUsuarioResource($this->whenLoaded('tipoUsuario')),
                'personaNatural' => new PersonaNaturalResource($this->whenLoaded('personaNatural'))
                 
            ]
           
        ];
    }
}

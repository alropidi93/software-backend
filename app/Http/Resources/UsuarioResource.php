<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UsuarioRelationshipResource;
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
                'idTipoUsuario' => $this->idTipoUsuario,
                'idTienda' => $this->idTienda,
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'relationships' => new UsuarioRelationshipResource($this),
        ];
    }
}

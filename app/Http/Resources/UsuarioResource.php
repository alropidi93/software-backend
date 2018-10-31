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
                'tipoUsuario' => new TipoUsuarioResource($this->whenLoaded('tipoUsuario')),
                'usuarioxtienda'=> $this->whenLoaded('pivot'),
                'personaNatural' => new PersonaNaturalResource($this->whenLoaded('personaNatural')),
                'tiendaDeLaQueEsJefeDeTienda' => new TiendaResource($this->whenLoaded('tiendaCargoJefeTienda')),
                //'tiendaDeLaQueEsJefeDeAlmacen' => new TiendaResource($this->tiendaCargoJefeAlmacen),
                'tiendasDeLaQueEsJefeDeTienda' => new TiendasResource($this->whenLoaded('tiendasCargoJefeTienda')),
                'tiendasDeLaQueEsJefeDeAlmacen' => new TiendasResource($this->whenLoaded('tiendasCargoJefeAlmacen')),
                'tiendasDeLaQueEsTrabajador' => new TiendasResource($this->whenLoaded('tiendas')),
                
                 
            ]
           
        ];
    }
}

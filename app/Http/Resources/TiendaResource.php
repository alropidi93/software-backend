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
            
            'type'          => 'Tienda',
            'id'            => $this->id,
            'attributes'    => [
                'nombre' => $this->nombre,
                'distrito' => $this->distrito,
                'ubicacion' => $this->ubicacion,
                'direccion' => $this->direccion,
                'latitud' => $this->latitud,
                'longitud' => $this->longitud,
                'telefono' => $this->telefono,
                'jefeTienda' =>  new UsuarioResource($this->whenLoaded('jefeDeTienda')),
                'jefeAlmacen' =>  new UsuarioResource($this->whenLoaded('jefeDeAlmacen')),
                'trabajadores' => new UsuariosResource($this->whenLoaded('trabajadores')),
                'almacen' => new AlmacenResource($this->whenLoaded('almacen')),
                'usuarioxtienda'=>$this->whenLoaded('pivot'),
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}


<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProveedorResource extends JsonResource
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
            'type'          => 'Proveedor',
            'id'            => $this->id,
            'attributes'    => [
                'ruc' => $this->ruc,
                'razonSocial' => $this->razonSocial,
                'contacto' => $this->contacto,
                'direccion' => $this->direccion,
                'email' => $this->email,
                'productoxproveedor'=> $this->whenLoaded('pivot'),
                'telefono' => $this->telefono,
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
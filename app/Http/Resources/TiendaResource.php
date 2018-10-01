<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TiendaResource extends JsonResource
{
    protected $title;

    public function title($title){
        $this->title = $title;
        return $this;
    }
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
                'telefono' => $this->telefono,
                'deleted' => $this->deleted,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}


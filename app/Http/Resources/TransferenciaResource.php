<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransferenciaResource extends JsonResource
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
            
            'type'          => 'Transferencia',
            'id'            => $this->id,
            'attributes'    => [
                'estado' => $this->estado,
                'observacion' => $this->observacion,
                'respuesta'=> $this->respuesta,
                'deleted' => $this->deleted,
               
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}


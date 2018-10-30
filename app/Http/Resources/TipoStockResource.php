<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;



class TipoStockResource extends JsonResource
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
            
            'type'          => 'Tipo Stock',
            'id'            => $this->id,
            'attributes'    => [
                'tipo' => $this->tipo,
                'key' => $this->key,
                
               
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}

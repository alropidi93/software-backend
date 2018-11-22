<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovimientoTipoStockResource extends JsonResource
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
            
            'type'          => 'Movimiento Tipo Stock',
            'id'            => $this->id,
            'attributes'    => [
                'producto' =>  new ProductoResource($this->whenLoaded('producto')), 
                'almacen' =>  new AlmacenResource($this->whenLoaded('almacen')), 
                'tipoStock' => new TipoStockResource($this->whenLoaded('tipoStock')),                
                /*PARTE DE TUTORIAL PARA RELATIONSHIPS */
                'usuario' => new UsuarioResource($this->whenLoaded('usuario')), //'usuario' hace referencia al metodo relacional public function usuario() del modelo 'Movimiento', se le coloca la funcon setWhenLoaderd, para que aparezca solo cuando anteriormente se ha 'cargado la relacion', eso de 'cargar la relacion' lo explicar en MovimientoController
                'cantidad'=>$this->cantidad,
                'signo'=>$this->signo,
                /*PFIN ARTE DE TUTORIAL PARA RELATIONSHIPS */
                'deleted' => $this->deleted,
                'created_at' => $this->created_at
                
            ],
        ];
    }
}
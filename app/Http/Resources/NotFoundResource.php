<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotFoundResource extends JsonResource
{
    protected $title;
    protected $notfound;

    public function title($title){
        $this->title = $title;
        return $this;
    }

    public function notFound($notFound){
        $this->notFound = $notFound;
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
            'status'        => false,
            'title'         => $this->title,
            'notFound'      => $this->notFound,
            
        ];
    }
}


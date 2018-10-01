<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResponseResource extends JsonResource
{
    protected $title;
    protected $body;

    public function title($title){
        $this->title = $title;
        return $this;
    }

    public function body($body){
        $this->body = $body;
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
            'status'        => true,
            'title'         => $this->title,
            'body'          => $this->body,
            
        ];
    }
}


<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    protected $title;
    protected $body;

    public function title($title){
        $this->title = $title;
        return $this;
    }

    public function message($message){
        $this->message = $message;
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
            'message'          => $this->message,
            
        ];
    }
}


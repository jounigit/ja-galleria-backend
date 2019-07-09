<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'categories' => CategoryResource::collection($this->categories),
            // 'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            // 'categories' => CategoryResource::collection($this->whenLoaded($this->categories)),
            'albums' => $this->albums,
            'pictures' => $this->pictures
        ];
    }
}

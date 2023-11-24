<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image,
            'description' => $request->id ? $this->description : Str::limit($this->description, 70),
            'author' => $this->author,
            'published' => $this->published,
            'publisher' => $this->publisher,
            'isbn' => $this->isbn,
            'genre' => $this->genre
        ];
    }
}

<?php

namespace Webkul\Shop\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'image_url' => $this->image_url,
            'subscribed_to_news_letter' => $this->subscribed_to_news_letter,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

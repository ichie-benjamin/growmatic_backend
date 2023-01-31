<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "phone" => $this->phone,
            "avatar" => $this->avatar,
            "address" => $this->address,

            "verify" => $this->email_verified_at ? true : false,
            "name" => $this->name,
            "email" => $this->email,
            "firstname" => $this->first_name,
            "lastname" => $this->last_name,
        ];
    }
}

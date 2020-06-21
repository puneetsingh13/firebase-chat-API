<?php
 
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'name'  => $this->name,
            'email' => $this->email,
            'role'  => $this->role,
            'access_token' => $this->when($this->access_token,$this->access_token),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
 
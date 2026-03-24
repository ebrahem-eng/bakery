<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'email'             => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'gender'            => $this->gender,
            'status'            => $this->status,
            'age'               => $this->age,
            'birthday'          => $this->birthday,
            'phone'             => $this->phone,
            'img'               => $this->img,
            'last_login_at'     => $this->last_login_at,
            'last_login_ip'     => $this->last_login_ip,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
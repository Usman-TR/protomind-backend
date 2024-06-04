<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthResource extends JsonResource
{
    private string $token;

    public function __construct($resource, string $token)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->token = $token;
    }

    public function toArray(Request $request): array
    {
        return [
            "token" => $this->token,
            "expires_in" => JWTAuth::factory()->getTTL() * 60
        ];
    }
}

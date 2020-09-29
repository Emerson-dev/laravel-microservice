<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;

class GenreResource extends JsonResponse
{
    public function toArray($request)
    {
        return parent::toArray();
    }
}

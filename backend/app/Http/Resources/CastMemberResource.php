<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;

class CastMemberResource extends JsonResponse
{
    public function toArray($request)
    {
        return parent::toArray();
    }
}

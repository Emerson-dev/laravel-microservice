<?php

declare(strict_types=1);

namespace Tests\Traits;

use Exception;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait TestResources
{

    protected function assertResource(TestResponse $response, JsonResource $resource)
    {
        $response->assertJson($resource->response()->getData(true));
    }
}

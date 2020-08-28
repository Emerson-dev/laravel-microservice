<?php

declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Lang;

trait TestValidations
{
    protected function assertInValidationFields(
        TestResponse $response,
        array $fields,
        string $rule,
        array $rulesParams = []
    ) {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors($fields);

        foreach ($fields as $field) {
            $fieldName = str_replace('_', ' ', $field);
            $response->assertJsonFragment([
                Lang::get("validation.{$rule}", ['attribute' => $fieldName] + $rulesParams)
            ]);
        }
    }
}

<?php

declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Lang;

trait TestValidations
{

    protected function AssertInValidationStoreAction(
        array $data,
        string $rule,
        $rulesParams = []
    ) {
        $response = $this->json('POST', $this->routeStore(), $data);
        $fields = array_keys($data);
        $this->assertInValidationFields($response, $fields, $rule, $rulesParams);
    }

    protected function AssertInValidationupdateAction(
        array $data,
        string $rule,
        $rulesParams = []
    ) {
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $fields = array_keys($data);
        $this->assertInValidationFields($response, $fields, $rule, $rulesParams);
    }

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

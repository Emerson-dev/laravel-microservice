<?php

namespace Tests\Unit\Rules;

use App\Rules\GenreHasCategoriesRule;
use Mockery;
use Mockery\MockInterface;
use ReflectionClass;
use Tests\TestCase;

class GenreHasCategoriesRuleTest extends TestCase
{
    public function testCategoriesIdField()
    {
        $rule = new GenreHasCategoriesRule(
            [1, 1, 2, 2]
        );
        $refletionClass = new ReflectionClass(GenreHasCategoriesRule::class);
        $refletionProperty = $refletionClass->getProperty('categoriesId');
        $refletionProperty->setAccessible(true);

        $categoryId = $refletionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1, 2], $categoryId);
    }

    public function testGenresIdValue()
    {
        $rule = new GenreHasCategoriesRule([]);
        $rule->passes('', [1, 1, 2, 2]);

        $refletionClass = new ReflectionClass(GenreHasCategoriesRule::class);
        $refletionProperty = $refletionClass->getProperty('genresId');
        $refletionProperty->setAccessible(true);

        $genresId = $refletionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1, 2], $genresId);
    }

    public function testPassesRetunrsFalseWhenCategoriesOrGenresIsArrayEmpty()
    {
        $rule = $this->createRuleMock([1]);
        $this->assertFalse($rule->passes('', []));

        $rule = $this->createRuleMock([]);
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesReturnsFalseWhenGetRowsIsEmpty()
    {
        $rule = $this->createRuleMock([]);
        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect());
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesReturnFaslseWhenCategoriesWithoutGenres()
    {
        $rule = $this->createRuleMock([1, 2]);
        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect(['category_id' => 1]));
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesIsValid()
    {
        $rule = $this->createRuleMock([1, 2]);
        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([
                ['category_id' => 1],
                ['category_id' => 2]
            ]));

        $this->assertFalse($rule->passes('', [1]));

        $rule = $this->createRuleMock([1, 2]);
        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([
                ['category_id' => 1],
                ['category_id' => 2],
                ['category_id' => 1],
                ['category_id' => 2]
            ]));

        $this->assertFalse($rule->passes('', [1]));
    }

    protected function createRuleMock(array $categoriesId): MockInterface
    {
        return Mockery::mock(GenreHasCategoriesRule::class, [$categoriesId])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }
}

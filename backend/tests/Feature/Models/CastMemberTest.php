<?php

namespace Tests\Feature\Models;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;

class CastMemberTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {

        factory(CastMember::class, 1)->create();
        $castMembers = CastMember::all();
        $this->assertCount(1, $castMembers);
        $castMemberKeys = array_keys($castMembers->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'type',
                'deleted_at',
                'created_at',
                'updated_at'
            ],
            $castMemberKeys
        );
    }

    public function testCreate()
    {
        $castMember = CastMember::create([
            'name' => 'Teste',
            'type' => CastMember::TYPE_ACTOR
        ]);
        $castMember->refresh();

        $this->assertEquals('Teste', $castMember->name);

        $this->assertEquals(CastMember::TYPE_ACTOR, $castMember->type);

        $this->assertNotEmpty($castMember->id);
        $this->assertIsString($castMember->id);

        $this->assertTrue(RamseyUuid::isValid($castMember->id));
    }

    public function testUpdate()
    {
        /**
         *  @var CastMember $castMember
         */
        $castMember = factory(CastMember::class)->create([
            'name' => 'Teste name',
            'type' => CastMember::TYPE_ACTOR
        ])->first();

        $data = [
            'name' => 'Teste update',
            'type' => CastMember::TYPE_DIRECTOR
        ];

        $castMember->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $castMember->{$key});
        }
    }

    public function testDelete()
    {
        /**
         *  @var CastMember $castMember
         */
        $castMember = factory(CastMember::class)->create([
            'name' => 'Teste name',
            'type' => CastMember::TYPE_ACTOR
        ])->first();

        $castMember->delete();

        $this->assertSoftDeleted($castMember);
    }
}

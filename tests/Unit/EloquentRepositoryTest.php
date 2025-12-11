<?php

declare(strict_types=1);

namespace Nauxa\RepositoryService\Tests\Unit;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Nauxa\RepositoryService\Abstracts\EloquentRepository;
use Nauxa\RepositoryService\Contracts\RepositoryContract;
use Nauxa\RepositoryService\Tests\TestCase;

class EloquentRepositoryTest extends TestCase
{
    protected TestUserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test table
        Schema::create('test_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        $this->repository = new TestUserRepository(new TestUser());
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('test_users');

        parent::tearDown();
    }

    /** @test */
    public function it_can_create_a_record(): void
    {
        $user = $this->repository->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertInstanceOf(Model::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertDatabaseHas('test_users', ['email' => 'john@example.com']);
    }

    /** @test */
    public function it_can_find_a_record_by_id(): void
    {
        $created = $this->repository->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $found = $this->repository->find($created->id);

        $this->assertNotNull($found);
        $this->assertEquals($created->id, $found->id);
        $this->assertEquals('Jane Doe', $found->name);
    }

    /** @test */
    public function it_returns_null_when_finding_nonexistent_record(): void
    {
        $found = $this->repository->find(99999);

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_find_or_fail_a_record(): void
    {
        $created = $this->repository->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $found = $this->repository->findOrFail($created->id);

        $this->assertEquals($created->id, $found->id);
    }

    /** @test */
    public function it_throws_exception_when_find_or_fail_nonexistent(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->repository->findOrFail(99999);
    }

    /** @test */
    public function it_can_get_all_records(): void
    {
        $this->repository->create(['name' => 'User 1', 'email' => 'user1@example.com']);
        $this->repository->create(['name' => 'User 2', 'email' => 'user2@example.com']);
        $this->repository->create(['name' => 'User 3', 'email' => 'user3@example.com']);

        $all = $this->repository->all();

        $this->assertInstanceOf(Collection::class, $all);
        $this->assertCount(3, $all);
    }

    /** @test */
    public function it_can_update_a_record(): void
    {
        $user = $this->repository->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $result = $this->repository->update($user->id, ['name' => 'Updated Name']);

        $this->assertTrue($result);
        $this->assertDatabaseHas('test_users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    /** @test */
    public function it_can_delete_a_record(): void
    {
        $user = $this->repository->create([
            'name' => 'To Delete',
            'email' => 'delete@example.com',
        ]);

        $result = $this->repository->delete($user->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('test_users', ['id' => $user->id]);
    }

    /** @test */
    public function it_can_destroy_multiple_records(): void
    {
        $user1 = $this->repository->create(['name' => 'User 1', 'email' => 'u1@example.com']);
        $user2 = $this->repository->create(['name' => 'User 2', 'email' => 'u2@example.com']);
        $user3 = $this->repository->create(['name' => 'User 3', 'email' => 'u3@example.com']);

        $deleted = $this->repository->destroy([$user1->id, $user2->id]);

        $this->assertEquals(2, $deleted);
        $this->assertDatabaseMissing('test_users', ['id' => $user1->id]);
        $this->assertDatabaseMissing('test_users', ['id' => $user2->id]);
        $this->assertDatabaseHas('test_users', ['id' => $user3->id]);
    }

    /** @test */
    public function it_can_find_where_conditions(): void
    {
        $this->repository->create(['name' => 'Active User', 'email' => 'active@example.com', 'status' => 'active']);
        $this->repository->create(['name' => 'Inactive User', 'email' => 'inactive@example.com', 'status' => 'inactive']);
        $this->repository->create(['name' => 'Another Active', 'email' => 'active2@example.com', 'status' => 'active']);

        $activeUsers = $this->repository->findWhere(['status' => 'active']);

        $this->assertCount(2, $activeUsers);
    }

    /** @test */
    public function it_can_find_where_in(): void
    {
        $user1 = $this->repository->create(['name' => 'User 1', 'email' => 'u1@example.com']);
        $user2 = $this->repository->create(['name' => 'User 2', 'email' => 'u2@example.com']);
        $this->repository->create(['name' => 'User 3', 'email' => 'u3@example.com']);

        $found = $this->repository->findWhereIn('id', [$user1->id, $user2->id]);

        $this->assertCount(2, $found);
    }

    /** @test */
    public function it_can_paginate_results(): void
    {
        for ($i = 1; $i <= 25; $i++) {
            $this->repository->create(['name' => "User $i", 'email' => "user$i@example.com"]);
        }

        $paginated = $this->repository->paginate(10);

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginated);
        $this->assertCount(10, $paginated->items());
        $this->assertEquals(25, $paginated->total());
        $this->assertEquals(3, $paginated->lastPage());
    }

    /** @test */
    public function it_can_first_or_create(): void
    {
        // First call should create
        $user1 = $this->repository->firstOrCreate(
            ['email' => 'unique@example.com'],
            ['name' => 'First Creation']
        );

        $this->assertEquals('First Creation', $user1->name);

        // Second call should find existing
        $user2 = $this->repository->firstOrCreate(
            ['email' => 'unique@example.com'],
            ['name' => 'Second Creation']
        );

        $this->assertEquals($user1->id, $user2->id);
        $this->assertEquals('First Creation', $user2->name); // Should still be first name
    }

    /** @test */
    public function it_can_set_and_reset_with_relations(): void
    {
        $repository = $this->repository->with(['posts']);
        $this->assertInstanceOf(RepositoryContract::class, $repository);

        $repository->resetWith();
        // Just verify it doesn't throw
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_get_and_set_model(): void
    {
        $model = $this->repository->getModel();
        $this->assertInstanceOf(Model::class, $model);

        $newModel = new TestUser();
        $this->repository->setModel($newModel);

        $this->assertSame($newModel, $this->repository->getModel());
    }
}

/**
 * Test User Model.
 */
class TestUser extends Model
{
    protected $table = 'test_users';

    protected $guarded = [];
}

/**
 * Test User Repository.
 */
class TestUserRepository extends EloquentRepository
{
    public function __construct(TestUser $model)
    {
        $this->model = $model;
    }
}

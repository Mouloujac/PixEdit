<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Album;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AlbumController
 */
class AlbumControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_behaves_as_expected(): void
    {
        $albums = Album::factory()->count(3)->create();

        $response = $this->get(route('album.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AlbumController::class,
            'store',
            \App\Http\Requests\AlbumStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_saves(): void
    {
        $title = $this->faker->sentence(4);
        $description = $this->faker->text;
        $created_at = $this->faker->dateTime();

        $response = $this->post(route('album.store'), [
            'title' => $title,
            'description' => $description,
            'created_at' => $created_at,
        ]);

        $albums = Album::query()
            ->where('title', $title)
            ->where('description', $description)
            ->where('created_at', $created_at)
            ->get();
        $this->assertCount(1, $albums);
        $album = $albums->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function show_behaves_as_expected(): void
    {
        $album = Album::factory()->create();

        $response = $this->get(route('album.show', $album));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AlbumController::class,
            'update',
            \App\Http\Requests\AlbumUpdateRequest::class
        );
    }

    /**
     * @test
     */
    public function update_behaves_as_expected(): void
    {
        $album = Album::factory()->create();
        $title = $this->faker->sentence(4);
        $description = $this->faker->text;
        $created_at = $this->faker->dateTime();

        $response = $this->put(route('album.update', $album), [
            'title' => $title,
            'description' => $description,
            'created_at' => $created_at,
        ]);

        $album->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($title, $album->title);
        $this->assertEquals($description, $album->description);
        $this->assertEquals($created_at, $album->created_at);
    }


    /**
     * @test
     */
    public function destroy_deletes_and_responds_with(): void
    {
        $album = Album::factory()->create();

        $response = $this->delete(route('album.destroy', $album));

        $response->assertNoContent();

        $this->assertModelMissing($album);
    }
}

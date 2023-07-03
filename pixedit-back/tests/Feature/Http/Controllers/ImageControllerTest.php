<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Image;
use App\Models\Nullable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ImageController
 */
class ImageControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_behaves_as_expected(): void
    {
        $images = Image::factory()->count(3)->create();

        $response = $this->get(route('image.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ImageController::class,
            'store',
            \App\Http\Requests\ImageStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_saves(): void
    {
        $title = $this->faker->sentence(4);
        $link = $this->faker->text;
        $album = Nullable::factory()->create();
        $created_at = $this->faker->dateTime();
        $updated_at = $this->faker->dateTime();

        $response = $this->post(route('image.store'), [
            'title' => $title,
            'link' => $link,
            'album_id' => $album->id,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        ]);

        $images = Image::query()
            ->where('title', $title)
            ->where('link', $link)
            ->where('album_id', $album->id)
            ->where('created_at', $created_at)
            ->where('updated_at', $updated_at)
            ->get();
        $this->assertCount(1, $images);
        $image = $images->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function show_behaves_as_expected(): void
    {
        $image = Image::factory()->create();

        $response = $this->get(route('image.show', $image));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ImageController::class,
            'update',
            \App\Http\Requests\ImageUpdateRequest::class
        );
    }

    /**
     * @test
     */
    public function update_behaves_as_expected(): void
    {
        $image = Image::factory()->create();
        $title = $this->faker->sentence(4);
        $link = $this->faker->text;
        $album = Nullable::factory()->create();
        $created_at = $this->faker->dateTime();
        $updated_at = $this->faker->dateTime();

        $response = $this->put(route('image.update', $image), [
            'title' => $title,
            'link' => $link,
            'album_id' => $album->id,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        ]);

        $image->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($title, $image->title);
        $this->assertEquals($link, $image->link);
        $this->assertEquals($album->id, $image->album_id);
        $this->assertEquals($created_at, $image->created_at);
        $this->assertEquals($updated_at, $image->updated_at);
    }


    /**
     * @test
     */
    public function destroy_deletes_and_responds_with(): void
    {
        $image = Image::factory()->create();

        $response = $this->delete(route('image.destroy', $image));

        $response->assertNoContent();

        $this->assertModelMissing($image);
    }
}

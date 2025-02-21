<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_page()
    {
        $response = $this->postJson('/api/pages', [
            'slug' => 'test-page',
            'title' => 'Test Page',
            'content' => 'This is a test page',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['status', 'message', 'data']);
    }

    public function test_can_resolve_nested_page()
    {
        $parent = Page::create(['slug' => 'parent', 'title' => 'Parent', 'content' => 'Parent content']);
        $child = Page::create(['parent_id' => $parent->id, 'slug' => 'child', 'title' => 'Child', 'content' => 'Child content']);

        $response = $this->getJson('/parent/child');

        $response->assertStatus(200)
            ->assertJsonFragment(['slug' => 'child']);
    }
}
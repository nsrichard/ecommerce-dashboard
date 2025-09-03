<?php

namespace Tests\Feature;

use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ExportsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_redirected(): void
    {
        $store = Store::factory()->create();
        $response = $this->post(route('exports.store', $store), []);
        $response->assertRedirect('/login');
    }

    public function test_authenticated_can_enqueue_export(): void
    {
        Bus::fake();

        $user  = \App\Models\User::factory()->create();
        $store = Store::factory()->create();

        $this->actingAs($user)
            ->post(route('exports.store', $store), [
                'type'   => 'products',
                'format' => 'csv',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        Bus::assertDispatched(\App\Jobs\ExportProductsJob::class);
        $this->assertDatabaseCount('exports', 1);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EnsureUserHasPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_request_to_permission_route_is_rejected(): void
    {
        // Define a test route using permission middleware
        \Illuminate\Support\Facades\Route::get('/test-permission-check', fn () => 'OK')
            ->middleware('permission:admin.access');

        $response = $this->get('/test-permission-check');
        $response->assertStatus(401);
    }

    public function test_user_without_permission_is_forbidden(): void
    {
        \Illuminate\Support\Facades\Route::get('/test-permission-check-user', fn () => 'OK')
            ->middleware('permission:admin.access');

        $user = User::query()->create([
            'name' => 'Regular User',
            'email' => 'user@example.test',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->actingAs($user)->get('/test-permission-check-user');
        $response->assertForbidden();
    }

    public function test_user_with_permission_can_access(): void
    {
        Permission::findOrCreate('admin.access');

        \Illuminate\Support\Facades\Route::get('/test-permission-allowed', fn () => 'OK')
            ->middleware('permission:admin.access');

        $user = User::query()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.test',
            'password' => bcrypt('secret'),
        ]);
        $user->givePermissionTo('admin.access');

        $response = $this->actingAs($user)->get('/test-permission-allowed');
        $response->assertOk();
        $this->assertSame('OK', $response->getContent());
    }
}

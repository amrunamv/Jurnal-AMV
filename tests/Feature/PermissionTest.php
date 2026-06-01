<?php

namespace Tests\Feature;

use App\Models\Manuscript;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_download_unpublished_manuscript()
    {
        $manuscript = Manuscript::factory()->create([
            'status' => Manuscript::STATUS_DRAFT
        ]);

        $response = $this->get(route('articles.download', $manuscript->slug));

        $response->assertStatus(403);
    }

    public function test_super_admin_can_download_unpublished_manuscript()
    {
        $adminRole = Role::create(['name' => 'super_admin']);
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $manuscript = Manuscript::factory()->create([
            'status' => Manuscript::STATUS_DRAFT
        ]);

        // Create dummy file
        @mkdir(storage_path('app/manuscripts'), 0755, true);
        file_put_contents(storage_path('app/manuscripts/' . $manuscript->uuid . '.pdf'), 'dummy content');

        $response = $this->actingAs($admin)->get(route('articles.download', $manuscript->slug));

        $response->assertStatus(200);
        
        // Cleanup
        @unlink(storage_path('app/manuscripts/' . $manuscript->uuid . '.pdf'));
    }
}


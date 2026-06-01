<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Permissions follow Filament Shield snake_case format: {action}_{resource}
     * Roles follow underscore naming convention: super_admin, editor, etc.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─────────────────────────────────────────
        // Define all permissions per resource
        // ─────────────────────────────────────────

        $resources = [
            'manuscript',
            'journal',
            'review',
            'user',
            'volume',
            'issue',
            'announcement',
            'role',
        ];

        // Special resources with :: separator (Filament Shield convention)
        $specialResources = [
            'crossref::log',
            'plagiarism::report',
        ];

        $actions = [
            'view_any',
            'view',
            'create',
            'update',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
        ];

        // Create all permissions
        $allPermissions = [];

        foreach (array_merge($resources, $specialResources) as $resource) {
            foreach ($actions as $action) {
                $permissionName = "{$action}_{$resource}";
                $allPermissions[] = $permissionName;
                Permission::firstOrCreate(['name' => $permissionName]);
            }
        }

        // ─────────────────────────────────────────
        // Create roles and assign permissions
        // ─────────────────────────────────────────

        // 1. Super Admin — Full access to everything
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Editor — Manages manuscripts, journals, volumes, issues, announcements, reviews
        $editor = Role::firstOrCreate(['name' => 'editor']);
        $editor->givePermissionTo([
            // Manuscript
            'view_any_manuscript',
            'view_manuscript',
            'update_manuscript',
            'delete_manuscript',

            // Journal
            'view_any_journal',
            'view_journal',
            'update_journal',

            // Review
            'view_any_review',
            'view_review',
            'create_review',
            'update_review',

            // Volume
            'view_any_volume',
            'view_volume',
            'create_volume',
            'update_volume',

            // Issue
            'view_any_issue',
            'view_issue',
            'create_issue',
            'update_issue',

            // Announcement
            'view_any_announcement',
            'view_announcement',
            'create_announcement',
            'update_announcement',
            'delete_announcement',
        ]);

        // 3. Reviewer — Can view & review assigned manuscripts only
        $reviewer = Role::firstOrCreate(['name' => 'reviewer']);
        $reviewer->givePermissionTo([
            // Manuscript (view only)
            'view_any_manuscript',
            'view_manuscript',

            // Review (create & update their reviews)
            'view_any_review',
            'view_review',
            'create_review',
            'update_review',
        ]);

        // 4. Author — Can submit and manage own manuscripts
        $author = Role::firstOrCreate(['name' => 'author']);
        $author->givePermissionTo([
            // Manuscript
            'view_any_manuscript',
            'view_manuscript',
            'create_manuscript',
            'update_manuscript',
        ]);

        // 5. Reader — Public access only, no admin panel permissions
        Role::firstOrCreate(['name' => 'reader']);
        // Reader role has no permissions (public access via frontend only)
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'tickets' => ['view', 'create', 'edit', 'delete', 'assign', 'close'],
            'access_requests' => ['view', 'create', 'approve', 'reject', 'implement'],
            'change_requests' => ['view', 'create', 'approve', 'reject', 'schedule', 'implement'],
            'users' => ['view', 'create', 'edit', 'delete', 'assign-role'],
            'roles' => ['view', 'create', 'edit', 'delete'],
            'reports' => ['view', 'export'],
            'settings' => ['view', 'edit'],
        ];

        foreach ($permissions as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$module}.{$action}"]);
            }
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions(Permission::all());

        $agent = Role::firstOrCreate(['name' => 'Agent']);
        $agent->syncPermissions([
            'tickets.view', 'tickets.create', 'tickets.edit', 'tickets.assign', 'tickets.close',
            'access_requests.view', 'access_requests.implement',
            'change_requests.view', 'change_requests.implement',
            'reports.view',
        ]);

        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $manager->syncPermissions([
            'tickets.view', 'tickets.create', 'tickets.edit', 'tickets.assign', 'tickets.close',
            'access_requests.view', 'access_requests.create', 'access_requests.approve', 'access_requests.reject',
            'change_requests.view', 'change_requests.create', 'change_requests.approve', 'change_requests.reject', 'change_requests.schedule',
            'reports.view', 'reports.export',
        ]);

        $user = Role::firstOrCreate(['name' => 'User']);
        $user->syncPermissions([
            'tickets.view', 'tickets.create',
            'access_requests.view', 'access_requests.create',
            'change_requests.view', 'change_requests.create',
        ]);

        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@helpdesk.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdminUser->assignRole('Super Admin');

        $agentUser = User::firstOrCreate(
            ['email' => 'agent@helpdesk.com'],
            [
                'name' => 'Agent User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $agentUser->assignRole('Agent');

        $managerUser = User::firstOrCreate(
            ['email' => 'manager@helpdesk.com'],
            [
                'name' => 'Manager User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $managerUser->assignRole('Manager');

        $normalUser = User::firstOrCreate(
            ['email' => 'user@helpdesk.com'],
            [
                'name' => 'Normal User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $normalUser->assignRole('User');
    }
}

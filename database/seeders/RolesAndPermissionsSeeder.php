<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        Permission::create(['name' => 'Administrator']);
        Permission::create(['name' => 'Developer']);
        Permission::create(['name' => 'Academics']);
        Permission::create(['name' => 'Finance']);

        // Create roles and assign permissions
        $developer = Role::create(['name' => 'Developer']);
        $administrator = Role::create(['name' => 'Administrator']);
        $academics = Role::create(['name' => 'Academics']);
        $finance = Role::create(['name' => 'Finance']);

        $developer->givePermissionTo('Administrator');
        $developer->givePermissionTo('Developer');
        $developer->givePermissionTo('Academics');
        $developer->givePermissionTo('Finance');
        $administrator->givePermissionTo('Administrator');
        $administrator->givePermissionTo('Academics');
        $administrator->givePermissionTo('Finance');
        $academics->givePermissionTo('Academics');
        $finance->givePermissionTo('Finance');
    }
}

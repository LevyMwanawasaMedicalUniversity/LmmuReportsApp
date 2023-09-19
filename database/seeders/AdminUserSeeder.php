<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Dev User',
            'email' => 'dev@lmmu.com',
            'password' => bcrypt('Lmmu@D3v!'),
        ]);

        // Create the admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'Developer']);

        // Assign the admin role to the admin user
        $admin->assignRole($adminRole);

        // Assign all permissions to the admin role (optional)
        $adminRole->syncPermissions(Permission::all());
    }
}

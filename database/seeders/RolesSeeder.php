<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = Role::create(['name' => 'superadmin']);
        $admin = Role::create(['name' => 'admin']);
        
        $manage_users_permission = Permission::create(['name' => 'manage users']);
        $manage_tools_permission = Permission::create(['name' => 'manage tools']);

        $permissions =[
            $manage_users_permission,
            $manage_tools_permission
        ];
        $superadmin->syncPermissions($permissions);
    }
}

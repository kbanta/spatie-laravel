<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = new User();
        $superadmin->name = 'superadmin';
        $superadmin->email = 'superadmin@superadmin.com';
        $superadmin->password = Hash::make('password');
        $superadmin->save();
        $superadmin->assignRole(1);
    }
}

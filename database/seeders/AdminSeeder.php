<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email'=>'admin@lelangjam.local'],
            [
                'name'=>'Superadmin Lelang',
                'username' => 'Superadmin',
                'password'=>bcrypt('password'),
                'role'=>'SUPERADMIN',
                'status'=>'ACTIVE',
                'email_verified_at'=>now(),
            ]
        );
    }
}

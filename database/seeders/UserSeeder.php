<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
    
        $admin = User::factory()->create([
            'name' => 'Roshan Napit',
            'email' => 'admin@sharklasers.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole($adminRole);

      // mutltimple admin
        for ($i = 1; $i <= 16; $i++) {
            $user = User::factory()->create([
                'email' => "user{$i}@sharklasers.com",
                'password' => Hash::make('password'),
            ]);

            $user->assignRole($adminRole);
        }
    }
}

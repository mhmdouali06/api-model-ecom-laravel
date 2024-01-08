<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    protected static ?string $password;

    public function run(): void
    
    {
        // Role::create(['name' => 'admin']);
        // Role::create(['name' => 'user']);
        // Role::create(['name' => 'writer']);
        // $user=\App\Models\User::create([
        //     'name' => "admin",
        //     'email' => "admin@gmail.com",
        //     'email_verified_at' => now(),
        //     'password' => static::$password ??= Hash::make('password'),
        //     'remember_token' => Str::random(10),
        // ]);
        // $user->assignRole('writer', 'admin');
    
        // \App\Models\User::factory(10)->create();
        // $role = Role::create(['name' => 'superAdmin']);
// $permission = Permission::create(['name' => 'edit articles']);

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}

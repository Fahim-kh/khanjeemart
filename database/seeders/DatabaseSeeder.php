<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\Role::factory(1)->create();
        \App\Models\Module::factory(1)->create();
        \App\Models\RolePermission::factory(1)->create();
        \App\Models\User::factory(1)->create();
    }
}

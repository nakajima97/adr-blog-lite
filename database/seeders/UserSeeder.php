<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Blog Admin',
            'email' => 'admin@example.com',
            'password' => 'password', // ハッシュ化される
        ]);
    }
}

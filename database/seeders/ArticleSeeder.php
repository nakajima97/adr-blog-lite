<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;

final class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        // 公開記事
        Article::factory()->count(5)->create([
            'user_id' => $user->id,
            'status' => 'published',
        ]);

        // 下書き記事
        Article::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'draft',
        ]);
    }
}

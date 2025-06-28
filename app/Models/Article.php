<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    // スコープ: 公開済み記事のみ
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    // スコープ: 最新順
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    // リレーション: 記事は1人のユーザーに属する
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

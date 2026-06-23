<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $table = 'bja_posts';

    protected $fillable = [
        'title', 'slug', 'category', 'excerpt', 'content',
        'cover_url', 'cover_alt', 'author', 'published_at', 'is_published',
        'meta_title', 'meta_description', 'focus_keyword', 'tags',
        'og_title', 'og_image', 'og_description',
    ];

    protected $casts = [
        'tags'         => 'array',
        'is_published' => 'boolean',
        'published_at' => 'date',
    ];

    public static function categories(): array
    {
        return ['Umum', 'Tips', 'Promo', 'Berita', 'Panduan', 'Informasi'];
    }

    public static function makeSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $i = 1;
        while (static::where('slug', $slug)->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'summary',
        'content',
        'thumbnail',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // ============ BOOT — slug từ title, tránh trùng (kể cả bản ghi đã xóa mềm) ============

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $base = Str::slug($post->title) ?: 'bai-viet';
                $post->slug = static::ensureUniqueSlug($base, null);
            } else {
                $base = Str::slug($post->slug) ?: 'bai-viet';
                $post->slug = static::ensureUniqueSlug($base, null);
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title') && ! $post->isDirty('slug')) {
                $base = Str::slug($post->title) ?: 'bai-viet';
                $post->slug = static::ensureUniqueSlug($base, $post->id);
            } elseif ($post->isDirty('slug')) {
                $base = Str::slug($post->slug) ?: 'bai-viet';
                $post->slug = static::ensureUniqueSlug($base, $post->id);
            }
        });
    }

    protected static function ensureUniqueSlug(string $base, ?int $exceptId): string
    {
        $slug = $base;
        $n = 1;
        while (static::withTrashed()
            ->where('slug', $slug)
            ->when($exceptId !== null, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists()) {
            $slug = $base.'-'.$n++;
        }

        return $slug;
    }

    // ============ HELPERS ============

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (empty($this->thumbnail)) {
            return null;
        }

        if (Str::startsWith($this->thumbnail, ['http://', 'https://'])) {
            return $this->thumbnail;
        }

        return asset('storage/'.$this->thumbnail);
    }

    // ============ SCOPES ============

    // Chỉ lấy bài đã đăng (dùng cho trang khách hàng)
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // ============ RELATIONSHIPS ============

    // Bài viết thuộc về một tác giả (admin)
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}

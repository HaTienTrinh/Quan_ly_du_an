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
            'deleted_at'   => 'datetime',
        ];
    }

    // ============ BOOT — tự tạo slug từ title ============

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title')) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    // ============ HELPERS ============

    public function isPublished(): bool
    {
        return $this->status === 'published';
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

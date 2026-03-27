<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    // ============ BOOT — tự tạo slug từ name ============

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // ============ SCOPES ============

    // Chỉ lấy danh mục đang hoạt động
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ============ RELATIONSHIPS ============

    // Một danh mục có nhiều sản phẩm
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Chỉ lấy sản phẩm đang active (dùng cho trang khách hàng)
    public function activeProducts()
    {
        return $this->hasMany(Product::class)->where('is_active', true);
    }
}

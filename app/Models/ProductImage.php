<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_primary'  => 'boolean',
            'sort_order'  => 'integer',
        ];
    }

    // ============ RELATIONSHIPS ============

    // Ảnh thuộc về một sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

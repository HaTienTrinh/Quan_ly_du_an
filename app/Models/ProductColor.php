<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'size',       // Size biến thể: S, M, L, XL, 38, 39...
        'hex_code',
    ];

    // Tên hiển thị đầy đủ: "Đỏ - M" hoặc chỉ "Đỏ" nếu không có size
    public function getDisplayNameAttribute(): string
    {
        return $this->size ? "{$this->name} - {$this->size}" : $this->name;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

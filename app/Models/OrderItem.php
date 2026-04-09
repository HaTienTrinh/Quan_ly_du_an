<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_color_id',
        'product_name',
        'product_color_name',
        'product_color_hex',
        'product_size',       // Snapshot size tại thời điểm mua
        'product_thumbnail',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'quantity'   => 'integer',
            'subtotal'   => 'decimal:2',
        ];
    }

    // ============ RELATIONSHIPS ============

    // Item thuộc về một đơn hàng
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Item liên kết với sản phẩm gốc
    // (dùng để xem thông tin sản phẩm hiện tại nếu cần)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productColor()
    {
        return $this->belongsTo(ProductColor::class);
    }

    public function returnRequest()
    {
        return $this->hasOne(ReturnRequest::class);
    }
}

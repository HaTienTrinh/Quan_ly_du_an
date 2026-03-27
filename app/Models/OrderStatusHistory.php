<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'changed_by',
        'from_status',
        'to_status',
        'note',
    ];

    // ============ RELATIONSHIPS ============

    // Lịch sử thuộc về một đơn hàng
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Admin thực hiện thay đổi
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}

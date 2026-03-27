<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'receiver_name',
        'phone',
        'province',
        'district',
        'ward',
        'address_detail',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    // ============ HELPERS ============

    // Trả về địa chỉ đầy đủ dạng chuỗi
    public function getFullAddressAttribute(): string
    {
        return "{$this->address_detail}, {$this->ward}, {$this->district}, {$this->province}";
    }

    // ============ RELATIONSHIPS ============

    // Địa chỉ thuộc về một user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

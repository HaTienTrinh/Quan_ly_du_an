<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'birth_date',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'is_active' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    // ============ HELPERS ============

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (empty($this->avatar)) {
            return null;
        }

        if (Str::startsWith($this->avatar, ['http://', 'https://'])) {
            return $this->avatar;
        }

        return asset('storage/'.$this->avatar);
    }

    // ============ RELATIONSHIPS ============

    // Một user có nhiều địa chỉ giao hàng
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    // Địa chỉ mặc định
    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    // Một user (khách hàng) có nhiều đơn hàng
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Một user (admin) viết nhiều bài viết
    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    // Lịch sử trạng thái đơn hàng do admin cập nhật
    public function orderStatusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class, 'changed_by');
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function returnRequestStatusHistories()
    {
        return $this->hasMany(ReturnRequestStatusHistory::class, 'changed_by');
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function postComments()
    {
        return $this->hasMany(PostComment::class);
    }
}

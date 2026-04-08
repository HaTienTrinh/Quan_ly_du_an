<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'receiver_name',
        'receiver_phone',
        'receiver_province',
        'receiver_district',
        'receiver_ward',
        'receiver_address_detail',
        'subtotal',
        'shipping_fee',
        'discount_amount',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'paid_at',
        'note',
        'admin_note',
        'confirmed_by',
        'confirmed_at',
        'cancelled_at',
        'cancel_reason',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'        => 'decimal:2',
            'shipping_fee'    => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount'    => 'decimal:2',
            'paid_at'         => 'datetime',
            'confirmed_at'    => 'datetime',
            'cancelled_at'    => 'datetime',
        ];
    }

    // ============ CONSTANTS ============

    const STATUS_PENDING   = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPING  = 'shipping';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_RETURNED  = 'returned';

    const STATUS_LABELS = [
        'pending'    => 'Chờ xác nhận',
        'confirmed'  => 'Đã xác nhận',
        'processing' => 'Đang chuẩn bị',
        'shipping'   => 'Đang giao',
        'delivered'  => 'Đã giao',
        'cancelled'  => 'Đã hủy',
        'returned'   => 'Hoàn trả',
    ];

    const PAYMENT_LABELS = [
        'cash'          => 'Thanh toán khi nhận hàng',
        'credit_card'   => 'VNPay',
        'bank_transfer' => 'Chuyển khoản ngân hàng',
        'cod'           => 'Thanh toán khi nhận hàng',
        'bank_transfer' => 'Chuyển khoản ngân hàng',
        'momo'          => 'Ví MoMo',
        'vnpay'         => 'VNPay',
    ];

    // ============ HELPERS ============

    // Tạo mã đơn hàng tự động: ORD-20240101-0001
    public static function generateOrderCode(): string
    {
        $date   = now()->format('Ymd');
        $count  = self::whereDate('created_at', today())->count() + 1;
        return 'ORD-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getPaymentLabelAttribute(): string
    {
        return [
            'cash' => 'Thanh toán khi nhận hàng',
            'credit_card' => 'VNPay',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'cod' => 'Thanh toán khi nhận hàng',
            'momo' => 'Ví MoMo',
            'vnpay' => 'VNPay',
        ][$this->payment_method] ?? $this->payment_method;
    }

    public function getPaymentMethodAttribute($value): string
    {
        return match ($value) {
            'cod' => 'cash',
            'vnpay' => 'credit_card',
            default => $value,
        };
    }

    public function setPaymentMethodAttribute($value): void
    {
        $this->attributes['payment_method'] = match ($value) {
            'cash' => 'cod',
            'credit_card' => 'vnpay',
            default => $value,
        };
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->receiver_address_detail}, {$this->receiver_ward}, {$this->receiver_district}, {$this->receiver_province}";
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_PROCESSING,
        ], true);
    }

    public function canBePrepared(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function canBeShipped(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function canBeCompleted(): bool
    {
        return $this->status === self::STATUS_SHIPPING;
    }

    public function canBeReceived(): bool
    {
        return $this->status === self::STATUS_SHIPPING;
    }

    public function canBeReturned(): bool
    {
        if ($this->status !== self::STATUS_DELIVERED) {
            return false;
        }

        $returnDeadline = $this->getReturnDeadlineAt();

        return $returnDeadline !== null && now()->lte($returnDeadline);
    }

    public function getDeliveredAt(): ?Carbon
    {
        $history = $this->relationLoaded('statusHistories')
            ? $this->statusHistories->firstWhere('to_status', self::STATUS_DELIVERED)
            : $this->statusHistories()
                ->where('to_status', self::STATUS_DELIVERED)
                ->latest('created_at')
                ->first();

        return $history?->created_at;
    }

    public function getReturnDeadlineAt(): ?Carbon
    {
        return $this->getDeliveredAt()?->copy()->addDays(7);
    }

    // ============ SCOPES ============

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // ============ RELATIONSHIPS ============

    // Đơn hàng thuộc về một khách hàng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Đơn hàng được xác nhận bởi admin
    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // Đơn hàng có nhiều sản phẩm
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function latestReturnRequest()
    {
        return $this->hasOne(ReturnRequest::class)->latestOfMany();
    }

    // Lịch sử thay đổi trạng thái
    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }
}

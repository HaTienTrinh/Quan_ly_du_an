<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ReturnRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_SHIPPING_BACK = 'shipping_back';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_INSPECTING = 'inspecting';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_EXCHANGED = 'exchanged';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REJECTED = 'rejected';

    public const TYPE_REFUND = 'refund';
    public const TYPE_EXCHANGE = 'exchange';

    public const LOGISTICS_CUSTOMER_SHIP = 'customer_ship';
    public const LOGISTICS_SYSTEM_PICKUP = 'system_pickup';

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_SHIPPING_BACK => 'Shipping Back',
        self::STATUS_RECEIVED => 'Received',
        self::STATUS_INSPECTING => 'Inspecting',
        self::STATUS_REFUNDED => 'Refunded',
        self::STATUS_EXCHANGED => 'Exchanged',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_REJECTED => 'Rejected',
    ];

    public const TYPE_LABELS = [
        self::TYPE_REFUND => 'Hoàn tiền',
        self::TYPE_EXCHANGE => 'Đổi hàng',
    ];

    public const LOGISTICS_LABELS = [
        self::LOGISTICS_CUSTOMER_SHIP => 'Khách tự gửi hàng về',
        self::LOGISTICS_SYSTEM_PICKUP => 'Hệ thống đến lấy hàng',
    ];

    protected $fillable = [
        'order_id',
        'order_item_id',
        'user_id',
        'request_type',
        'status',
        'reason',
        'evidence_paths',
        'logistics_method',
        'admin_note',
        'rejection_reason',
        'approved_at',
        'shipping_back_at',
        'received_at',
        'inspecting_at',
        'resolved_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'evidence_paths' => 'array',
            'approved_at' => 'datetime',
            'shipping_back_at' => 'datetime',
            'received_at' => 'datetime',
            'inspecting_at' => 'datetime',
            'resolved_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getRequestTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->request_type] ?? $this->request_type;
    }

    public function getLogisticsMethodLabelAttribute(): ?string
    {
        if (! $this->logistics_method) {
            return null;
        }

        return self::LOGISTICS_LABELS[$this->logistics_method] ?? $this->logistics_method;
    }

    public function getEvidenceUrlsAttribute(): array
    {
        return collect($this->evidence_paths ?? [])
            ->filter()
            ->map(fn (string $path) => Storage::disk('public')->url($path))
            ->values()
            ->all();
    }

    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeRejected(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeMarkedShippingBack(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function canBeMarkedReceived(): bool
    {
        return $this->status === self::STATUS_SHIPPING_BACK;
    }

    public function canBeMarkedInspecting(): bool
    {
        return $this->status === self::STATUS_RECEIVED;
    }

    public function canBeRefunded(): bool
    {
        return $this->status === self::STATUS_INSPECTING && $this->request_type === self::TYPE_REFUND;
    }

    public function canBeExchanged(): bool
    {
        return $this->status === self::STATUS_INSPECTING && $this->request_type === self::TYPE_EXCHANGE;
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status, [self::STATUS_REFUNDED, self::STATUS_EXCHANGED], true);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(ReturnRequestStatusHistory::class)->latest();
    }
}

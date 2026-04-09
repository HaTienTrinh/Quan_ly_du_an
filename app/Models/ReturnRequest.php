<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        self::STATUS_PENDING => 'Chờ xử lý',
        self::STATUS_APPROVED => 'Đã duyệt',
        self::STATUS_SHIPPING_BACK => 'Đang vận chuyển về',
        self::STATUS_RECEIVED => 'Đã nhận hàng trả về',
        self::STATUS_INSPECTING => 'Đang kiểm tra',
        self::STATUS_REFUNDED => 'Đã hoàn tiền',
        self::STATUS_EXCHANGED => 'Đã xác nhận đổi hàng',
        self::STATUS_COMPLETED => 'Hoàn tất',
        self::STATUS_REJECTED => 'Đã từ chối',
    ];

    public const TYPE_LABELS = [
        self::TYPE_REFUND => 'Hoàn tiền',
        self::TYPE_EXCHANGE => 'Đổi hàng',
    ];

    public const LOGISTICS_LABELS = [
        self::LOGISTICS_CUSTOMER_SHIP => 'Khách tự gửi hàng về',
        self::LOGISTICS_SYSTEM_PICKUP => 'Cửa hàng đến lấy hàng',
    ];

    protected $fillable = [
        'order_id',
        'order_item_id',
        'user_id',
        'replacement_order_id',
        'exchange_color_id',    // Biến thể (màu+size) khách muốn đổi sang
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
        return collect($this->evidence_assets)
            ->pluck('url')
            ->all();
    }

    public function getEvidenceAssetsAttribute(): array
    {
        return collect($this->evidence_paths ?? [])
            ->filter()
            ->values()
            ->map(function (string $path, int $index) {
                $normalizedPath = $this->normalizeEvidencePath($path);
                $extension = Str::lower(pathinfo(parse_url($normalizedPath, PHP_URL_PATH) ?? $normalizedPath, PATHINFO_EXTENSION));

                return [
                    'name' => 'Minh chứng ' . ($index + 1),
                    'url' => $this->resolveEvidenceUrl($normalizedPath),
                    'type' => $this->detectEvidenceType($extension),
                ];
            })
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

    public function replacementOrder()
    {
        return $this->belongsTo(Order::class, 'replacement_order_id');
    }

    // Biến thể (màu + size) khách muốn đổi sang
    public function exchangeColor()
    {
        return $this->belongsTo(ProductColor::class, 'exchange_color_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(ReturnRequestStatusHistory::class)->latest();
    }

    // ReturnRequest có 1 Inspection (kết quả kiểm tra hàng)
    public function inspection()
    {
        return $this->hasOne(Inspection::class, 'return_id');
    }

    // ReturnRequest có 1 Reship (gửi lại hàng)
    public function reship()
    {
        return $this->hasOne(Reship::class, 'return_id');
    }

    // ReturnRequest có 1 Refund (hoàn tiền)
    public function refund()
    {
        return $this->hasOne(Refund::class, 'return_id');
    }

    private function resolveEvidenceUrl(string $path): string
    {
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $path = $this->normalizeEvidencePath($path);

        if (Str::startsWith($path, '/storage/')) {
            return asset(ltrim($path, '/'));
        }

        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    private function detectEvidenceType(string $extension): string
    {
        return match (true) {
            in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'], true) => 'image',
            in_array($extension, ['mp4', 'mov', 'webm', 'm4v'], true) => 'video',
            default => 'file',
        };
    }

    private function normalizeEvidencePath(string $path): string
    {
        $normalizedPath = trim(str_replace('\\', '/', $path));

        if ($normalizedPath === '') {
            return $normalizedPath;
        }

        if (Str::startsWith($normalizedPath, ['http://', 'https://', '/storage/', 'storage/'])) {
            return $normalizedPath;
        }

        if (Str::startsWith($normalizedPath, 'public/storage/')) {
            return 'storage/' . ltrim(Str::after($normalizedPath, 'public/storage/'), '/');
        }

        if (Str::startsWith($normalizedPath, 'storage/app/public/')) {
            return 'storage/' . ltrim(Str::after($normalizedPath, 'storage/app/public/'), '/');
        }

        if (Str::startsWith($normalizedPath, 'public/')) {
            return ltrim(Str::after($normalizedPath, 'public/'), '/');
        }

        return ltrim($normalizedPath, '/');
    }
}

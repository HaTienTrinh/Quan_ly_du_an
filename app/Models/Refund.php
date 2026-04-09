<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model Refund: thông tin hoàn tiền
class Refund extends Model
{
    protected $fillable = ['return_id', 'amount', 'status'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    // Refund thuộc về 1 ReturnRequest
    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }
}

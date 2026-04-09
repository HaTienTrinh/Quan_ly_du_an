<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model Reship: thông tin gửi lại hàng cho khách
class Reship extends Model
{
    protected $fillable = ['return_id', 'tracking_code', 'carrier', 'status'];

    // Reship thuộc về 1 ReturnRequest
    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model Inspection: kết quả kiểm tra hàng trả về
class Inspection extends Model
{
    protected $fillable = ['return_id', 'is_valid', 'note'];

    protected function casts(): array
    {
        return ['is_valid' => 'boolean'];
    }

    // Inspection thuộc về 1 ReturnRequest
    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }
}

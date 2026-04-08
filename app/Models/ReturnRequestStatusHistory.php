<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRequestStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'changed_by',
        'from_status',
        'to_status',
        'note',
    ];

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}

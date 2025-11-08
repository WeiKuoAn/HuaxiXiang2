<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerHistory extends Model
{
    use HasFactory;

    protected $table = 'customer_histories';

    protected $fillable = [
        'customer_id',
        'changed_by',
        'action',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}


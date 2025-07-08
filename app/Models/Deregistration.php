<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deregistration extends Model
{
    use HasFactory;

    protected $table = "deregistration";

    protected $fillable = [
        'number',
        'customer_id',
        'registrant',
        'ic_card',
        'pet_name',
        'variety',
        'comment',
        'created_by',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}

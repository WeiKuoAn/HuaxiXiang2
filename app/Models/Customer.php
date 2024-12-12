<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = "customer";

    protected $fillable = [
        'name',
        'mobile',
        'county',
        'district',
        'address',
        'created_up',
        'group_id',
        'bank_id',
        'bank_number',
        'commission',
        'visit'
    ];
    public function group()
    {
        return $this->belongsTo('App\Models\CustGroup', 'group_id', 'id');
    }

    public function sale_datas()
    {
        return $this->hasMany('App\Models\Sale', 'customer_id', 'id')->select('pet_name')->distinct();
    }
}

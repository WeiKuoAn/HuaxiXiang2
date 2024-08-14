<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pay;

class PayItem extends Model
{
    use HasFactory;

    protected $table = "pay_item";

    protected $fillable = [
        'pay_data_id',
        'pay_date',
        'invoice_number',
        'price',
        'invoice_type',
        'vender_id',
        'comment',
        'status'
    ];

    public function vender_data()
    {
        return $this->belongsTo('App\Models\Vender', 'vender_id', 'id');
    }

    public function pay_name(){
        return $this->hasOne('App\Models\Pay','id','pay_id');
    }

    public function pay_data(){
        return $this->hasOne('App\Models\PayData' ,'id','pay_data_id');
    }
    

}

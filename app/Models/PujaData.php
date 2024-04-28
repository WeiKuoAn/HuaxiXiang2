<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class PujaData extends Model
{
    use HasFactory;
    protected $table = "puja_data";

    protected $fillable = [
        'date',
        'puja_id',
        'type',
        'customer_id',
        'pay_id',
        'pay_method',
        'pay_price',
        'total',
        'user_id',
        'comm',
        'status',
    ];

    public function cust_name()
    {
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }

    public function puja_name()
    {
        return $this->hasOne('App\Models\Puja', 'id', 'puja_id');
    }

    public function pets()
    {
        return $this->hasMany('App\Models\PujaPet', 'puja_data_id', 'id');
    }

    public function products()
    {
        return $this->hasMany('App\Models\PujaDataAttchProduct', 'puja_data_id', 'id');
    }

    public function user_name()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function pay_type()
    {
        $pay_type = ['A' => '結清', 'C' => '訂金', 'D' => '尾款' , 'E' => '追加報名' , 'F' => '追加金紙'];
        return $pay_type[$this->pay_id];
    }

    public function type()
    {
        $type = ['0' => '一般', '1' => '贈送', '2' => '套組法會'];
        return $type[$this->type];
    }
}

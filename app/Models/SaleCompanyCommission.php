<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleCompanyCommission extends Model
{
    use HasFactory;
    protected $table = "sale_company_commission";

    protected $fillable = [
        'sale_date',
        'type',
        'customer_id',
        'sale_id',
        'company_id',
        'plan_price',
        'commission',
        'cooperation_price',
    ];

    public function company_name()
    {
        return $this->hasOne('App\Models\Customer', 'id', 'company_id');
    }

    public function user_name()
    {
        return $this->hasOne('App\Models\User', 'id', 'company_id');
    }

    public function proms()
    {
        return $this->hasMany('App\Models\Sale_prom', 'sale_id', 'id');
    }

    public function gdpapers()
    {
        return $this->hasMany('App\Models\Sale_gdpaper', 'sale_id', 'id');
    }

    public function pay_type()
    {
        $pay_type = ['A' => '結清', 'B' => '結清', 'C' => '訂金', 'D' => '尾款', 'E' => '追加'];
        return $pay_type[$this->pay_id];
    }
}

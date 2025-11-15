<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sale_promB;

class Sale extends Model
{
    use HasFactory;

    protected $table = "sale_data";

    protected $fillable = [
        'sale_on',
        'type_list',
        'user_id',
        'sale_date',
        'customer_id',
        'pet_name',
        'type',
        'plan_id',
        'plan_price',
        'before_prom_id',
        'before_prom_price',
        'pay_id',
        'pay_price',
        'variety',
        'total',
        'religion',
        'religion_other',
        'death_date',
        'send',
        'connector_address',
        'hospital_address',
        'comm',
        'check_user_id',
        'status',
        'cooperation_price',
    ];

    public function status()
    {
        $status = ['1' => '未送出對帳', '3' => '待確認對帳', '9' => '已對帳'];
        return $status[$this->status];
    }

    public function SaleChange()
    {
        return $this->hasOne('App\Models\SaleChange', 'sale_id', 'id')->orderBy('id', 'desc');
    }

    public function SalePlan()
    {
        return $this->hasOne('App\Models\SalePlan', 'sale_id', 'id')->orderBy('id', 'desc');
    }

    public function SaleSplit()
    {
        return $this->hasOne('App\Models\SaleSplit', 'sale_id', 'id')->orderBy('id', 'desc');
    }

    public function gdpapers()
    {
        return $this->hasMany('App\Models\Sale_gdpaper', 'sale_id', 'id');
    }

    public function proms()
    {
        return $this->hasMany('App\Models\Sale_prom', 'sale_id', 'id');
    }

    public function user_name()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function check_user_name()
    {
        return $this->hasOne('App\Models\User', 'id', 'check_user_id');
    }

    public function cust_name()
    {
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }

    public function hospital_address_name()
    {
        return $this->hasOne('App\Models\Customer', 'id', 'hospital_address');
    }

    public function connector_address_data()
    {
        return $this->hasOne('App\Models\SaleAddress', 'sale_id', 'id');
    }

    public function plan_name()
    {
        return $this->hasOne('App\Models\Plan', 'id', 'plan_id');
    }

    public function suit_name()
    {
        return $this->hasOne('App\Models\Suit', 'id', 'suit_id');
    }

    public function change_plan()
    {
        return $this->hasOne('App\Models\SalePlan', 'sale_id', 'id');
    }

    public function promA_name()
    {
        return $this->hasOne('App\Models\PromA', 'id', 'before_prom_id');
    }

    public function source_type()
    {
        return $this->belongsTo('App\Models\SaleSource', 'type', 'code');
    }

    public function sale_company_commission()
    {
        return $this->hasOne('App\Models\SaleCompanyCommission', 'sale_id', 'id');
    }

    public function pay_type()
    {
        $pay_type = ['A' => '結清', 'B' => '結清', 'C' => '訂金', 'D' => '尾款' , 'E' => '追加'];
        return $pay_type[$this->pay_id];
    }

    public function pay_method()
    {
        $pay_method = ['A'=>'現金','B'=>'匯款','C'=>'現金與匯款'];
        return $pay_method[$this->pay_method];
    }

    public function gdpaper_total()
    {
        $sales = Sale::where('id', $this->id)->get();
        foreach ($sales as $sale) {
            foreach ($sale->gdpapers as $gdpaper) {
                if (isset($gdpaper->gdpaper_id)) {
                    $num = $gdpaper->gdpaper_num;
                    $price = $gdpaper->gdpaper_name->price;
                    $totals[] = intval($num) * intval($price);
                }
            }
        }
        return $totals;
    }

    // public function total()
    // {
    //     $plan_price = intval($this->plan_price);
    //     $before_prom_price = intval($this->before_prom_price);
    //     $after_prom_price = Sale_promB::where('sale_id', $this->id)->sum('after_prom_total');

    //     $sales = Sale::where('id', $this->id)->get();
    //     foreach ($sales as $sale) {
    //         foreach ($sale->gdpapers as $gdpaper) {
    //             if (isset($gdpaper->gdpaper_id) && $gdpaper->gdpaper_id != null) {
    //                 $num = $gdpaper->gdpaper_num;
    //                 $price = $gdpaper->gdpaper_name->price;
    //                 if($sale->plan_id !=4){
    //                     $totals[] = intval($num) * intval($price);
    //                 }else{
    //                     $totals[] = 0;
    //                 }
    //             }
    //         }
    //     }
    //     if (isset($gdpaper->gdpaper_id) && $gdpaper->gdpaper_id != null) {
    //         $gdpaper_total = intval(array_sum($totals));
    //     }else{
    //         $gdpaper_total = 0;
    //     }
    //     return $plan_price + $before_prom_price + $after_prom_price + $gdpaper_total;
    // }

    public function price_sum(){
        
    }

    // 關聯到合約資料（一個業務單可能對應多個合約）
    public function contracts()
    {
        return $this->hasMany('App\Models\ContractData', 'sale_id');
    }

    // 關聯到重要日期資料（一個業務單可能對應一個重要日期記錄）
    public function memorialDate()
    {
        return $this->hasOne('App\Models\MemorialDate', 'sale_id');
    }

    /**
     * 取得宗教中文名稱
     */
    public function getReligionNameAttribute()
    {
        $religions = [
            'buddhism' => '佛教',
            'taoism' => '道教',
            'buddhism_taoism' => '佛道教',
            'christianity' => '基督教',
            'catholicism' => '天主教',
            'none' => '無宗教',
            'other' => '其他'
        ];

        return $religions[$this->religion] ?? '';
    }

    /**
     * 檢查是否為佛道教相關宗教
     */
    public function isBuddhistOrTaoist()
    {
        return in_array($this->religion, ['buddhism', 'taoism', 'buddhism_taoism']);
    }

    /**
     * 檢查是否有往生日期
     */
    public function hasDeathDate()
    {
        return !empty($this->death_date);
    }

    /**
     * 檢查是否需要計算重要日期
     */
    public function needsMemorialDates()
    {
        return $this->isBuddhistOrTaoist() && $this->hasDeathDate();
    }

    public function sale_souvenir_names()
    {
        return $this->hasMany('App\Models\SaleSouvenir', 'sale_id', 'id');
    }
}

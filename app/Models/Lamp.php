<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTime;

class Lamp extends Model
{
    use HasFactory;

    protected $table = "lamp";

    protected $fillable = [
        'type',
        'number',
        'customer_id',
        'pet_name',
        'mobile',
        'year',
        'start_date',
        'end_date',
        'close_date',
        'renew',
        'renew_year',
        'user_id',
        'comment',
    ];

    public function cust_name()
    {
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }

    public function type_data()
    {
        return $this->hasOne('App\Models\LampType', 'id', 'type');
    }

    public function user_name()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function getRocStartDateAttribute()
    {
        return $this->convertToROC($this->start_date);
    }
    public function getRocEndDateAttribute()
    {
        return $this->convertToROC($this->end_date);
    }
    public function getRocCloseDateAttribute()
    {
        return $this->convertToROC($this->close_date);
    }

    // 私有的轉換函數
    private function convertToROC($dateString) {
        if (!$dateString) {
            return '';
        }
        $date = new DateTime($dateString);
        $year = (int)$date->format('Y') - 1911;
        $month = $date->format('m');
        $day = $date->format('d');
        return "{$year}/{$month}/{$day}";
    }


}

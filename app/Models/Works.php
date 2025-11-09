<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class Works extends Model
{
    use HasFactory;

    protected $table = "works";
    static private $works  = "Works";

    protected $fillable = [
        'user_id',
        'worktime',
        'dutytime',
        'status',
        'remark',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static  function work_sum($workId)
    {
        $work = self::where('id',$workId)->first();
        $work_num = Carbon::parse($work->worktime)->floatDiffInHours($work->dutytime);
        
        // 滿8小時要休息1小時，所以如果工作滿9小時就要減1小時
        if ($work_num >= 9) {
            $work_num = $work_num - 1;
        }
        
        return $work_num;
    }

    public function work_total($userId)
    {
        $work_num = Carbon::parse($this->worktime)->floatDiffInHours($this->dutytime);
        
        // 滿8小時要休息1小時，所以如果工作滿9小時就要減1小時
        if ($work_num >= 9) {
            $work_num = $work_num - 1;
        }
        
        return $work_num;
    }
}

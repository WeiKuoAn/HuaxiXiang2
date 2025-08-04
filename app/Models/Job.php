<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $table = "job";

    protected $fillable = [
        'id',
        'name',
        'status',
        'state',
        'director_id',
    ];

    public function users()
    {
        return $this->hasMany('App\Models\User', 'job_id', 'id');
    }

    public function director_data()
    {
        return $this->hasOne('App\Models\Job', 'id', 'director_id');
    }

    // 取得該職稱的使用人數（只計算在職員工）
    public function getActiveUserCountAttribute()
    {
        return $this->users()->where('status', '0')->count();
    }

    // 取得該職稱的總人數（包含離職員工）
    public function getTotalUserCountAttribute()
    {
        return $this->users()->count();
    }
}

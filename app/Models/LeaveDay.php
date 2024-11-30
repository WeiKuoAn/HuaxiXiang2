<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveDay extends Model
{
    use HasFactory;

    protected $table = "leave_day";

    protected $fillable = [
        'user_id',
        'leave_day',
        'start_datetime',
        'end_datetime',
        'unit',
        'total',
        'comment',
        'director_id',
        'file',
        'state',
    ];

    public function leave_name(){
        return $this->hasOne('App\Models\Leaves','id','leave_day');
    }

    public function leave_status(){
        $leave_name = [ '1'=>'未送出' , '2'=>'待審核' , '9'=>'已核准'];
        return $leave_name[$this->state];
    }

    public function user_name(){
        return $this->hasOne('App\Models\User','id','user_id');
    }
}

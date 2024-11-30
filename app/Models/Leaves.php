<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leaves extends Model
{
    use HasFactory;

    protected $table = "leaves";

    protected $fillable = [
        'year',
        'name',
        'approved_days',
        'comment',
        'fixed',
        'seq',
        'status'
    ];

    public function settings(){
        return $this->hasMany('App\Models\LeaveSetting' ,'leave_id','id');
    }
}

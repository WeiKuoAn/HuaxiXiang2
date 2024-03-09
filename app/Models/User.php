<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'mobile',
        'address',
        'entry_date',
        'resign_date',
        'level',
        'job_id',
        'branch_id',
        'status',
        'password',
        'state',
        'seq',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function level_state(){
        $level_state = [ '0'=>'超級管理者' , '1'=>'管理者' , '2'=>'一般使用者' ];
        return $level_state[$this->level];
    }

    public function job_data()
    {
        return $this->hasOne('App\Models\Job', 'id', 'job_id');
    }

    public function branch_data()
    {
        return $this->hasOne('App\Models\Branch', 'id', 'branch_id');
    }
}

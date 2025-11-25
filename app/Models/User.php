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
        'part_time_entry_date',
        'resign_date',
        'level',
        'job_id',
        'branch_id',
        'status',
        'password',
        'bank',//銀行
        'branch',//銀行分行
        'education_school',
        'education_level',
        'is_graduated',
        'state',
        'seq',
        'comment',
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

    public function tasks()
    {
        return $this->hasMany('App\Models\Task', 'assigned_to', 'id');
    }

    public function taskItems()
    {
        return $this->hasMany(TaskItem::class, 'user_id', 'id');
    }

    public function assignedTasks()
    {
        return $this->belongsToMany(Task::class, 'task_items');
    }

    /**
     * 取得使用者的權限
     */
    public function permissions()
    {
        return $this->hasMany(UserPermission::class);
    }

    /**
     * 檢查使用者是否有特定權限
     */
    public function hasPermission($permissionName)
    {
        return UserPermission::hasPermission($this->id, $permissionName);
    }

    /**
     * 取得使用者的所有權限
     */
    public function getAllPermissions()
    {
        return UserPermission::getUserPermissions($this->id);
    }
}

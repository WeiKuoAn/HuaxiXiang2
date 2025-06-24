<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'time',
        'status',
        'note',
        'close_by',
        'created_by'
    ];

    protected $casts = [
        // cast 成 Carbon 物件，預設會解析成完整的 Y-m-d H:i:s
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
    ];

    /**
     * 與使用者多對多關聯
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function created_users()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function close_users()
    {
        return $this->belongsTo('App\Models\User', 'close_by', 'id');
    }
}

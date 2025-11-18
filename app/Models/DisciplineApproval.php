<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplineApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'discipline_id',
        'approver_id',
        'status',
        'comment',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * 關聯懲戒案件
     */
    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }

    /**
     * 關聯審核人
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}

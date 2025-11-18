<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplineDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'discipline_id',
        'incident_date',
        'note',
    ];

    protected $casts = [
        'incident_date' => 'date',
    ];

    /**
     * 關聯懲戒案件
     */
    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }
}

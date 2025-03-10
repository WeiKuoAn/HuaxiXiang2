<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prom extends Model
{
    use HasFactory;
    protected $table = "prom";

    protected $fillable = [
        'type',
        'name',
        'status',
        'seq'
    ];

    public function prom_type()
    {
        return $this->belongsTo('App\Models\PromType', 'type', 'code');
    }
}

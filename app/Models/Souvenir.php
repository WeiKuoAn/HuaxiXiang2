<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Souvenir extends Model
{
    use HasFactory;
    protected $table = "souvenir";

    protected $fillable = [
        'type',
        'name',
        'price',
        'status',
        'seq'
    ];

    public function souvenir_type()
    {
        return $this->belongsTo('App\Models\SouvenirType', 'type', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $table = "visit";

    protected $fillable = [
        'customer_id',
        'date',
        'visit_type',
        'comment',
        'supplement_items',
        'supplement_by',
        'user_id'
    ];

    protected $casts = [
        'supplement_items' => 'array',
    ];

    public function user_name(){
        return $this->hasOne('App\Models\User','id','user_id');
    }

    public function supplement_by_user(){
        return $this->hasOne('App\Models\User','id','supplement_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale_prom extends Model
{
    use HasFactory;
    protected $table = "sale_prom";

    protected $fillable = [
        'sale_id',
        'prom_type',
        'prom_id',
        'prom_total',
        'comment'
    ];

    public function prom_name()
    {
        return $this->hasOne('App\Models\Prom','id','prom_id');
    }

    public function sale_data()
    {
        return $this->hasOne('App\Models\Sale','id','sale_id');
    }

    public function souvenir()
    {
        return $this->hasOne(\App\Models\SaleSouvenir::class, 'sale_prom_id', 'id');
    }

}

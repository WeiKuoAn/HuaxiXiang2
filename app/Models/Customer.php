<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Customer extends Model
{
    use HasFactory;

    protected $table = "customer";

    protected $fillable = [
        'name',
        'mobile',
        'county',
        'district',
        'address',
        'created_up',
        'group_id',
        'bank_id',
        'bank_number',
        'commission',
        'visit_status',
        'contract_status',
        'comment'
    ];
    public function group()
    {
        return $this->belongsTo('App\Models\CustGroup', 'group_id', 'id');
    }

    public function sale_datas()
    {
        return $this->hasMany('App\Models\Sale', 'customer_id', 'id')->select('pet_name')->distinct();
    }

    protected function getBankData()
    {
        $filePath = public_path('assets/data/banks.json');

        if (!file_exists($filePath)) {
            Log::error("找不到銀行資料檔案：$filePath");
            return [];
        }

        return json_decode(file_get_contents($filePath), true) ?? [];
    }

    public function getBankName()
    {
        $bankData = $this->getBankData();
        // dd($bankData);

        // foreach ($bankData as $bankName => $info) {
        //     if (is_array($info) && isset($info['code']) && $info['code'] === $this->bank) {
        //         return $bankName;
        //     }
        // }

        return $bankData;
    }

    public function getBranchName()
    {
        $bankData = $this->getBankData();

        foreach ($bankData as $info) {
            if (is_array($info) && isset($info['code']) && $info['code'] === $this->bank) {
                if (isset($info['branches']) && is_array($info['branches'])) {
                    foreach ($info['branches'] as $branchName => $branchCode) {
                        if ($branchCode === $this->branch) {
                            return $branchName;
                        }
                    }
                }
            }
        }

        return '未知分行';
    }

    public function addresses()
    {
        return $this->hasMany('App\Models\CustomerAddress', 'customer_id', 'id');
    }

    public function mobiles()
    {
        return $this->hasMany('App\Models\CustomerMobile', 'customer_id', 'id');
    }
}

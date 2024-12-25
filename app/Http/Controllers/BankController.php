<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BankController extends Controller
{
    public function getBranches($bankCode)
    {
        try {
            $json = file_get_contents(public_path('assets/data/banks.json'));
            $banks = collect(json_decode($json, true));

            // 過濾分行資料
            $branches = $banks->filter(function ($item) use ($bankCode) {
                return $item['銀行代號/總機構代碼'] == $bankCode && $item['分支機構代號'] != '';
            });

            // 重新排序並返回純陣列格式
            return response()->json(array_values($branches->toArray()));
        } catch (\Exception $e) {
            return response()->json(['error' => '伺服器錯誤'], 500);
        }
    }
}

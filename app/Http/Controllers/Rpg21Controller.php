<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Sale;
use App\Models\Plan;

class Rpg21Controller extends Controller
{
    public function Rpg21(Request $request)
    {
        $years = range(Carbon::now()->year, 2022);
        // 獲取當前年份
        
        if(isset($request->year))
        {
            $currentYear = $request->year;
        }else{
            $currentYear = Carbon::now()->year;
        }
        // 初始化一個陣列來存儲結果
        $months = [];

        // 定義月份名稱的陣列
        $monthNames = [
            '一月', '二月', '三月', '四月', '五月', '六月',
            '七月', '八月', '九月', '十月', '十一月', '十二月'
        ];

        // 循環每個月
        for ($month = 1; $month <= 12; $month++) {
            // 獲取該月的第一天
            $startOfMonth = Carbon::create($currentYear, $month, 1)->startOfMonth();
            
            // 獲取該月的最後一天
            $endOfMonth = $startOfMonth->copy()->endOfMonth();
            
            // 將結果存儲到陣列中
            $months[] = [
                'monthName' => $monthNames[$month - 1], // 從$monthNames陣列中獲取國字月份名稱
                'start' => $startOfMonth->toDateString(),
                'end' => $endOfMonth->toDateString(),
            ];
        }
        $datas = [];

        $users = User::whereIn('job_id',[1,3,5,10])->orderBy('status')->get();

        foreach($users as $user) {
            $totalSales = 0; // 初始化用于累加销售额的变量
            $monthCount = count($months); // 获取月份数量
            $datas[$user->id] = [];
            $datas[$user->id]['name'] = $user->name;
            foreach($months as $key=>$month) {
                $monthlySales = Sale::where('user_id', $user->id)
                                      ->where('sale_date', '>=', $month['start'])
                                      ->where('sale_date', '<=', $month['end'])
                                      ->where('status', '9')
                                      ->sum('pay_price');

                
                $datas[$user->id]['months'][$key]['price'] = $monthlySales;

                $totalSales += $monthlySales; // 累加每月销售额
            }
            $monthlyCounts = Sale::where('user_id',$user->id)
                                       ->where('sale_date', '>=' ,$currentYear.'-01-01')
                                       ->where('sale_date', '<=' ,$currentYear.'-12-31')
                                       ->where('status', '9')
                                       ->whereIn('plan_id',[1,2,3])
                                       ->whereIn('pay_id', ['A', 'C'])
                                       ->count();
            $datas[$user->id]['count'] = $monthlyCounts;
            $datas[$user->id]['total_price'] = $totalSales;
            // 计算平均值
            $datas[$user->id]['month_average'] = ($monthCount != 0) ? round($totalSales / $monthCount, 0) : 0;

            $datas[$user->id]['sale_average'] = ($totalSales != 0 && $monthlyCounts != 0) ? round($totalSales / $monthlyCounts, 0) : 0;
        }
        // dd($datas);

        return view('rpg21.index')->with('years', $years)->with('datas',$datas)->with('months', $months)->with('request', $request);
    }
}

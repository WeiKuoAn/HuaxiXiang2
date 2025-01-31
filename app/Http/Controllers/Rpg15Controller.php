<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Sale;
use App\Models\Plan;

class Rpg15Controller extends Controller
{
    public function Rpg15(Request $request)
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
        $sums = [];

        $users = User::where('status', '0')->whereIn('job_id',[1,3,5])->get();

        foreach($users as $user) {
            $datas[$user->id] = [];
            $datas[$user->id]['name'] = $user->name;
            foreach($months as $key=>$month) {
                $datas[$user->id]['months'][$key]['plan_1'] = Sale::where('user_id',$user->id)->where('plan_id',1)->where('sale_date', '>=' ,$month['start'])->where('sale_date', '<=' ,$month['end'])->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();
                $datas[$user->id]['months'][$key]['plan_2'] = Sale::where('user_id',$user->id)->where('plan_id',2)->where('sale_date', '>=' ,$month['start'])->where('sale_date', '<=' ,$month['end'])->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();
                $datas[$user->id]['months'][$key]['plan_3'] = Sale::where('user_id',$user->id)->where('plan_id',3)->where('sale_date', '>=' ,$month['start'])->where('sale_date', '<=' ,$month['end'])->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();
                $sums[$key]['total_1'] = isset($sums[$key]['total_1']) ? $sums[$key]['total_1'] + $datas[$user->id]['months'][$key]['plan_1'] : $datas[$user->id]['months'][$key]['plan_1'];
                $sums[$key]['total_2'] = isset($sums[$key]['total_2']) ? $sums[$key]['total_2'] + $datas[$user->id]['months'][$key]['plan_2'] : $datas[$user->id]['months'][$key]['plan_2'];
                $sums[$key]['total_3'] = isset($sums[$key]['total_3']) ? $sums[$key]['total_3'] + $datas[$user->id]['months'][$key]['plan_3'] : $datas[$user->id]['months'][$key]['plan_3'];
            }
            $datas[$user->id]['total_1'] = Sale::where('user_id',$user->id)->where('plan_id',1)->where('sale_date', '>=' ,$currentYear.'-01-01')->where('sale_date', '<=' ,$currentYear.'-12-31')->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();
            $datas[$user->id]['total_2'] = Sale::where('user_id',$user->id)->where('plan_id',2)->where('sale_date', '>=' ,$currentYear.'-01-01')->where('sale_date', '<=' ,$currentYear.'-12-31')->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();
            $datas[$user->id]['total_3'] = Sale::where('user_id',$user->id)->where('plan_id',3)->where('sale_date', '>=' ,$currentYear.'-01-01')->where('sale_date', '<=' ,$currentYear.'-12-31')->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();
        }

        // dd($sums);
        
        return view('rpg15.index')->with('datas',$datas)->with('years',$years)->with('months',$months)->with('request',$request)->with('sums',$sums);
    }
}

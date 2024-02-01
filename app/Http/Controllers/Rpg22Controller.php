<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Prom;

class Rpg22Controller extends Controller
{
    public function Rpg22(Request $request)
    {
        $years = range(Carbon::now()->year, 2022);
        // dd($request);
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
        // dd($months);
        

        //取得專員資料，並取得老闆和專員的job_id
        $users = User::where('status', '0')->whereIn('job_id',[1,3,5])->get();
        $datas = [];
        $sums = [];
        
        $proms = Prom::where('status','up')->whereIn('id',[4,14,17,24,28])->get();

        foreach($proms as $prom)
        {
            $datas[$prom->id]['id'] = $prom->id;
            $datas[$prom->id]['name'] = $prom->name;
            foreach($months as $key=>$month)
            {
                $datas[$prom->id]['months'][$key]['monthName'] = $month['monthName']; 
                $datas[$prom->id]['months'][$key]['price'] = DB::table('sale_data')
                                                                ->join('sale_prom','sale_prom.sale_id', '=' , 'sale_data.id')
                                                                ->where('sale_prom.prom_id',$prom->id)
                                                                ->where('sale_data.sale_date', '>=', $month['start'])
                                                                ->where('sale_data.sale_date', '<=', $month['end'])
                                                                ->where('sale_data.status', '9')
                                                                ->sum('sale_prom.prom_total');
                $datas[$prom->id]['months'][$key]['count'] = DB::table('sale_data')
                                                                ->join('sale_prom','sale_prom.sale_id', '=' , 'sale_data.id')
                                                                ->where('sale_prom.prom_id',$prom->id)
                                                                ->where('sale_data.sale_date', '>=', $month['start'])
                                                                ->where('sale_data.sale_date', '<=', $month['end'])
                                                                ->where('sale_data.status', '9')
                                                                ->count();
            }
        }

        foreach($datas as $key=>$data)
        {
            foreach($data['months'] as $month)
            {
                if(isset($sums[$key]['count'])){
                    $sums[$key]['count'] += $month['count'];
                }else{
                    $sums[$key]['count'] = $month['count'];
                }
                if(isset($sums[$key]['total_price'])){
                    $sums[$key]['total_price'] += $month['price'];
                }else{
                    $sums[$key]['total_price'] = $month['price'];
                }
            }
        }
        // dd($sums);

        return view('rpg22.index')->with('users', $users)->with('years', $years)
                                  ->with('request',$request)->with('datas',$datas)
                                  ->with('sums',$sums)->with('months',$months);
    }

    public function detail(Request $request, $month , $prom_id)
    {
        $prom = Prom::where('id',$prom_id)->first();
        $search_year = $request->year;
        if(!isset($search_year)){
            $search_year = Carbon::now()->year;
        }

        $startOfMonth = Carbon::create($search_year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        //取得專員資料，並取得老闆和專員的job_id
        $users = User::where('status', '0')->whereIn('job_id',[1,3,5])->orderby('job_id')->get();


        $datas = [];
        $sums = [];

        foreach($users as $user)
        {
            $promDatas = DB::table('sale_data')
                        ->join('sale_prom','sale_prom.sale_id', '=' , 'sale_data.id')
                        ->leftjoin('users','users.id', '=' , 'sale_data.user_id')
                        ->leftJoin('plan','plan.id', '=' , 'sale_data.plan_id')
                        ->where('sale_prom.prom_id', $prom_id)
                        ->where('sale_data.sale_date', '>=', $startOfMonth)
                        ->where('sale_data.sale_date', '<=', $endOfMonth)
                        ->where('sale_data.user_id', $user->id)
                        ->where('sale_data.status', '9')
                        ->orderBy('sale_data.sale_date', 'desc')
                        ->select('sale_data.*','sale_prom.*','users.*','plan.name as plan_name')
                        ->get();

            if (!$promDatas->isEmpty()) {
                $datas[$user->id]['name'] = $user->name;
                $datas[$user->id]['prom_datas'] = $promDatas;
            }
        }

        foreach($datas as $user_id => $data) {
            foreach($data['prom_datas'] as $prom_data)
            {
                if(isset($datas[$user_id]['total_count'])){
                    $datas[$user_id]['total_count']++;
                }else{
                    $datas[$user_id]['total_count']=1;
                }
                if(isset($datas[$user_id]['prom_total'])){
                    $datas[$user_id]['prom_total'] += $prom_data->prom_total;
                }else{
                    $datas[$user_id]['prom_total']= $prom_data->prom_total;
                }
            }
        }
        $sums['count'] = 0;
        $sums['total'] = 0;
        foreach($datas as $key=>$data)
        {
            if(!isset($sums['count'])){
                $sums['count'] = 1;
            }else{
                $sums['count'] += $data['total_count'];
            }
            if(isset($sums['total'])){
                $sums['total'] += $data['prom_total'];
            }else{
                $sums['total'] = $data['prom_total'];
            }
        }
        // dd($sums);
        
        return view('rpg22.detail')->with('request',$request)->with('datas',$datas)->with('sums',$sums)->with('prom',$prom);
    }
}

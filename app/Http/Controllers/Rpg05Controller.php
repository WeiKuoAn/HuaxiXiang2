<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\PayData;
use App\Models\IncomeData;

class Rpg05Controller extends Controller
{
    public function Rpg05(Request $request)
    {
        $first_date = Carbon::now()->firstOfMonth();
        $last_date = Carbon::now()->lastOfMonth();

        // $after_date = Carbon::now()->firstOfMonth();
        // $before_date = Carbon::now()->lastOfMonth();

        $after_date = Carbon::now()->firstOfMonth();
        $before_date = Carbon::now()->lastOfMonth();
        $periods = CarbonPeriod::create($after_date, $before_date);

        $sale_datas = Sale::where('sale_date','>=',$after_date)->where('sale_date','<=',$before_date)->get();
        $income_datas = IncomeData::where('income_date','>=',$after_date)->where('income_date','<=',$before_date)->get();
        $pay_datas = PayData::where('pay_date','>=',$after_date)->where('pay_date','<=',$before_date)->get();

        if($request){
            $after_date = $request->after_date;
            if($after_date){
                 $sale_datas = Sale::where('sale_date','>=',$after_date)->get();
                 $income_datas = IncomeData::where('income_date','>=',$after_date)->get();
                 $pay_datas = PayData::where('pay_date','>=',$after_date)->get();
            }
            $before_date = $request->before_date;
            if($before_date){
                 $sale_datas = Sale::where('sale_date','<=',$before_date)->get();
                 $income_datas = IncomeData::where('income_date','<=',$before_date)->get();
                 $pay_datas = PayData::where('pay_date','<=',$before_date)->get();
            }
            if($after_date && $before_date){
                 $sale_datas = Sale::where('sale_date','>=',$after_date)->where('sale_date','<=',$before_date)->get();
                 $income_datas = IncomeData::where('income_date','>=',$after_date)->where('income_date','<=',$before_date)->get();
                 $pay_datas = PayData::where('pay_date','>=',$after_date)->where('pay_date','<=',$before_date)->get();
            }
            if($after_date && $before_date){
                $periods = CarbonPeriod::create( $request->after_date,  $request->before_date);
            }
        }

        $datas = [];
        $sums = [];

        foreach($periods as $period){
            $datas[$period->format("Y-m-d")]['sum_total'] = 0;
            $datas[$period->format("Y-m-d")]['income_total'] = 0;
            $datas[$period->format("Y-m-d")]['pay_total'] = 0;
        }
        
        //業務收入
        foreach($sale_datas as $sale_data){
            $datas[$sale_data->sale_date]['sum_total'] += $sale_data->pay_price;
        }
        //其他收入
        foreach($income_datas as $income_data){
            $datas[$income_data->income_date]['income_total'] += $income_data->price;
        }
        //其他支出
        foreach($pay_datas as $pay_data){
            $datas[$pay_data->pay_date]['pay_total'] += $pay_data->price;
        }
        // dd($datas);
        

        $sums['sum_total'] = 0;
        $sums['income_total'] = 0;
        $sums['pay_total'] = 0;
        $sums['total'] = 0;
        foreach($datas as $date=>$data){
            $sums['sum_total'] += $data['sum_total'];
            $sums['income_total'] += $data['income_total'];
            $sums['pay_total'] += $data['pay_total'];
            $sums['all_income_total'] = $sums['sum_total'] + $sums['income_total'];
            $sums['total'] = $sums['sum_total'] + $sums['income_total'] - $sums['pay_total'];
            $sums[$date]['day_income'] = $data['sum_total'] + $data['income_total'];
            $sums[$date]['day_total'] =  $sums['sum_total'] +  $sums['income_total'] - $sums['pay_total'];
        }

        // foreach($datas as $date=>$data){
        //     $sums['sum_total'] += $data['sum_total'];
        //     $sums['income_total'] += $data['income_total'];
        //     $sums['pay_total'] += $data['pay_total'];
        //     $sums['total'] = $sums['sum_total'] + $sums['income_total'] - $sums['pay_total'];
        //     $sums[$date]['day_total'] = $data['sum_total'] + $data['income_total'] - $data['pay_total'];
        // }

        // dd($sums);


        return view('rpg05')->with('request',$request)
                            ->with('first_date',$first_date)
                            ->with('last_date',$last_date)
                            ->with('datas',$datas)
                            ->with('periods',$periods)
                            ->with('sums',$sums);
    }
}

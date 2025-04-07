<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Sale;

class Rpg08Controller extends Controller
{
    public function Rpg08(Request $request)
    {
        //test
        $years = range(Carbon::now()->year, 2022);

        // if (isset($request)) {
        //     $search_year = $request->year;
        //     $search_month = $request->month;
        //     $firstDay = Carbon::createFromDate($search_year , $search_month)->firstOfMonth();
        //     $lastDay = Carbon::createFromDate($search_year , $search_month)->lastOfMonth();
        // } else {
        //     // $firstDay = Carbon::now()->firstOfMonth();
        //     // $lastDay = Carbon::now()->lastOfMonth();
        //     $firstDay = '2023-01-01';
        //     $lastDay = '2023-01-31';
        // }

        $firstDay = '2023-01-01';
        $lastDay = '2023-01-31';
        $periods = CarbonPeriod::create($firstDay, $lastDay);
        $totals = Sale::where('sale_date','>=',$firstDay)->where('sale_date','<=',$lastDay)->count();
        $datas = [];

        $sale_datas = Sale::where('sale_date','>=',$firstDay)->where('sale_date','<=',$lastDay)->get();

        foreach($sale_datas as $sale_data)
        {
            $datas['paln_datas'][$sale_data->plan_id] = 0;
            $datas['before_prom_datas'][$sale_data->before_prom_id] = 0;
            foreach ($sale_data->promBs as $promB)
            {
                $datas['after_prom_datas'][$promB->after_prom_id] = 0;
            }
        }

        foreach($sale_datas as $sale_data)
        {
            $datas['paln_datas'][$sale_data->plan_id]++;
            $datas['before_prom_datas'][$sale_data->before_prom_id]++;
            foreach ($sale_data->promBs as $promB)
            {
                $datas['after_prom_datas'][$promB->after_prom_id]++;
            }
        }



        dd($datas);

        $datas = [];

        return view('rpg08.index')->with('datas', $datas)->with('totals',$totals)->with('request',$request);
    }
}

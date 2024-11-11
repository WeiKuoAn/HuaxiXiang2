<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Prom;
use App\Models\Sale;
use App\Models\Plan;
use App\Models\SaleSource;

class Rpg27Controller extends Controller
{
    public function rpg27(Request $request)
    {
        $years = range(Carbon::now()->year, 2022);
        // dd($request);
        if (isset($request->year)) {
            $currentYear = $request->year;
        } else {
            $currentYear = Carbon::now()->year;
        }

        // 初始化一個陣列來存儲結果
        $months = [];

        // 定義月份名稱的陣列
        $monthNames = [
            '一月',
            '二月',
            '三月',
            '四月',
            '五月',
            '六月',
            '七月',
            '八月',
            '九月',
            '十月',
            '十一月',
            '十二月'
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

        $sources = SaleSource::where('status', 'up')->orderby('id')->get();

        foreach ($months as $key => $month) {
            // $datas[$key]['monthName'] = $month['monthName'];
            foreach ($sources as $source) {
                $datas[$source->code]['name'] = $source->name;
                $datas[$source->code]['months'][$key]['count'] = 0;
                $sums[$source->code]['count'] = 0;
            }
            $sales = Sale::where('sale_date', '>=', $month['start'])
                ->where('sale_date', '<=', $month['end'])
                ->whereNotNull('type')
                ->where('type', '!=', '')
                ->where('status', '9')
                ->whereIn('pay_id', ['A', 'C'])
                ->get();

            foreach ($sales as $sale) {
                if (!isset($datas[$sale->type]['months'][$key]['count'])) {
                    $datas[$sale->type]['months'][$key]['count'] = 0;
                }
                $datas[$sale->type]['months'][$key]['count']++;
                $datas[$sale->type]['months'][$key]['data'][] = $sale->id;
            }
        }
        // dd($datas);

        return view('rpg27.index')->with('datas', $datas)
            ->with('sums', $sums)
            ->with('sources', $sources)
            ->with('years', $years)
            ->with('request', $request)
            ->with('months', $months);
    }

    public function detail(Request $request, $month , $source_id)
    {
        $sources = SaleSource::where('status', 'up')->orderby('id')->get();
        foreach($sources as $source){
            $source_name[$source->code] = $source->name; 
        }
        $search_year = $request->year;
        if(!isset($search_year)){
            $search_year = Carbon::now()->year;
        }

        $startOfMonth = Carbon::create($search_year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $sources = SaleSource::where('status', 'up')->orderby('id')->get();
        foreach($sources as $source){
            $source_name[$source->code] = $source->name; 
        }
        $datas = Sale::where('sale_date','>=',$startOfMonth)->where('sale_date','<=',$endOfMonth)->where('type',$source_id)->where('status', '9')->whereIn('pay_id', ['A', 'C'])->get();
        return view('rpg27.detail')->with('datas',$datas)
                                   ->with('source_name',$source_name);
    }
}

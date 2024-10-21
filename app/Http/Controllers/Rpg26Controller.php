<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\IncomeData;
use App\Models\PujaData;
use App\Models\Plan;
use App\Models\PayData;
use App\Models\PayItem;
use App\Models\Sale_gdpaper;
use Illuminate\Support\Facades\Redis;
use App\Models\Prom;
use Illuminate\Support\Facades\DB;

class Rpg26Controller extends Controller
{
    public function rpg26(Request $request)
    {
        $years = range(Carbon::now()->year, 2022);

        $search_year = $request->year;
        $before_year = $search_year - 1;

        if (!isset($search_year)) {
            // $search_year = '2022';
            $search_year = Carbon::now()->year;
            $before_year = $search_year - 1;
        }

        $months = [
            '01' => ['month' => '一月', 'start_date' => $search_year . '-01-01', 'end_date' => $search_year . '-01-31'],
            '02' => ['month' => '二月', 'start_date' => $search_year . '-02-01', 'end_date' => $search_year . '-02-29'],
            '03' => ['month' => '三月', 'start_date' => $search_year . '-03-01', 'end_date' => $search_year . '-03-31'],
            '04' => ['month' => '四月', 'start_date' => $search_year . '-04-01', 'end_date' => $search_year . '-04-30'],
            '05' => ['month' => '五月', 'start_date' => $search_year . '-05-01', 'end_date' => $search_year . '-05-31'],
            '06' => ['month' => '六月', 'start_date' => $search_year . '-06-01', 'end_date' => $search_year . '-06-30'],
            '07' => ['month' => '七月', 'start_date' => $search_year . '-07-01', 'end_date' => $search_year . '-07-31'],
            '08' => ['month' => '八月', 'start_date' => $search_year . '-08-01', 'end_date' => $search_year . '-08-31'],
            '09' => ['month' => '九月', 'start_date' => $search_year . '-09-01', 'end_date' => $search_year . '-09-30'],
            '10' => ['month' => '十月', 'start_date' => $search_year . '-10-01', 'end_date' => $search_year . '-10-31'],
            '11' => ['month' => '十一月', 'start_date' => $search_year . '-11-01', 'end_date' => $search_year . '-11-30'],
            '12' => ['month' => '十二月', 'start_date' => $search_year . '-12-01', 'end_date' => $search_year . '-12-31'],
        ];

        $datas = [];
        $sums = [];
        // $titles = [];

        $proms = Prom::where('status', 'up')->orderby('seq')->get();
        // foreach ($proms as $prom) {
        //     $titles[$prom->id] = $prom->name;
        // }

        foreach ($months as $key => $month) {
            $datas[$key]['month'] = $month['month'];
            $datas[$key]['start_date'] = $this->date_text($month['start_date']);
            $datas[$key]['end_date'] = $this->date_text($month['end_date']);
            //抓取每月起始至末的日期，並取出（個別、團體、流浪）方案且不是尾款的單。
            $datas[$key]['count'] = Sale::where('status', '9')->where('sale_date', '>=', $month['start_date'])->where('sale_date', '<=', $month['end_date'])->whereIn('plan_id', [1, 2, 3])->whereIn('pay_id', ['A', 'C'])->count();
            //安葬服務金額
            $datas[$key]['sale_promA'] = DB::table('sale_data')
                ->leftjoin('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
                ->whereNotNull('sale_prom.prom_id')
                ->where('sale_data.sale_date', '>=', $month['start_date'])->where('sale_data.sale_date', '<=', $month['end_date'])
                ->where('sale_prom.prom_type', 'A')
                ->where('sale_data.status', '9')
                ->sum('sale_prom.prom_total');

            //後續服務金額
            $datas[$key]['sale_promB'] = DB::table('sale_data')
            ->leftjoin('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
            ->whereNotNull('sale_prom.prom_id')
            ->where('sale_data.sale_date', '>=', $month['start_date'])->where('sale_data.sale_date', '<=', $month['end_date'])
            ->where('sale_prom.prom_type', 'B')
            ->where('sale_data.status', '9')
            ->sum('sale_prom.prom_total');

            $datas[$key]['sale_promC'] = DB::table('sale_data')
                ->leftjoin('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
                ->whereNotNull('sale_prom.prom_id')
                ->where('sale_data.sale_date', '>=', $month['start_date'])->where('sale_data.sale_date', '<=', $month['end_date'])
                ->where('sale_prom.prom_type', 'C')
                ->where('sale_data.status', '9')
                ->sum('sale_prom.prom_total');

                
            //方案金額
            $datas[$key]['gdpaper_price'] = DB::table('sale_gdpaper')
                ->join('sale_data', 'sale_gdpaper.sale_id', '=', 'sale_data.id')
                ->where('sale_data.status', '9')
                ->whereBetween('sale_data.sale_date', [$month['start_date'], $month['end_date']])
                ->sum('sale_gdpaper.gdpaper_total');

            //抓取每月起始至末的日期並取出每張單的收入金額
            $datas[$key]['cur_sale_price'] = Sale::where('status', '9')->where('sale_date', '>=', $month['start_date'])->where('sale_date', '<=', $month['end_date'])->sum('pay_price');
            $datas[$key]['cur_income_price'] = IncomeData::where('income_date','>=',$month['start_date'])->where('income_date','<=',$month['end_date'])->sum('price');
            $datas[$key]['cur_price_amount'] = $datas[$key]['cur_income_price'] + $datas[$key]['cur_sale_price'];
            // 計算方案價格 = cur_sale_price - gdpaper_price - sale_promC - sale_promB - sale_promA
            $datas[$key]['plan_price'] = $datas[$key]['cur_sale_price']
                - $datas[$key]['gdpaper_price']
                - $datas[$key]['sale_promC']
                - $datas[$key]['sale_promB']
                - $datas[$key]['sale_promA'];

            //支出
            $datas[$key]['cur_pay_data_price'] = PayData::where('status','1')->where('pay_date','>=',$month['start_date'])->where('pay_date','<=',$month['end_date'])->where('created_at','<=','2023-01-08 14:22:21')->sum('price');
            $datas[$key]['cur_pay_item_price'] = PayItem::where('status','1')->where('pay_date','>=',$month['start_date'])->where('pay_date','<=',$month['end_date'])->whereNotIn('pay_id',['23'])->sum('price');
            $datas[$key]['cur_pay_price'] = $datas[$key]['cur_pay_data_price']+$datas[$key]['cur_pay_item_price'];
            $datas[$key]['cur_month_total'] = $datas[$key]['cur_price_amount'] - $datas[$key]['cur_pay_price'];
        }

        $sums['total_count'] = 0;
        $sums['total_sale_promA'] = 0;
        $sums['total_sale_promB'] = 0;
        $sums['total_sale_promC'] = 0;
        $sums['total_gdpaper_price'] = 0;
        $sums['total_price_amount'] = 0;
        $sums['total_plan_price'] = 0;
        $sums['total_pay_price'] = 0;
        $sums['total_month_total'] = 0;

        foreach($datas as $data)
        {
            $sums['total_count'] += $data['count'];
            $sums['total_sale_promA'] += $data['sale_promA'];
            $sums['total_sale_promB'] += $data['sale_promB'];
            $sums['total_sale_promC'] += $data['sale_promC'];
            $sums['total_gdpaper_price'] += $data['gdpaper_price'];
            $sums['total_price_amount'] += $data['cur_price_amount'];
            $sums['total_plan_price'] += $data['plan_price'];
            $sums['total_pay_price'] += $data['cur_pay_price'];
            $sums['total_month_total'] += $data['cur_month_total'];
        }

        // dd($datas);


        // $sums['total_count'] = 0;
        // $sums['total_puja_count'] = 0;
        // $sums['total_income_price'] = 0;
        // $sums['total_puja_price'] = 0;
        // $sums['total_sale_price'] = 0;
        // $sums['total_price_amount'] = 0;
        // $sums['total_pay_price'] = 0;
        // $sums['total_month_total'] = 0;

        // foreach ($datas as $key => $data) {
        //     $sums['total_count'] += $data['cur_count'];
        // }
        // dd($sums);
        return view('rpg26.index')->with('datas', $datas)
            ->with('request', $request)
            ->with('search_year', $search_year)
            ->with('years', $years)
            ->with('sums', $sums)
        ;
    }

    private function date_text($date) //民國顯示
    {
        $date_text = "";

        if ($date) {
            $month = mb_substr($date, 5, 2);
            $day = mb_substr($date, 8, 2);

            $date_text = $month . '/' . $day;
        }

        return $date_text;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class Rpg30Controller extends Controller
{
    public function rpg30(Request $request)
    {
        $years = range(Carbon::now()->year, 2022);
        $search_year = $request->year;

        if (!isset($search_year)) {
            // $search_year = '2022';
            $search_year = Carbon::now()->year;
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

        $seasons = [
            '01' => ['month' => '一月', 'start_date' => $search_year . '-01-01', 'end_date' => $search_year . '-03-31'],
            '04' => ['month' => '四月', 'start_date' => $search_year . '-04-01', 'end_date' => $search_year . '-06-30'],
            '07' => ['month' => '七月', 'start_date' => $search_year . '-07-01', 'end_date' => $search_year . '-09-30'],
            '10' => ['month' => '十月', 'start_date' => $search_year . '-10-01', 'end_date' => $search_year . '-12-31'],
        ];

        $datas = [];
        foreach ($months as $key => $month) {
            $datas[$key]['month'] = $month['month'];

            //1.金紙（金紙的賣出總額）
            $datas[$key]['gdpaper_month'] = DB::table('sale_data')
                ->join('sale_gdpaper', 'sale_gdpaper.sale_id', '=', 'sale_data.id')
                ->where('sale_data.sale_date', '>=', $month['start_date'])
                ->where('sale_data.sale_date', '<=', $month['end_date'])
                ->where('sale_data.status', '9')
                ->where('sale_data.type_list', 'dispatch')
                ->sum('sale_gdpaper.gdpaper_total');
            // //2.花樹葬（花樹葬的數量）
            $datas[$key]['flower_month'] = DB::table('sale_data')
                ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
                ->where('sale_data.sale_date', '>=', $month['start_date'])
                ->where('sale_data.sale_date', '<=', $month['end_date'])
                ->where('sale_data.status', '9')
                ->where('sale_prom.prom_id', '15')
                ->whereNotNull('sale_prom.prom_id')
                ->where('sale_prom.prom_id', '<>', '')
                ->count();
            // //3.盆栽（盆栽的數量）
            $datas[$key]['potted_plant_month'] = DB::table('sale_data')
                ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
                ->where('sale_data.sale_date', '>=', $month['start_date'])
                ->where('sale_data.sale_date', '<=', $month['end_date'])
                ->where('sale_data.status', '9')
                ->where('sale_prom.prom_id', '16')
                ->whereNotNull('sale_prom.prom_id')
                ->where('sale_prom.prom_id', '<>', '')
                ->count();

            //4.骨灰罐（骨灰罐的總額）
            $datas[$key]['urn_month'] = DB::table('sale_data')
                ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
                ->where('sale_data.sale_date', '>=', $month['start_date'])
                ->where('sale_data.sale_date', '<=', $month['end_date'])
                ->where('sale_data.status', '9')
                ->where('sale_prom.prom_id', '14')
                ->whereNotNull('sale_prom.prom_id')
                ->where('sale_prom.prom_id', '<>', '')
                ->sum('sale_prom.prom_total');

            // dd($flower_month);

            //5.指定款獎金（VVG+拍拍+寵物花忠+vvg紀念品+指定款紀念品+[玉罐+大理石罐]）加總
            $datas[$key]['specify_month'] = DB::table('sale_data')
                ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
                ->where('sale_data.sale_date', '>=', $month['start_date'])
                ->where('sale_data.sale_date', '<=', $month['end_date'])
                ->where('sale_data.status', '9')
                ->whereIn('sale_prom.prom_id', [28, 20, 24, 32])
                ->whereNotNull('sale_prom.prom_id')
                ->where('sale_prom.prom_id', '<>', '')
                ->sum('sale_prom.prom_total');
        }

        $season_datas = [];
        foreach ($seasons as $key => $season) {
            $season_datas[$key]['month'] = $season['month'];
            $season_datas[$key]['suit_season'] = DB::table('sale_data')
                ->where('sale_data.sale_date', '>=', $season['start_date'])
                ->where('sale_data.sale_date', '<=', $season['end_date'])
                ->where('sale_data.status', '9')
                ->whereNotIn('sale_data.suit_id', [1])
                ->whereNotNull('sale_data.suit_id')
                // 排除空字串，如果是數值型別改用 ->where('sale_data.suit_id', '>', 0)
                ->where('sale_data.suit_id', '<>', '')
                ->count();
            $season_datas[$key]['urn_souvenir_season'] = DB::table('sale_data')
                ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
                ->where('sale_data.sale_date', '>=', $season['start_date'])
                ->where('sale_data.sale_date', '<=', $season['end_date'])
                ->where('sale_data.status', '9')
                ->whereIn('sale_prom.prom_id', [14, 4])
                ->whereNotNull('sale_prom.prom_id')
                ->where('sale_prom.prom_id', '<>', '')
                ->sum('sale_prom.prom_total');
        }
        // dd($season_datas);
        $sums = [];



        return view('rpg30.index')->with('datas', $datas)
            ->with('request', $request)
            ->with('search_year', $search_year)
            ->with('years', $years)
            ->with('sums', $sums)
            ->with('months', $months)
            ->with('season_datas', $season_datas);
    }
}

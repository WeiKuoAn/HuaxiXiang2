<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Lamp;
use App\Models\Sale;
use App\Models\Plan;
use App\Models\LampType;

class Rpg31Controller extends Controller
{
    public function rpg31(Request $request)
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

        $lampTypes = LampType::where('status', 'up')->orderby('id')->get();

        foreach ($months as $key => $month) {
            // $datas[$key]['monthName'] = $month['monthName'];
            foreach ($lampTypes as $lampType) {
                $datas[$lampType->id]['name'] = $lampType->name;
                $datas[$lampType->id]['months'][$key]['count'] = 0;
                $sums[$lampType->id]['count'] = 0;
            }
            $lamps = Lamp::where('start_date', '>=', $month['start'])
                ->where('start_date', '<=', $month['end'])
                ->get();

            foreach ($lamps as $lamp) {
                if (!isset($datas[$lamp->type]['months'][$key]['count'])) {
                    $datas[$lamp->type]['months'][$key]['count'] = 0;
                }
                $datas[$lamp->type]['months'][$key]['count']++;
            }
        }
        // dd($datas);

        foreach ($datas as $key => $data) {
            $sums[$key]['count'] = 0;
            foreach ($data['months'] as $month) {
                $sums[$key]['count'] += $month['count'];
            }
        }


        return view('rpg31.index')->with('datas', $datas)
            ->with('sums', $sums)
            ->with('lampTypes', $lampTypes)
            ->with('years', $years)
            ->with('request', $request)
            ->with('months', $months);
    }

    public function detail(Request $request, $month, $lamp_type)
    {
        $lampTypes = LampType::where('status', 'up')->orderby('id')->get();
        foreach ($lampTypes as $lampType) {
            $lampType_name[$lampType->id] = $lampType->name;
        }
        $search_year = $request->year;
        if (!isset($search_year)) {
            $search_year = Carbon::now()->year;
        }

        $startOfMonth = Carbon::create($search_year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $datas = Lamp::where('start_date', '>=', $startOfMonth)
            ->where('start_date', '<=', $endOfMonth)
            ->where('type', $lamp_type)
            ->get();

        return view('rpg31.detail')->with('datas', $datas)
            ->with('lampType_name', $lampType_name);
    }
}

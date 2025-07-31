<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Plan;
use App\Models\Prom;
use App\Models\Sale;
use App\Models\SaleSource;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Rpg27Controller extends Controller
{
    public function rpg27(Request $request)
    {
        $years = range(Carbon::now()->year, 2022);
        if (isset($request->year) && isset($request->month)) {
            $search_year = $request->year;
            $search_month = $request->month;
            $firstDay = Carbon::createFromDate($search_year, $search_month, 1)->firstOfMonth();
            $lastDay = Carbon::createFromDate($search_year, $search_month, 1)->lastOfMonth();
        } else {
            $firstDay = Carbon::now()->startOfYear();
            $lastDay = Carbon::now()->endOfYear();
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

        // // 循環每個月
        // for ($month = 1; $month <= 12; $month++) {
        //     // 獲取該月的第一天
        //     $startOfMonth = Carbon::create($currentYear, $month, 1)->startOfMonth();

        //     // 獲取該月的最後一天
        //     $endOfMonth = $startOfMonth->copy()->endOfMonth();

        //     // 將結果存儲到陣列中
        //     $months[] = [
        //         'monthName' => $monthNames[$month - 1], // 從$monthNames陣列中獲取國字月份名稱
        //         'start' => $startOfMonth->toDateString(),
        //         'end' => $endOfMonth->toDateString(),
        //     ];
        // }

        $datas = [];
        $sums = [];

        $sources = SaleSource::where('status', 'up')->orderby('id')->whereIn('code', ['H', 'B', 'G', 'Salon', 'dogpark', 'other'])->get();
        foreach ($sources as $source) {
            $datas[$source->code]['name'] = $source->name;
            $sums[$source->code]['count'] = 0;
            $sales = Sale::join('sale_company_commission', 'sale_data.id', '=', 'sale_company_commission.sale_id')
                ->where('sale_data.sale_date', '>=', $firstDay)
                ->where('sale_data.sale_date', '<=', $lastDay)
                ->whereNotNull('sale_data.type')
                ->where('sale_data.type', '!=', '')
                ->where('sale_data.type', $source->code)
                ->where('sale_data.status', '9')
                ->whereIn('sale_data.pay_id', ['A', 'C'])
                ->get();
            $datas[$source->code]['count'] = $sales->count();
            foreach ($sales as $sale) {
                if (!isset($datas[$source->code]['items'][$sale->company_id])) {
                    $datas[$source->code]['items'][$sale->company_id] = $sale->company_name;
                    $datas[$source->code]['items'][$sale->company_id]['name'] = Customer::find($sale->company_id)->name;
                    $datas[$source->code]['items'][$sale->company_id]['count'] = 1;
                } else {
                    $datas[$source->code]['items'][$sale->company_id]['count']++;
                }
            }

            // 對每個來源的公司項目按案件量排序（從多到少）
            if (isset($datas[$source->code]['items'])) {
                uasort($datas[$source->code]['items'], function ($a, $b) {
                    return $b['count'] - $a['count'];
                });
            }
        }

        // dd($datas);

        return view('rpg27.index')
            ->with('datas', $datas)
            ->with('sums', $sums)
            ->with('sources', $sources)
            ->with('years', $years)
            ->with('request', $request)
            ->with('months', $months);
    }

    public function detail(Request $request, $year, $month, $source_id, $company_id)
    {
        // 確保 year 和 month 不為 null
        $search_year = $year ?: Carbon::now()->year;
        $search_month = $month ?: 'all';
        
        $sources = SaleSource::where('status', 'up')->orderby('id')->get();
        foreach ($sources as $source) {
            $source_name[$source->code] = $source->name;
        }

        if ($search_month == 'all') {
            $startOfMonth = Carbon::create($search_year, 1, 1)->startOfMonth();
            $endOfMonth = Carbon::create($search_year, 12, 31)->endOfMonth();
        } else {
            $startOfMonth = Carbon::create($search_year, $search_month, 1)->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();
        }
        
        $datas = Sale::join('sale_company_commission', 'sale_data.id', '=', 'sale_company_commission.sale_id')
            ->where('sale_data.sale_date', '>=', $startOfMonth)
            ->where('sale_data.sale_date', '<=', $endOfMonth)
            ->where('sale_data.type', $source_id)
            ->where('sale_company_commission.company_id', $company_id)
            ->where('sale_data.status', '9')
            ->whereIn('sale_data.pay_id', ['A', 'C'])
            ->select('sale_data.*')
            ->get();
        return view('rpg27.detail')
            ->with('datas', $datas)
            ->with('source_name', $source_name)
            ->with('year', $search_year)
            ->with('month', $search_month)
            ->with('source_id', $source_id)
            ->with('company_id', $company_id);
    }
}

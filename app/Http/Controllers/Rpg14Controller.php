<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Plan;
use App\Models\SaleSource;
use Illuminate\Support\Facades\Redis;
use Intervention\Image\Colors\Rgb\Channels\Red;
use Illuminate\Support\Facades\DB;

class Rpg14Controller extends Controller
{
    public function rpg14(Request $request)
    {
        $years = range(Carbon::now()->year, 2022);
        if (isset($request)) {
            $search_year = $request->year;
            $search_month = $request->month;
            $firstDay = Carbon::createFromDate($search_year, $search_month, 1)->firstOfMonth();
            $lastDay = Carbon::createFromDate($search_year, $search_month, 1)->lastOfMonth();
            $month = Carbon::createFromDate($search_year, $search_month, 1)->format('Y-m');
        } else {
            $firstDay = Carbon::now()->firstOfMonth();
            $lastDay = Carbon::now()->lastOfMonth();
            $month = Carbon::createFromDate($firstDay, $lastDay, 1)->format('Y-m');
        }
        $periods = CarbonPeriod::create($firstDay, $lastDay);

        //單純有拜訪的
        $visit_sources = SaleSource::where('status', 'up')->whereIn('code', ['H', 'Salon', 'B', 'G', 'dogpark', 'other'])->orderby('id')->get();


        $sources = SaleSource::where('status', 'up')->orderby('id')->get();
        foreach ($periods as $period) {
            foreach ($sources as $source) {
                $datas[$period->format("Y-m-d")][$source->code]['name'] = $source->name;
                $datas[$period->format("Y-m-d")][$source->code]['count'] = 0;
                $sums[$source->code]['count'] = 0;
                $sums[$source->code]['count'] += $datas[$period->format("Y-m-d")][$source->code]['count'];
            }
            $sales = Sale::where('sale_date', $period->format("Y-m-d"))->where('status', '9')->whereIn('pay_id', ['A', 'C'])->get();
            foreach ($sales as $sale) {
                if (!isset($datas[$period->format("Y-m-d")][$sale->type]['count'])) {
                    $datas[$period->format("Y-m-d")][$sale->type]['count'] = 0;
                }
                $datas[$period->format("Y-m-d")][$sale->type]['count']++;
            }
        }
        // dd($datas);
        foreach ($periods as $period) {
            foreach ($sources as $source) {
                $sums[$source->code]['count'] += $datas[$period->format("Y-m-d")][$source->code]['count'];
            }
        }
        // dd($datas);
        return view('rpg14.index')->with('datas', $datas)
            ->with('sums', $sums)
            ->with('sources', $sources)
            ->with('years', $years)
            ->with('request', $request)
            ->with('month', $month);
    }

    public function detail(Request $request, $date, $source_code)
    {
        $sources = SaleSource::where('status', 'up')->orderby('id')->get();
        foreach ($sources as $source) {
            $source_name[$source->code] = $source->name;
        }
        $datas = Sale::where('sale_date', $date)->where('status', '9')->whereIn('pay_id', ['A', 'C'])->where('type', $source_code)->get();
        return view('rpg14.detail')->with('datas', $datas)
            ->with('source_name', $source_name)
            ->with('date', $date)
            ->with('source_code', $source_code);
    }

    public function month_detail(Request $request, $month, $source_code)
    {
        $sources = SaleSource::where('status', 'up')->orderby('id')->get();
        foreach ($sources as $source) {
            $source_name[$source->code] = $source->name;
        }
        $firstDay = Carbon::createFromDate($month, 1)->firstOfMonth();
        $lastDay = Carbon::createFromDate($month, 1)->lastOfMonth();
        $datas = Sale::whereBetween('sale_data.sale_date', [$firstDay, $lastDay])
            ->join('sale_company_commission', 'sale_company_commission.sale_id', '=', 'sale_data.id')
            ->join('customer', 'customer.id', '=', 'sale_company_commission.company_id')
            ->where('sale_data.status', '9')
            ->whereIn('sale_data.pay_id', ['A', 'C'])
            ->where('sale_data.type', $source_code)
            ->select(
                'sale_company_commission.company_id',
                'customer.name',
                DB::raw('count(*) as total')
            )
            ->groupBy('sale_company_commission.company_id', 'customer.name')
            ->orderByDesc('total')
            ->get();

        return view('rpg14.month_detail')->with('datas', $datas)
            ->with('source_name', $source_name)
            ->with('month', $month)
            ->with('source_code', $source_code);
    }
}

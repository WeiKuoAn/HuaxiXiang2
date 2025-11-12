<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Prom;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Sale_prom;

class Rpg35Controller extends Controller
{
    public function rpg35(Request $request)
    {
        $years = range(Carbon::now()->year, 2022);
        if (isset($request->year)) {
            $search_year = $request->year;
            $search_month = $request->month;
            $firstDay = Carbon::createFromDate($search_year , $search_month,1)->firstOfMonth();
            $lastDay = Carbon::createFromDate($search_year , $search_month,1)->lastOfMonth();
        } else {
            $firstDay = Carbon::now()->firstOfMonth();
            $lastDay = Carbon::now()->lastOfMonth();
        }

        $proms = Prom::whereIn('id',[2,30])->get();
        $sale_proms = Sale_prom::join('sale_data','sale_data.id','=','sale_prom.sale_id')
        ->whereIn('sale_prom.prom_id',[2,30])->where('sale_data.sale_date','>=',$firstDay)->where('sale_data.sale_date','<=',$lastDay)
        ->select('sale_prom.*', 'sale_data.sale_date', 'sale_data.status', 'sale_data.id')
        ->where('sale_data.status','9')
        ->get();

        $datas = [];
        $total = 0;
        // 初始化資料結構
        foreach ($sale_proms as $sale_prom) {
            $promId = $sale_prom->prom_id;
            $promTotal = $sale_prom->prom_total;

            if (!isset($datas[$promId])) {
                $datas[$promId] = [
                    'name' => optional($proms->where('id', $promId)->first())->name,
                    'total' => 0,
                    'items' => [],
                ];
            }

            $datas[$promId]['items'][$promTotal] = ($datas[$promId]['items'][$promTotal] ?? 0) + 1;
            $datas[$promId]['total'] = array_sum($datas[$promId]['items']);
        }
        return view('rpg35.index')->with('years', $years)->with('request',$request)->with('datas',$datas)->with('total',$total);
    }
}

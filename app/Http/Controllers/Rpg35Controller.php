<?php

namespace App\Http\Controllers;

use App\Models\Prom;
use App\Models\Sale_prom;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Rpg35Controller extends Controller
{
    public function rpg35(Request $request)
    {
        $years = range(Carbon::now()->year, 2022);
        if (isset($request->year)) {
            $search_year = $request->year;
            $search_month = $request->month;
            $firstDay = Carbon::createFromDate($search_year, $search_month, 1)->firstOfMonth();
            $lastDay = Carbon::createFromDate($search_year, $search_month, 1)->lastOfMonth();
        } else {
            $firstDay = Carbon::now()->firstOfMonth();
            $lastDay = Carbon::now()->lastOfMonth();
        }

        $proms = Prom::whereIn('id', [2, 30])->get();
        $sale_proms = Sale_prom::join('sale_data', 'sale_data.id', '=', 'sale_prom.sale_id')
            ->leftJoin('customer', 'customer.id', '=', 'sale_data.customer_id')
            ->whereIn('sale_prom.prom_id', [2, 30])
            ->where('sale_data.sale_date', '>=', $firstDay)
            ->where('sale_data.sale_date', '<=', $lastDay)
            ->select(
                'sale_prom.*',
                'sale_data.sale_date',
                'sale_data.status',
                'sale_data.id as sale_data_id',
                'sale_data.pay_id',
                'sale_data.customer_id',
                'sale_data.kg',
                'sale_data.variety',
                'sale_data.pet_name',
                'customer.name as customer_name'
            )
            ->where('sale_data.status', '9')
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
                    'details' => [],
                ];
            }

            $datas[$promId]['items'][$promTotal] = ($datas[$promId]['items'][$promTotal] ?? 0) + 1;
            if (!isset($datas[$promId]['details'][$promTotal])) {
                $datas[$promId]['details'][$promTotal] = [];
            }

            $kg = $sale_prom->kg;
            $variety = $sale_prom->variety;

            if ((is_null($kg) || $kg === '') && $sale_prom->pay_id === 'D') {
                $depositRecord = Sale_prom::join('sale_data', 'sale_data.id', '=', 'sale_prom.sale_id')
                    ->leftJoin('customer', 'customer.id', '=', 'sale_data.customer_id')
                    ->where('sale_data.customer_id', $sale_prom->customer_id)
                    ->where('sale_data.pet_name', $sale_prom->pet_name)
                    ->where('sale_data.pay_id', 'C')
                    ->where('sale_data.status', '9')
                    ->orderBy('sale_data.sale_date', 'desc')
                    ->select(
                        'sale_data.kg',
                        'sale_data.variety'
                    )
                    ->first();

                if ($depositRecord) {
                    $kg = $depositRecord->kg ?? $kg;
                    $variety = $depositRecord->variety ?? $variety;
                }
            }

            $datas[$promId]['details'][$promTotal][] = [
                'sale_date' => $sale_prom->sale_date,
                'kg' => $kg,
                'variety' => $variety,
                'pet_name' => $sale_prom->pet_name,
                'customer_name' => $sale_prom->customer_name,
            ];
            $datas[$promId]['total']++;
        }
        return view('rpg35.index')->with('years', $years)->with('request', $request)->with('datas', $datas)->with('total', $total);
    }
}

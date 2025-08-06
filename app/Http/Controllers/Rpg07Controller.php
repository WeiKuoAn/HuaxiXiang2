<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\Customer;
use App\Models\Gdpaper;
use App\Models\Plan;
use App\Models\PromB;
use App\Models\PromA;
use App\Models\Sale_gdpaper;
use App\Models\Sale_promB;
use App\Models\Sale;
use App\Models\User;
use App\Models\SaleSource;
use Illuminate\Support\Facades\Auth;
use App\Exports\Rpg07Export;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Merit;


class Rpg07Controller extends Controller
{
    public function rpg07(Request $request)
    {
        if ($request->input() != null) {
            // 獲取銷售資料
            $sales = Sale::whereIn('plan_id',[2,3])->whereIn('pay_id',['A','C','E'])->whereIn('status',[1,3,9]);
            $after_date = $request->after_date;
            if ($after_date) {
                $sales = $sales->where('sale_date', '>=', $after_date);
            }
            $before_date = $request->before_date;
            if ($before_date) {
                $sales = $sales->where('sale_date', '<=', $before_date);
            }
            $sales = $sales->get();

            // 獲取功德資料
            $merits = Merit::with('user_data');
            if ($after_date) {
                $merits = $merits->where('date', '>=', $after_date);
            }
            if ($before_date) {
                $merits = $merits->where('date', '<=', $before_date);
            }
            $merits = $merits->get();

            // 檢查功德資料
            if ($merits->count() > 0) {
                \Log::info('功德資料檢查:', [
                    'total_merits' => $merits->count(),
                    'date_range' => [
                        'min_date' => $merits->min('date'),
                        'max_date' => $merits->max('date')
                    ],
                    'sample_dates' => $merits->take(5)->pluck('date')->toArray()
                ]);
            }

            // 按日期分組功德資料
            $merits_by_date = $merits->groupBy('date');

            // 建立合併陣列，將 sales 和 merits 放在同一層
            $datas = [];
            $total_price = 0;
            $kgs = 0;
            $total_merits = $merits->count();

            // 處理銷售資料
            foreach ($sales as $sale) {
                $datas[] = [
                    'type' => 'sale',
                    'date' => $sale->sale_date,
                    'data' => $sale,
                    'merits' => $merits_by_date->get($sale->sale_date, collect([]))
                ];
                $total_price += $sale->plan_price;
                $kgs += floatval($sale->kg) ?? 0;
            }

            // 處理功德資料（所有功德記錄都要顯示）
            foreach ($merits as $merit) {
                $datas[] = [
                    'type' => 'merit_only',
                    'date' => $merit->date,
                    'data' => null,
                    'merits' => collect([$merit])
                ];
                // 加上功德記錄的公斤數
                $kgs += floatval($merit->kg) ?? 0;
            }

            // 檢查最終資料
            $merit_only_count = collect($datas)->where('type', 'merit_only')->count();
            \Log::info('資料處理結果:', [
                'total_datas' => count($datas),
                'sales_count' => collect($datas)->where('type', 'sale')->count(),
                'merit_only_count' => $merit_only_count,
                'merit_dates' => collect($datas)->where('type', 'merit_only')->pluck('date')->toArray()
            ]);

            // 按日期排序
            $datas = collect($datas)->sortBy('date')->values();
        } else {
            $datas = [];
            $total_price = 0;
            $kgs = 0;
            $total_merits = 0;
        }

        return view('rpg07.index')->with('datas', $datas)
                                  ->with('total_price',$total_price)
                                  ->with('kgs',$kgs)
                                  ->with('total_merits',$total_merits)
                                  ->with('request', $request);
    }

    public function export(Request $request)
    {
        if ($request->input() != null) {
            // 獲取銷售資料
            $sales = Sale::whereIn('plan_id',[2,3])->whereIn('pay_id',['A','C','E'])->whereIn('status',[1,3,9]);
            $after_date = $request->after_date;
            if ($after_date) {
                $sales = $sales->where('sale_date', '>=', $after_date);
            }
            $before_date = $request->before_date;
            if ($before_date) {
                $sales = $sales->where('sale_date', '<=', $before_date);
            }
            $sales = $sales->get();

            // 獲取功德資料
            $merits = Merit::with('user_data');
            if ($after_date) {
                $merits = $merits->where('date', '>=', $after_date);
            }
            if ($before_date) {
                $merits = $merits->where('date', '<=', $before_date);
            }
            $merits = $merits->get();

            // 按日期分組功德資料
            $merits_by_date = $merits->groupBy('date');

            // 建立合併陣列，將 sales 和 merits 放在同一層
            $datas = [];

            // 處理銷售資料
            foreach ($sales as $sale) {
                $datas[] = [
                    'type' => 'sale',
                    'date' => $sale->sale_date,
                    'data' => $sale,
                    'merits' => $merits_by_date->get($sale->sale_date, collect([]))
                ];
            }

            // 處理功德資料（所有功德記錄都要顯示）
            foreach ($merits as $merit) {
                $datas[] = [
                    'type' => 'merit_only',
                    'date' => $merit->date,
                    'data' => null,
                    'merits' => collect([$merit])
                ];
            }

            // 按日期排序
            $datas = collect($datas)->sortBy('date')->values();
        } else {
            $after_date = '';
            $before_date = '';
            $datas = [];
        }

        $fileName = '團體火化' . date("Y-m-d") . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        $header = array('日期', $after_date.'~' ,  $before_date);
        $columns = array('No', '日期', '客戶', '寶貝名', '公斤數' , '品種' , '火化費' , '方案','金紙','備註');

        $callback = function() use($datas, $columns ,$header) {
            
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF).chr(0xBB).chr(0xBF), 3); 
            fputcsv($file, $header);
            fputcsv($file, $columns);

            $row_no = 1;
            foreach ($datas as $item) {
                if ($item['type'] === 'sale') {
                    $data = $item['data'];
                    $row['No'] = $row_no++;
                    $row['日期'] = $data->sale_date;
                    $row['客戶'] = $data->cust_name->name ?? '';
                    $row['寶貝名'] = $data->pet_name ?? '';
                    $row['品種'] = $data->variety ?? '';
                    $row['公斤數'] = $data->kg ?? 0;
                    if($data->pay_id == 'E'){
                        $row['火化費'] = number_format($data->pay_price);
                    }else{
                        $row['火化費'] = number_format($data->plan_price);
                    }
                    $row['方案'] = $data->plan_name->name ?? '';
                    $row['金紙'] = '';
                    foreach ($data->gdpapers as $gdpaper){
                        if(isset($gdpaper->gdpaper_id))
                        {
                            $row['金紙'] .= ($row['金紙']=='' ? '' : "\r\n").$gdpaper->gdpaper_name->name.' '.$gdpaper->gdpaper_num.'份';
                        }else{
                            $row['金紙'] = '無';
                        }
                    }
                    $row['備註'] = $data->comm ?? '';
                    
                    
                    fputcsv($file, array($row['No'], $row['日期'], $row['客戶'], $row['寶貝名'], $row['公斤數'],$row['品種'], $row['火化費'], $row['方案'],$row['金紙'],$row['備註']));
                } elseif ($item['type'] === 'merit_only') {
                    $merit = $item['merits']->first();
                    $row['No'] = $row_no++;
                    $row['日期'] = $item['date'];
                    $row['客戶'] = $merit->user_data->name ?? '功德記錄';
                    $row['寶貝名'] = '-';
                    $row['品種'] = $merit->variety ?? '功德';
                    $row['公斤數'] = $merit->kg ?? 0;
                    $row['火化費'] = '-';
                    $row['方案'] = '功德';
                    $row['金紙'] = '-';
                    $row['備註'] = '功德件';
                    
                    fputcsv($file, array($row['No'], $row['日期'], $row['客戶'], $row['寶貝名'], $row['公斤數'],$row['品種'], $row['火化費'], $row['方案'],$row['金紙'],$row['備註']));
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function export_test( Request $request)
    {
        $datas = [];
        $datas = Sale::whereIn('plan_id',[2,3])->whereIn('pay_id',['A','C','E'])->whereIn('status',[3,9]);
        $after_date = $request->after_date;
        if ($after_date) {
            $datas = $datas->where('sale_date', '>=', $after_date);
        }
        $before_date = $request->before_date;
        if ($before_date) {
            $datas = $datas->where('sale_date', '<=', $before_date);
        }
        $datas = $datas->get();
        // dd($after_date);
        return Excel::download(new Rpg07Export($datas,$after_date,$before_date,$request), '團體火化.xlsx');
        
    }
}

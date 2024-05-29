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


class Rpg07Controller extends Controller
{
    public function rpg07(Request $request)
    {
        if ($request->input() != null) {
            $datas = Sale::whereIn('plan_id',[2,3])->where('status',9);
            $after_date = $request->after_date;
            if ($after_date) {
                $datas = $datas->where('sale_date', '>=', $after_date);
            }
            $before_date = $request->before_date;
            if ($before_date) {
                $datas = $datas->where('sale_date', '<=', $before_date);
            }
            $datas = $datas->get();
        }else{
            $datas = [];
        }
        // dd($datas);
        // dd($datas);
        return view('rpg07.index')->with('datas', $datas)
                                  ->with('request', $request);
    }

    public function export(Request $request)
    {
        if ($request->input() != null) {
            $datas = Sale::whereIn('plan_id',[2,3])->where('status',9);
            $after_date = $request->after_date;
            if ($after_date) {
                $datas = $datas->where('sale_date', '>=', $after_date);
            }
            $before_date = $request->before_date;
            if ($before_date) {
                $datas = $datas->where('sale_date', '<=', $before_date);
            }
            $datas = $datas->get();
        }else{
            $after_date='';
            $before_date ='';
            $datas = [];
        }

        foreach($datas as $key => $data)
        {
            $data->comment = '';
            foreach($data->gdpapers as $gd_key => $gdpaper)
            {
                if (isset($gdpaper->gdpaper_id)){
                    
                    $data->comment .= $gdpaper->gdpaper_name->name.$gdpaper->gdpaper_num.'份'."\r\n";
                }else{
                    $data->comment .= '無';
                }
            }
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
        $columns = array('No', '日期', '客戶', '寶貝名', '公斤數' , '火化費' , '方案','金紙','備註','評論（送資材袋）','完成準備');

        $callback = function() use($datas, $columns ,$header) {
            
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF).chr(0xBB).chr(0xBF), 3); 
            fputcsv($file, $header);
            fputcsv($file, $columns);

            foreach ($datas as $key=>$data) {
                $row['No']  = $key+1;
                $row['日期'] = $data->sale_date;
                $row['客戶'] = $data->cust_name->name;
                $row['寶貝名'] = $data->pet_name;
                $row['公斤數'] = $data->kg;
                $row['火化費'] = $data->plan_price;
                $row['方案'] = $data->plan_name->name;
                $row['金紙'] = '';
                foreach ($data->gdpapers as $gdpaper){
                    if(isset($gdpaper->gdpaper_id))
                    {
                        $row['金紙'] .= ($row['金紙']=='' ? '' : "\r\n").$gdpaper->gdpaper_name->name.' '.$gdpaper->gdpaper_num.'份';
                    }else{
                        $row['金紙'] = '無';
                    }
                }
                $row['備註'] = $data->comm;
                fputcsv($file, array($row['No'], $row['日期'], $row['客戶'], $row['寶貝名'], $row['公斤數'], $row['火化費'], $row['方案'],$row['金紙'],$row['備註']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function export_test( Request $request)
    {
        $datas = [];
        $datas = Sale::whereIn('plan_id',[2,3])->where('status',9);
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

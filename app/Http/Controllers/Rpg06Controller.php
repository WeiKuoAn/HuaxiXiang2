<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\Prom;
use App\Models\Sale_prom;
use App\Models\Sale;
use App\Models\Customer;

class Rpg06Controller extends Controller
{
    public function rpg06(Request $request)
    {

        if($request){
            $datas = Sale_prom::where('prom_type','B')->where('prom_id',8);
            $after_date = $request->after_date;
            if($after_date){
                $after_date = $after_date.' 00:00:00';
                $datas = $datas->where('created_at','>=',$after_date);
            }
            $before_date = $request->before_date;
            if($before_date){
                $before_date = $before_date.' 23:59:59';
                $datas = $datas->where('created_at','<=',$before_date);
            }
            $cust_name = $request->cust_name;
            if($cust_name){
                $cust_name = $cust_name.'%';
                $cust_datas = Customer::where('name','like',$cust_name)->get();
                foreach($cust_datas as $cust_data){
                    $cust_ids[] = $cust_data->id; 
                }
                $sale_datas = Sale::whereIn('customer_id',$cust_ids)->get();
                foreach($sale_datas as $sale_data){
                    $sale_ids[] = $sale_data->id; 
                }
                $datas = $datas->whereIn('sale_id',$sale_ids);
            }
            $cust_mobile = $request->cust_mobile;
            if($cust_mobile){
                $cust_mobile = $cust_mobile.'%';
                $cust_datas = Customer::where('mobile','like',$cust_mobile)->get();
                foreach($cust_datas as $cust_data){
                    $cust_ids[] = $cust_data->id; 
                }
                $sale_datas = Sale::whereIn('customer_id',$cust_ids)->get();
                foreach($sale_datas as $sale_data){
                    $sale_ids[] = $sale_data->id; 
                }
                $datas = $datas->whereIn('sale_id',$sale_ids);
            }
            $datas = $datas->orderby('created_at', 'desc')->paginate(50);
            $condition = $request->all();
        }else{
            $datas = Sale_prom::where('prom_type','B')->where('prom_id',8)->orderby('created_at', 'desc')->paginate(50);
            $condition = '';
        }
        
        return view('rpg06.index')->with('datas',$datas)->with('request',$request)->with('condition',$condition);
    }

    public function export(Request $request)
    {
        if($request){
            $datas = Sale_prom::where('prom_type','B')->where('prom_id',8);
            $after_date = $request->after_date;
            if($after_date){
                $after_date = $after_date.' 00:00:00';
                $datas = $datas->where('created_at','>=',$after_date);
            }
            $before_date = $request->before_date;
            if($before_date){
                $before_date = $before_date.' 23:59:59';
                $datas = $datas->where('created_at','<=',$before_date);
            }
            $cust_name = $request->cust_name;
            if($cust_name){
                $cust_name = $cust_name.'%';
                $cust_datas = Customer::where('name','like',$cust_name)->get();
                foreach($cust_datas as $cust_data){
                    $cust_ids[] = $cust_data->id; 
                }
                $sale_datas = Sale::whereIn('customer_id',$cust_ids)->get();
                foreach($sale_datas as $sale_data){
                    $sale_ids[] = $sale_data->id; 
                }
                $datas = $datas->whereIn('sale_id',$sale_ids);
            }
            $cust_mobile = $request->cust_mobile;
            if($cust_mobile){
                $cust_mobile = $cust_mobile.'%';
                $cust_datas = Customer::where('mobile','like',$cust_mobile)->get();
                foreach($cust_datas as $cust_data){
                    $cust_ids[] = $cust_data->id; 
                }
                $sale_datas = Sale::whereIn('customer_id',$cust_ids)->get();
                foreach($sale_datas as $sale_data){
                    $sale_ids[] = $sale_data->id; 
                }
                $datas = $datas->whereIn('sale_id',$sale_ids);
            }
            $datas = $datas->orderby('created_at', 'desc')->get();
        }else{
            $datas = Sale_prom::where('prom_type','B')->where('prom_id',8)->orderby('created_at', 'desc')->get();
        }

        $fileName = '套組法會資料匯出' . date("Y-m-d") . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        $columns = array('編號','報名日期', '客戶姓名', '客戶電話', '寶貝名稱' , '客戶地址' ,'法會費用');

        $callback = function() use($datas, $columns) {
            
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF).chr(0xBB).chr(0xBF), 3); 
            fputcsv($file, $columns);

            foreach ($datas as $key=>$data) {
                $row['編號'] = $key+1;
                $row['報名日期'] = date('Y-m-d',strtotime($data->created_at));
                $row['客戶姓名'] = $data->sale_data->cust_name->name;
                $row['客戶電話'] = $data->sale_data->cust_name->mobile;
                $row['寶貝名稱'] = $data->sale_data->pet_name;
                $row['客戶地址'] = $data->sale_data->cust_name->county.$data->sale_data->cust_name->district.$data->sale_data->cust_name->address;
                $row['法會費用'] = number_format($data->prom_total);
                fputcsv($file, array($row['編號'],$row['報名日期'],$row['客戶姓名']
                                    ,$row['客戶電話'],$row['寶貝名稱'], $row['客戶地址'], $row['法會費用']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

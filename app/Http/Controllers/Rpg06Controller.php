<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\Sale_promB;
use App\Models\Sale;
use App\Models\Customer;

class Rpg06Controller extends Controller
{
    public function rpg06(Request $request)
    {

        if($request){
            $datas = Sale_promB::where('after_prom_id',8);
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
            $datas = Sale_promB::where('after_prom_id',8)->orderby('created_at', 'desc')->paginate(50);
            $condition = '';
        }
        
        return view('rpg06.index')->with('datas',$datas)->with('request',$request)->with('condition',$condition);
    }
}

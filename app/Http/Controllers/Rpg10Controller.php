<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;


class Rpg10Controller extends Controller
{
    public function rpg10(Request $request)
    {
        $years = range(Carbon::now()->year, 2022);
        // dd($request);
        if (isset($request->year)) {
            $search_year = $request->year;
            $search_month = $request->month;
            $firstDay = Carbon::createFromDate($search_year , $search_month,1)->firstOfMonth();
            $lastDay = Carbon::createFromDate($search_year , $search_month,1)->lastOfMonth();
        } else {
            $firstDay = Carbon::now()->firstOfMonth();
            $lastDay = Carbon::now()->lastOfMonth();;
        }
        //取得專員資料，並取得老闆和專員的job_id
        $users = User::where('status', '0')->whereIn('job_id',[1,3,5])->get();
        $datas = [];
        $sums = [];
        $sale_datas = DB::table('sale_data')
                    ->join('sale_gdpaper','sale_gdpaper.sale_id', '=' , 'sale_data.id')
                    ->leftJoin('users','users.id', '=' , 'sale_data.user_id')
                    ->leftJoin('product','product.id', '=' , 'sale_gdpaper.gdpaper_id')
                    ->leftJoin('plan','plan.id', '=' , 'sale_data.plan_id')
                    ->where('sale_data.type_list','dispatch')
                    ->where('sale_data.status','9')
                    ->where('product.commission', '0')
                    ->where('users.status', '0')
                    ->whereIn('users.job_id',[1,3,5])
                    ->whereNotNull('sale_gdpaper.gdpaper_id')
                    // ->whereNotNull('sale_data.plan_id')
                    ->where('sale_data.sale_date','>=',$firstDay)
                    ->where('sale_data.sale_date','<=',$lastDay);

        $user_id = $request->user_id;
        if ($user_id != "NULL") {
            if (isset($user_id)) {
                $sale_datas = $sale_datas->where('sale_data.user_id', $user_id);
            } else {
                $sale_datas = $sale_datas;
            }
        }

        $sale_datas = $sale_datas->orderby('users.id','asc')
                                 ->orderby('sale_data.sale_date','desc')
                                 ->orderby('sale_data.plan_id','asc')
                                 ->select('sale_data.*','sale_gdpaper.*','users.name'
                                         ,'users.id as user_id','product.name as product_name'
                                         ,'plan.name as plan_name')
                                 ->get();
                    // dd($sale_datas);
        
        foreach($sale_datas as $sale_data)
        {
            $datas[$sale_data->name]['sale_datas'] = DB::table('sale_data')
                                                    ->join('sale_gdpaper','sale_gdpaper.sale_id', '=' , 'sale_data.id')
                                                    ->leftJoin('product','product.id', '=' , 'sale_gdpaper.gdpaper_id')
                                                    ->leftJoin('plan','plan.id', '=' , 'sale_data.plan_id')
                                                    ->where('sale_data.type_list','dispatch')
                                                    ->where('product.commission', '0')
                                                    ->where('sale_data.status','9')
                                                    ->whereNotNull('sale_gdpaper.gdpaper_id')
                                                    ->where('sale_data.sale_date','>=',$firstDay)
                                                    ->where('sale_data.sale_date','<=',$lastDay)
                                                    ->where('sale_data.user_id',$sale_data->user_id)
                                                    ->orderby('sale_data.sale_date','desc')
                                                    ->orderby('sale_data.plan_id','asc')
                                                    ->select('sale_data.*','sale_gdpaper.*','plan.name as plan_name','product.name')
                                                    ->get();
        }

        foreach($datas as $user_id=>$data)
        {
            foreach($data['sale_datas'] as $sale_data)
            {
                if($sale_data->plan_id == 3){
                    if($sale_data->gdpaper_total <= 100){
                        $sale_data->comm_price = $sale_data->gdpaper_total * 0;
                    }else{
                        $sale_data->comm_price = ($sale_data->gdpaper_total - 100) * 0.3;
                    }
                }else{
                    $sale_data->comm_price = $sale_data->gdpaper_total * 0.3;
                }
                
            }
        }
        // dd($datas);
        
        $sums['total_num'] = 0;
        $sums['total_price'] = 0;
        $sums['total_comm_price'] = 0;
        foreach($datas as $user_id=>$data)
        {
            foreach($data['sale_datas'] as $sale_data)
            {
                $datas[$user_id]['total_num'] = 0;
                $datas[$user_id]['total_price'] = 0;
                $datas[$user_id]['total_comm_price'] = 0;
            }
        }

        foreach($datas as $user_id=>$data)
        {
            foreach($data['sale_datas'] as $sale_data)
            {
                $datas[$user_id]['total_num'] += $sale_data->gdpaper_num;
                $datas[$user_id]['total_price'] += $sale_data->gdpaper_total;
                $datas[$user_id]['total_comm_price'] += $sale_data->comm_price;
            }
        }

        foreach($datas as $user_id=>$data)
        {
            $sums['total_num'] += $datas[$user_id]['total_num'];
            $sums['total_price'] += $datas[$user_id]['total_price'];
            $sums['total_comm_price'] += $datas[$user_id]['total_comm_price'];
        }




        

        // dd($sums);  


        return view('rpg10.index')->with('users', $users)->with('years', $years)->with('request',$request)->with('datas',$datas)->with('sums',$sums);
    }
}

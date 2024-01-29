<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Pay;
use App\Models\PayData;
use App\Models\PayItem;

class Rpg20Controller extends Controller
{
    public function Rpg20(Request $request){

        $years = range(Carbon::now()->year, 2022);
        $currentMonth = Carbon::now()->month;
        $pastMonth = Carbon::now()->subMonth()->month;
        $nowYear = Carbon::now()->year;

        //取得當月第一天和最後一天（目前）
        $current_year = $request->current_year;
        $current_month = $request->current_month;
        $pay_id = $request->pay_id;

        if(!isset($current_month)){
            $currentMonthStart = Carbon::now()->firstOfMonth();
            $currentMonthEnd = Carbon::now()->endOfMonth();
        }else{
            $currentMonthStart = Carbon::createFromDate($current_year , $current_month)->firstOfMonth();
            $currentMonthEnd = Carbon::createFromDate($current_year , $current_month)->endOfMonth();
        }

        $current_datas = [];
        $current_sums = [];
        $current_sums['total_amount'] = 0;
        //前一個月
        $current_pay_datas = PayData::where('status','1')->where('pay_date','>=',$currentMonthStart)->where('pay_date','<=',$currentMonthEnd)->where('created_at','<=','2023-01-08 14:22:21');
        $current_pay_items = PayItem::where('status','1')->where('pay_date','>=',$currentMonthStart)->where('pay_date','<=',$currentMonthEnd);
        if ($pay_id != "NULL") {
            if (isset($pay_id)) {
                $current_pay_datas = $current_pay_datas->where('pay_id', $pay_id);
                $current_pay_items = $current_pay_items->where('pay_id', $pay_id);
            } else {
                $current_pay_datas = $current_pay_datas;
                $current_pay_items = $current_pay_items;
            }
        }
        $current_pay_datas = $current_pay_datas->get();
        $current_pay_items = $current_pay_items->get();

        foreach($current_pay_datas as $current_pay_data){
            if(isset($current_pay_data->pay_name)){
                $current_datas[$current_pay_data->pay_id]['pay_name'] = $current_pay_data->pay_name->name;
            }else{
                $current_datas[$current_pay_data->pay_id]['pay_name'] = $current_pay_data->pay_id;
            }
            $current_datas[$current_pay_data->pay_id]['price'][] =  $current_pay_data->price;
            $current_datas[$current_pay_data->pay_id]['comment'] = $current_pay_data->comment;
        }
        foreach($current_pay_datas as $current_pay_data){
            $current_datas[$current_pay_data->pay_id]['total_price'] =  array_sum($current_datas[$current_pay_data->pay_id]['price']);
        }

        foreach($current_pay_items as $pay_item){
            if($pay_item->pay_id == null)
            {
                $get_current_pay_data = PayData::where('id',$pay_item->pay_data_id)->first();
                $pay_item->pay_id = $get_current_pay_data->pay_id;
            }
            // dd($pay_item->pay_id);
            if(isset($pay_item->pay_name)){
                $current_datas[$pay_item->pay_id]['pay_name'] = $pay_item->pay_name->name;
            }else{
                $current_datas[$pay_item->pay_id]['pay_name'] = $pay_item->pay_id;
            }
            $current_datas[$pay_item->pay_id]['price'][] =  $pay_item->price;
            $current_datas[$pay_item->pay_id]['comment'] = $pay_item->comment;
        }
        foreach($current_pay_items as $pay_item){
            $current_datas[$pay_item->pay_id]['total_price'] =  array_sum($current_datas[$pay_item->pay_id]['price']);
        }

        foreach($current_datas as $data){
            $current_sums['total_amount'] += $data['total_price'];
            $current_sums['percent'] = round($current_sums['total_amount']*100/$current_sums['total_amount'],2);
        }

        foreach($current_pay_datas as $current_pay_data){
            $current_datas[$current_pay_data->pay_id]['percent'] = round($current_datas[$current_pay_data->pay_id]['total_price']*100/$current_sums['total_amount'],2);
        }

        foreach($current_pay_items as $pay_item){
            $current_datas[$pay_item->pay_id]['percent'] = round($current_datas[$pay_item->pay_id]['total_price']*100/$current_sums['total_amount'],2);
        }


        //取得上個月第一天和最後一天（過去）
        $past_year = $request->past_year;
        $past_month = $request->past_month;
        if(!isset($past_month)){
            $pastMonthStart = Carbon::now()->subMonth()->firstOfMonth();
            $pastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        }else{
            $pastMonthStart = Carbon::createFromDate($past_year , $past_month)->firstOfMonth();
            $pastMonthEnd = Carbon::createFromDate($past_year , $past_month)->endOfMonth();
        }
        
        //前一個月
        $past_pay_datas = PayData::where('status','1')->where('pay_date','>=',$pastMonthStart)->where('pay_date','<=',$pastMonthEnd)->where('created_at','<=','2023-01-08 14:22:21');
        $past_pay_items = PayItem::where('status','1')->where('pay_date','>=',$pastMonthStart)->where('pay_date','<=',$pastMonthEnd);
        if ($pay_id != "NULL") {
            if (isset($pay_id)) {
                $past_pay_datas = $past_pay_datas->where('pay_id', $pay_id);
                $past_pay_items = $past_pay_items->where('pay_id', $pay_id);
            } else {
                $past_pay_datas = $past_pay_datas;
                $past_pay_items = $past_pay_items;
            }
        }
        $past_pay_datas = $past_pay_datas->get();
        $past_pay_items = $past_pay_items->get();

        $past_datas = [];
        $past_sums = [];
        $past_sums['total_amount'] = 0;
        foreach($past_pay_datas as $past_pay_data){
            if(isset($past_pay_data->pay_name)){
                $past_datas[$past_pay_data->pay_id]['pay_name'] = $past_pay_data->pay_name->name;
            }else{
                $past_datas[$past_pay_data->pay_id]['pay_name'] = $past_pay_data->pay_id;
            }
            $past_datas[$past_pay_data->pay_id]['price'][] =  $past_pay_data->price;
            $past_datas[$past_pay_data->pay_id]['comment'] = $past_pay_data->comment;
        }
        foreach($past_pay_datas as $past_pay_data){
            $past_datas[$past_pay_data->pay_id]['total_price'] =  array_sum($past_datas[$past_pay_data->pay_id]['price']);
        }

        foreach($past_pay_items as $pay_item){
            if($pay_item->pay_id == null)
            {
                $get_past_pay_data = PayData::where('id',$pay_item->pay_data_id)->first();
                $pay_item->pay_id = $get_past_pay_data->pay_id;
            }
            // dd($pay_item->pay_id);
            if(isset($pay_item->pay_name)){
                $past_datas[$pay_item->pay_id]['pay_name'] = $pay_item->pay_name->name;
            }else{
                $past_datas[$pay_item->pay_id]['pay_name'] = $pay_item->pay_id;
            }
            $past_datas[$pay_item->pay_id]['price'][] =  $pay_item->price;
            $past_datas[$pay_item->pay_id]['comment'] = $pay_item->comment;
        }
        foreach($past_pay_items as $pay_item){
            $past_datas[$pay_item->pay_id]['total_price'] =  array_sum($past_datas[$pay_item->pay_id]['price']);
        }

        foreach($past_datas as $data){
            $past_sums['total_amount'] += $data['total_price'];
            $past_sums['percent'] = round($past_sums['total_amount']*100/$past_sums['total_amount'],2);
        }

        foreach($past_pay_datas as $past_pay_data){
            $past_datas[$past_pay_data->pay_id]['percent'] = round($past_datas[$past_pay_data->pay_id]['total_price']*100/$past_sums['total_amount'],2);
        }

        foreach($past_pay_items as $pay_item){
            $past_datas[$pay_item->pay_id]['percent'] = round($past_datas[$pay_item->pay_id]['total_price']*100/$past_sums['total_amount'],2);
        }

        //計算差異
        $differences = [];
        $key = 0;
        // 遍历 current_datas 来找出差异
        foreach ($current_datas as $pay_id => $current_data) {
            $current_total = $current_data['total_price'] ?? 0;
            $past_total = $past_datas[$pay_id]['total_price'] ?? 0;
            $key = $key+1;
            $difference = $current_total - $past_total;

            $differences[$pay_id] = [
                'key' => $key,
                'pay_name' => $current_data['pay_name'] ?? 'Unknown',
                'current_total' => $current_total,
                'past_total' => $past_total,
                'difference' => $difference
            ];
        }

        // 检查 past_datas 中是否有在 current_datas 中不存在的 pay_id
        foreach ($past_datas as $pay_id => $past_data) {
            if (!array_key_exists($pay_id, $current_datas)) {
                $past_total = $past_data['total_price'] ?? 0;
                $key = $key+1;
                $differences[$pay_id] = [
                    'key' => $key,
                    'pay_name' => $past_data['pay_name'] ?? 'Unknown',
                    'current_total' => 0,
                    'past_total' => $past_total,
                    'difference' => -1 * $past_total
                ];
            }
        }


        //兩月相差值
        $current_total_amount = $current_sums['total_amount'] ?? 0;
        $past_total_amount = $past_sums['total_amount'] ?? 0;

        // 计算两个月份的总金额差异
        $total_difference = $current_total_amount - $past_total_amount;

        // 可以选择以数组的形式存储这个差异
        $sums_difference = [
            'current_total_amount' => $current_total_amount,
            'past_total_amount' => $past_total_amount,
            'total_difference' => $total_difference
        ];
        
        // dd($differences);

        
        // dd($current_datas);

        $pays = Pay::where('status', 'up')->orderby('id')->get();

        $months = [
            '01'=> [ 'name'=>'一月'],
            '02'=> [ 'name'=>'二月'],
            '03'=> [ 'name'=>'三月'],
            '04'=> [ 'name'=>'四月'],
            '05'=> [ 'name'=>'五月'],
            '06'=> [ 'name'=>'六月'],
            '07'=> [ 'name'=>'七月'],
            '08'=> [ 'name'=>'八月'],
            '09'=> [ 'name'=>'九月'],
            '10'=> [ 'name'=>'十月'],
            '11'=> [ 'name'=>'十一月'],
            '12'=> [ 'name'=>'十二月'],
        ];
       

        return view('rpg20.index')->with('request', $request)
                            ->with('past_datas',$past_datas)
                            ->with('past_sums',$past_sums)
                            ->with('current_datas',$current_datas)
                            ->with('current_sums',$current_sums)
                            ->with('pays',$pays)
                            ->with('currentMonth',$currentMonth)
                            ->with('currentMonthStart',$currentMonthStart)
                            ->with('pastMonthStart',$pastMonthStart)
                            ->with('pastMonth',$pastMonth)
                            ->with('nowYear',$nowYear)
                            ->with('pastMonth',$pastMonth)
                            ->with('nowYear',$nowYear)
                            ->with('years', $years)
                            ->with('months',$months)
                            ->with('differences',$differences)
                            ->with('sums_difference',$sums_difference);
    }
}

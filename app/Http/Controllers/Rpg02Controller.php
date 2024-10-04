<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use App\Models\Pay;
use App\Models\PayData;
use App\Models\PayItem;

class Rpg02Controller extends Controller
{
    public function Rpg02(Request $request)
    {

        $first_date = Carbon::now()->firstOfMonth();
        $last_date = Carbon::now()->lastOfMonth();

        $after_date = Carbon::now()->firstOfMonth()->format("Y-m-d");
        $before_date = Carbon::now()->lastOfMonth()->format("Y-m-d");

        $pay_datas = PayData::where('status', '1');
        $pay_items = PayItem::where('status', '1');


        if ($request->input() != null) {
            $after_date = $request->after_date;
            if ($after_date) {
                $pay_datas = $pay_datas->where('pay_date', '>=', $after_date);
                $pay_items = $pay_items->where('pay_date', '>=', $after_date);
            }
            $before_date = $request->before_date;
            if ($before_date) {
                $pay_datas = $pay_datas->where('pay_date', '<=', $before_date);
                $pay_items = $pay_items->where('pay_date', '<=', $before_date);
            }

            $pay_id = $request->pay_id;
            if ($pay_id != "NULL") {
                if (isset($pay_id)) {
                    $pay_datas = $pay_datas->where('pay_id', $pay_id);
                    $pay_items = $pay_items->where('pay_id', $pay_id);
                } else {
                    $pay_datas = $pay_datas;
                    $pay_items = $pay_items;
                }
            }
            $pay_datas = $pay_datas->where('created_at', '<=', '2023-01-08 14:22:21')->get(); //擷取至6/9號
            $pay_items = $pay_items->get(); //從至6/9號抓取
        } else {
            $pay_datas = $pay_datas->where('pay_date', '>=', $after_date)->where('pay_date', '<=', $before_date)->where('created_at', '<=', '2023-01-08 14:22:21')->get(); //擷取至6/9號
            $pay_items = $pay_items->where('pay_date', '>=', $after_date)->where('pay_date', '<=', $before_date)->get(); //從至6/9號抓取
        }

        // dd($after_date);

        // dd($pay_datas);
        $datas = [];
        $sums = [];
        $sums['total_amount'] = 0;
        foreach ($pay_datas as $pay_data) {
            if (isset($pay_data->pay_name)) {
                $datas[$pay_data->pay_id]['pay_name'] = $pay_data->pay_name->name;
            } else {
                $datas[$pay_data->pay_id]['pay_name'] = $pay_data->pay_id;
            }
            $datas[$pay_data->pay_id]['price'][] =  $pay_data->price;
            $datas[$pay_data->pay_id]['comment'] = $pay_data->comment;
            $datas[$pay_data->pay_id]['start_date'] = $after_date;
            $datas[$pay_data->pay_id]['end_start'] = $before_date;
        }
        foreach ($pay_datas as $pay_data) {
            $datas[$pay_data->pay_id]['total_price'] =  array_sum($datas[$pay_data->pay_id]['price']);
        }
        foreach ($pay_items as $pay_item) {
            if ($pay_item->pay_id == null) {
                $get_pay_data = PayData::where('id', $pay_item->pay_data_id)->first();
                $pay_item->pay_id = $get_pay_data->pay_id;
            }
            // dd($pay_item->pay_id);
            if (isset($pay_item->pay_name)) {
                $datas[$pay_item->pay_id]['pay_name'] = $pay_item->pay_name->name;
            } else {
                $datas[$pay_item->pay_id]['pay_name'] = $pay_item->pay_id;
            }
            $datas[$pay_item->pay_id]['price'][] =  $pay_item->price;
            $datas[$pay_item->pay_id]['comment'] = $pay_item->comment;
        }
        foreach ($pay_items as $pay_item) {
            $datas[$pay_item->pay_id]['total_price'] =  array_sum($datas[$pay_item->pay_id]['price']);
        }

        foreach ($datas as $data) {
            $sums['total_amount'] += $data['total_price'];
            $sums['percent'] = round($sums['total_amount'] * 100 / $sums['total_amount'], 2);
        }

        foreach ($pay_datas as $pay_data) {
            $datas[$pay_data->pay_id]['percent'] = round($datas[$pay_data->pay_id]['total_price'] * 100 / $sums['total_amount'], 2);
        }

        foreach ($pay_items as $pay_item) {
            $datas[$pay_item->pay_id]['percent'] = round($datas[$pay_item->pay_id]['total_price'] * 100 / $sums['total_amount'], 2);
        }

        $pays = Pay::where('status', 'up')->orderby('id')->get();


        return view('rpg02.index')->with('request', $request)
            ->with('first_date', $first_date)
            ->with('last_date', $last_date)
            ->with('datas', $datas)
            ->with('sums', $sums)
            ->with('pays', $pays)
            ->with('after_date', $after_date)
            ->with('before_date', $before_date);
    }

    public function detail(Request $request, $after_date, $before_date, $pay_id)
    {
        $pay_data = Pay::where('id', $pay_id)->first();
        $datas = PayItem::where('status', '1')->where('pay_date', '>=', $after_date)->where('pay_date', '<=', $before_date)->where('pay_id', $pay_id)->get();
        $total = 0;
        foreach ($datas as $data) {
            $total += $data->price;
        }
        return view('rpg02.detail')->with('after_date', $after_date)
            ->with('before_date', $before_date)
            ->with('datas', $datas)
            ->with('pay_data', $pay_data)
            ->with('total', $total);
    }
}

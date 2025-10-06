<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use App\Models\Puja;
use App\Models\PujaData;
use App\Models\PujaType;
use App\Models\PujaDataAttchProduct;

class Rpg18Controller extends Controller
{
    public function rpg18(Request $request)
    {
        $years = range(Carbon::now()->year,2022);
        $search_year = $request->year;

        if(!isset($search_year)){
            // $search_year = '2022';
            $search_year = Carbon::now()->year;
        }

        $puja_types = PujaType::where('status','up')->get();

        $datas = [];

        $pujas = Puja::where('date','like',$search_year.'%');
        
        $type = $request->type;

        if ($type != "null") {
            if (isset($type)) {
                $pujas = $pujas->where('type', $type);
            } else {
                $pujas = $pujas;
            }
        }
        $pujas = $pujas->orderby('id','desc')->get();

        foreach($pujas as $key=>$puja)
        {
            $datas[$puja->id]['name'] = $puja->name;
            $datas[$puja->id]['count'] = PujaData::where('puja_id',$puja->id)->whereIn('pay_id',['A','C','E'])->count();
            $datas[$puja->id]['gift_count'] = PujaData::where('puja_id',$puja->id)->where('type','1')->count();
            $datas[$puja->id]['suit_count'] = PujaData::where('puja_id',$puja->id)->where('type','2')->count();
            $datas[$puja->id]['puja_datas'] = PujaData::where('puja_id',$puja->id)->get();
            $datas[$puja->id]['monty_price'] = 0;
            // //應收金額
            // $datas[$puja->id]['should_price'] = 0;
            //實收金額
            $datas[$puja->id]['total_price'] = 0;
            $datas[$puja->id]['total_price_amount'] = 0;
        }

        foreach($pujas as $key=>$puja)
        {
            foreach($datas[$puja->id]['puja_datas'] as $puja_data)
            {
                if($puja_data->type == 0 || $puja_data->type == 2){//一般跟套組
                    $datas[$puja->id]['total_price'] += $puja_data->pay_price;
                }
                $datas[$puja->id]['total_price_amount'] = $datas[$puja->id]['total_price'] + $datas[$puja->id]['suit_count'] * $puja->price;
                foreach($puja_data->products as $product)
                {
                    $datas[$puja->id]['monty_price'] += $product->product_total;
                }
            }
            $datas[$puja->id]['apply_price'] = $datas[$puja->id]['total_price'] - $datas[$puja->id]['monty_price'];
            // $datas[$puja->id]['monty_price'] = $datas[$puja->id]['total_price'] - $datas[$puja->id]['apply_price'];
        }
        

        // dd($datas);

        return view('rpg18.index')->with('datas',$datas)
                                  ->with('puja_types',$puja_types)
                                    ->with('request',$request)
                                    ->with('search_year',$search_year)
                                    ->with('years',$years);
    }
}

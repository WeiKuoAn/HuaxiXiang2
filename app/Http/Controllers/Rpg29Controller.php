<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\ContractType;
use App\Models\Contract;

class Rpg29Controller extends Controller
{
    public function rpg29(Request $request)
    {
        $years = range(Carbon::now()->year,2022);
        $search_year = $request->year;

        if(!isset($search_year)){
            // $search_year = '2022';
            $search_year = Carbon::now()->year;
        }

        $contract_types = ContractType::where('status','up')->get();

        $datas = [];
        $sums = [];

        $contracts = Contract::where('start_date','like',$search_year.'%');
        
        $type = $request->type;

        if ($type != "null") {
            if (isset($type)) {
                $contracts = $contracts->where('type', $type);
            } else {
                $contracts = $contracts;
            }
        }
        $contracts = $contracts->orderby('id','desc')->get();

        foreach($contract_types as $key=>$contract_type)
        {
            $datas[$contract_type->id]['name'] = $contract_type->name;
            $datas[$contract_type->id]['count'] = contract::where('start_date','like',$search_year.'%')->where('type',$contract_type->id)->count();
            $datas[$contract_type->id]['close_count'] = contract::where('close_date','like',$search_year.'%')->where('type',$contract_type->id)->count();
            $datas[$contract_type->id]['new_count'] = contract::where('start_date','like',$search_year.'%')->where('type',$contract_type->id)->where('renew','0')->count();
            $datas[$contract_type->id]['renew_count'] = contract::where('start_date','like',$search_year.'%')->where('type',$contract_type->id)->where('renew','1')->count();
            $datas[$contract_type->id]['total_price'] = contract::where('start_date','like',$search_year.'%')->where('type',$contract_type->id)->sum('price');
        }
        
        $sums['total_price'] = 0;
        foreach($datas as $key=>$data)
        {
            $sums['red_count'] = $datas[1]['count'] + $datas[3]['count'];
            $sums['yellow_count'] = $datas[2]['count'] + $datas[4]['count'];
            $sums['total_price'] += $data['total_price'];
        }
        // dd($sums);

        return view('rpg29.index')->with('datas',$datas)
                                  ->with('contract_types',$contract_types)
                                    ->with('request',$request)
                                    ->with('search_year',$search_year)
                                    ->with('years',$years)
                                    ->with('sums',$sums);
    }
}

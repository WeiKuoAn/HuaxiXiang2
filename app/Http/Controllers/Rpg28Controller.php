<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Lamp;
use App\Models\LampType;

class Rpg28Controller extends Controller
{
    public function rpg28(Request $request)
    {
        $years = range(Carbon::now()->year,2022);
        $search_year = $request->year;

        if(!isset($search_year)){
            // $search_year = '2022';
            $search_year = Carbon::now()->year;
        }

        $lamp_types = LampType::where('status','up')->get();

        $datas = [];
        $sums = [];

        $lamps = Lamp::where('start_date','like',$search_year.'%');
        
        $type = $request->type;

        if ($type != "null") {
            if (isset($type)) {
                $lamps = $lamps->where('type', $type);
            } else {
                $lamps = $lamps;
            }
        }
        $lamps = $lamps->orderby('id','desc')->get();

        foreach($lamp_types as $key=>$lamp_type)
        {
            $datas[$lamp_type->id]['name'] = $lamp_type->name;
            $datas[$lamp_type->id]['count'] = Lamp::where('start_date','like',$search_year.'%')->where('type',$lamp_type->id)->count();
            $datas[$lamp_type->id]['total_price'] = Lamp::where('start_date','like',$search_year.'%')->where('type',$lamp_type->id)->sum('price');
        }
        
        $sums['total_price'] = 0;
        foreach($datas as $key=>$data)
        {
            $sums['red_count'] = $datas[1]['count'] + $datas[3]['count'];
            $sums['yellow_count'] = $datas[2]['count'] + $datas[4]['count'];
            $sums['total_price'] += $data['total_price'];
        }
        // dd($sums);

        return view('rpg28.index')->with('datas',$datas)
                                  ->with('lamp_types',$lamp_types)
                                    ->with('request',$request)
                                    ->with('search_year',$search_year)
                                    ->with('years',$years)
                                    ->with('sums',$sums);
    }
}

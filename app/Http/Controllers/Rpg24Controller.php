<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Carbon\Carbon;

class Rpg24Controller extends Controller
{
    public function rpg24(Request $request)
    {
        $first_date = Carbon::now()->firstOfMonth();
        $last_date = Carbon::now()->lastOfMonth();

        

        if($request->input() != null){
            $after_date = $request->after_date;
            $before_date = $request->before_date;
        }else{
            $after_date = Carbon::now()->firstOfMonth();
            $before_date = Carbon::now()->lastOfMonth();
        }
        // dd($after_date);

        $datas = [];
        $countys = [ '高雄市' , '屏東縣' , '臺南市'];
        
        foreach($countys as $county)
        {
            $customers = Customer::where('county',$county)->where('created_at','>=',$after_date)->where('created_at','<=',$before_date)->get();
            foreach($customers as $customer)
            {
                if(!isset($datas[$customer->county][$customer->district]['count'])){
                    $datas[$customer->county][$customer->district]['count']=1;
                }else{
                    $datas[$customer->county][$customer->district]['count']++;
                }
            }
        }

        foreach ($datas as $county => &$districts) {
            uasort($districts, function ($a, $b) {
                return $b['count'] - $a['count'];
            });
        }

        // dd($datas);
        return view('rpg24.index')->with('datas', $datas)->with('request',$request)->with('first_date',$first_date)->with('last_date',$last_date);
    }
}

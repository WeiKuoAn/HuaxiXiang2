<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveDay;
use App\Models\Job;
use App\Models\LeaveDayCheck;
use App\Models\Leaves;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use App\Models\Vacation;
use App\Models\UserHoliday;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
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
        if(isset($request->year))
        {
            $year = $request->year;
        }else{
            $year = Carbon::now()->year;//取得當年
        }
        $years = range(Carbon::now()->year,2022);
        $users = User::where('status','0')->whereIn('job_id',[3,4,5])->orderby('job_id')->get();
        $year_holiday = Vacation::where('year',$year)->sum('day');//取放假天數
        // dd($year_holiday);
        $datas = Leaves::where('status',0)->orderby('seq')->get();

        return view('personnel.leaves')->with('months',$months)->with('years',$years)->with('request',$request)->with('datas',$datas);
    }

    public function create(Request $request)
    {
        $year = Carbon::now()->year;//取得當年
        return view('personnel.leave_create')->with('year',$year);
    }

    public function store(Request $request)
    {
        $data = new Leaves;
        $data->name = $request->name;
        $data->seq = $request->seq;
        $data->status = $request->status;
        $data->fixed = $request->fixed;
        $data->comment = $request->comment;
        $data->save();
        return redirect()->route('personnel.leaves');
    }

    public function edit(Request $request , $id)
    {
        $year = Carbon::now()->year;//取得當年
        $data = Leaves::where('id',$id)->first();
        return view('personnel.leave_edit')->with('year',$year)->with('data',$data);
    }

    public function update(Request $request , $id)
    {
        $data = Leaves::where('id',$id)->first();
        $data->name = $request->name;
        $data->seq = $request->seq;
        $data->status = $request->status;
        $data->fixed = $request->fixed;
        $data->comment = $request->comment;
        $data->save();
        return redirect()->route('personnel.leaves');
    }
}

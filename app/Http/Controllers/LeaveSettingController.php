<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveDay;
use App\Models\Job;
use App\Models\LeaveDayCheck;
use App\Models\Leaves;
use App\Models\LeaveSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use App\Models\Vacation;
use App\Models\UserHoliday;

class LeaveSettingController extends Controller
{
    public function create()
    {
        $year = Carbon::now()->year;//取得當年
        $datas = Leaves::where('status',0)->get();
        return view('personnel.leave_sitting_create')->with('year',$year)->with('datas',$datas);
    }

    public function store(Request $request)
    {
        $data = new LeaveSetting;
        $data->leave_id = $request->leave_id;
        $data->year = $request->year;
        $data->approved_days = $request->approved_days;
        $data->unit = $request->unit;
        $data->save();
        return redirect()->route('personnel.leaves');
    }

    public function edit($id)
    {
        $year = Carbon::now()->year;//取得當年
        $leaves = Leaves::where('status',0)->get();
        $data = LeaveSetting::where('id',$id)->first();
        return view('personnel.leave_sitting_edit')->with('year',$year)->with('data',$data)->with('leaves',$leaves);
    }

    public function update(Request $request , $id)
    {
        $data = LeaveSetting::where('id',$id)->first();
        $data->leave_id = $request->leave_id;
        $data->year = $request->year;
        $data->approved_days = $request->approved_days;
        $data->unit = $request->unit;
        $data->save();
        return redirect()->route('personnel.leaves');
    }
}

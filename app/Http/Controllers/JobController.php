<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;

class JobController extends Controller
{
    /*科目新增 */
    public function index(){
        $datas = Job::with(['users', 'director_data'])->paginate(50);
        
        // 為每個職稱計算人數
        foreach($datas as $job) {
            $job->active_user_count = $job->users()->where('status', '0')->count();
            $job->total_user_count = $job->users()->count();
        }
        
        return view('job.jobs')->with('datas',$datas);
    }

    public function create(){
        $datas = Job::where('status','up')->get();
        return view('job.create')->with('datas',$datas);
    }

    public function store(Request $request){
        $data = new Job();
        $data->name = $request->name;
        $data->director_id = $request->director_id;
        $data->status = $request->status;
        $data->save();
        return redirect()->route('jobs');
    }

    public function show($id){
        $jobs = Job::where('status','up')->get();
        $data = Job::with(['users', 'director_data'])->where('id',$id)->first();
        return view('job.edit')->with('data',$data)->with('jobs',$jobs);
    }

    public function update($id, Request $request){
        $data = Job::where('id',$id)->first();
        $data->name = $request->name;
        $data->director_id = $request->director_id;
        $data->status = $request->status;
        $data->save();
        return redirect()->route('jobs');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SeniorityPauses;
use App\Models\User;

class SeniorityPausesController extends Controller
{
    public function index(Request $request, $user_id)
    {
        $datas = SeniorityPauses::where('user_id', $user_id)->get();
        return view('seniority_pauses.index')->with('datas', $datas)->with('user_id', $user_id);
    }

    public function create($user_id)
    {
        $user = User::where('id', $user_id)->first();
        return view('seniority_pauses.create')->with('user_id', $user_id)->with('user', $user);
    }

    public function store(Request $request, $user_id)
    {
        $data = new SeniorityPauses;
        $data->pause_date = $request->pause_date;
        $data->resume_date = $request->resume_date;
        $data->user_id = $user_id;
        $data->save();

        return redirect()->route('SeniorityPausess', $user_id)->with('success', '暫停資料新增成功！');
    }

    public function show(Request $request, $user_id, $id)
    {
        $user = User::where('id', $user_id)->first();
        $data = SeniorityPauses::where('id', $id)->first();
        return view('seniority_pauses.edit')->with('user_id', $user_id)->with('user', $user)->with('id', $id)->with('data', $data);
    }

    public function update(Request $request, $user_id, $id)
    {
        $user = User::where('id', $user_id)->first();
        $data = SeniorityPauses::where('id', $id)->first();
        $data->pause_date = $request->pause_date;
        $data->resume_date = $request->resume_date;
        $data->save();
        return redirect()->route('SeniorityPausess', $user_id)->with('success', '暫停資料新增成功！');
    }

    public function delete(Request $request, $user_id, $id)
    {
        $user = User::where('id', $user_id)->first();
        $data = SeniorityPauses::where('id', $id)->first();
        return view('seniority_pauses.del')->with('user_id', $user_id)->with('user', $user)->with('id', $id)->with('data', $data);
    }

    public function destroy(Request $request, $user_id, $id)
    {
        $user = User::where('id', $user_id)->first();
        $data = SeniorityPauses::where('id', $id)->first();
        $data->delete();
        return redirect()->route('SeniorityPausess', $user_id)->with('success', '暫停資料新增成功！');
    }
}

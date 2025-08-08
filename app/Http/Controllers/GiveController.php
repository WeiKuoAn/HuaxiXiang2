<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Give;
use App\Models\User;

class GiveController extends Controller
{
    public function index(Request $request)
    {
        $datas = Give::where('sale_on', 'like', '%' . $request->sale_on . '%')->get();
        $users = User::where('status', 1)->get();
        return view('give.index')->with('request', $request)->with('datas', $datas)->with('users', $users);
    }

    public function create(Request $request)
    {
        $users = User::where('status', 1)->get();
        return view('give.create')->with('request', $request)->with('users', $users);
    }

    public function store(Request $request)
    {
        $give = new Give();
        $give->sale_on = $request->sale_on;
        $give->value = $request->value;
        $give->price = $request->price;
        $give->user_id = $request->user_id;
        $give->save();
        return redirect()->route('give.index')->with('success', '新增成功');
    }

    public function edit(Request $request, $id)
    {
        $data = Give::where('id', $id)->first();
        $users = User::where('status', 1)->get();
        return view('give.edit')->with('request', $request)->with('data', $data)->with('users', $users);
    }

    public function update(Request $request, $id)
    {
        $give = Give::where('id', $id)->first();
        $give->sale_on = $request->sale_on;
        $give->value = $request->value;
        $give->price = $request->price;
        $give->user_id = $request->user_id;
        $give->save();
        return redirect()->route('give.index')->with('success', '編輯成功');
    }

    public function delete(Request $request, $id)
    {
        $data = Give::where('id', $id)->first();
        $users = User::where('status', 1)->get();
        return view('give.del')->with('request', $request)->with('data', $data)->with('users', $users);
    }

    public function destroy(Request $request, $id)
    {
        $give = Give::where('id', $id)->first();
        $give->delete();
        return redirect()->route('give.index')->with('success', '刪除成功');
    }
}

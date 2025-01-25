<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pay;

class PayController extends Controller
{
    public function index()
    {
        $datas = Pay::orderby('status', 'asc')->orderby('seq', 'asc')->paginate(50);
        return view('pay_suject.index')->with('datas', $datas);
    }

    public function create()
    {
        return view('pay_suject.create');
    }

    public function store(Request $request)
    {
        $pay = new Pay();
        $pay->name = $request->name;
        $pay->seq = $request->seq;
        $pay->status = $request->status;
        $pay->suject_type = $request->suject_type;
        $pay->view_status = $request->view_status;
        $pay->calculate = $request->calculate;
        $pay->comment = $request->comment;
        $pay->save();
        return redirect()->route('pay.sujects');
    }

    public function show($id)
    {
        $data = Pay::where('id', $id)->first();
        return view('pay_suject.edit')->with('data', $data);
    }

    public function update($id, Request $request)
    {
        $pay = Pay::where('id', $id)->first();
        $pay->name = $request->name;
        $pay->seq = $request->seq;
        $pay->status = $request->status;
        $pay->comment = $request->comment;
        $pay->suject_type = $request->suject_type;
        $pay->view_status = $request->view_status;
        $pay->calculate = $request->calculate;
        $pay->save();
        return redirect()->route('pay.sujects');
    }
}

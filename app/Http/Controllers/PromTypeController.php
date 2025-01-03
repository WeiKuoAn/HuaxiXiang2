<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PromType;

class PromTypeController extends Controller
{
    public function index(Request $request)
    {
        $datas = PromType::get();

        return view('prom_type.index')->with('datas',$datas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('prom_type.create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $prom = new PromType;
        $prom->name = $request->name;
        $prom->code = $request->code;
        $prom->status = $request->status;
        $prom->save();
        return redirect()->route('prom_types');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = PromType::where('id',$id)->first();
        return view('prom_type.edit')->with('data',$data);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $prom = PromType::where('id',$id)->first();
        $prom->name = $request->name;
        $prom->code = $request->code;
        $prom->status = $request->status;
        $prom->save();
        return redirect()->route('prom_types');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

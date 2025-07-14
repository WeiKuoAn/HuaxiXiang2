<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prom;
use App\Models\PromType;
use Illuminate\Support\Facades\Redis;

class PromController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $datas = Prom::orderby('type','asc')->orderby('seq','asc')->orderby('status','asc');
        $type = $request->type;
        if($type){
            $datas = $datas->where('type', $type);
        }
        $datas = $datas->paginate(50);
        $prom_types = PromType::where('status','up')->get();

        return view('prom.index')->with('datas',$datas)->with('request',$request)->with('prom_types',$prom_types);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $prom_types = PromType::where('status','up')->get();
        return view('prom.create')->with('prom_types',$prom_types);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $prom = new Prom;
        $prom->type = $request->type;
        $prom->name = $request->name;
        $prom->seq = $request->seq;
        $prom->status = $request->status;
        $prom->is_custom_product = $request->is_custom_product;
        $prom->save();
        return redirect()->route('proms');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $prom_types = PromType::where('status','up')->get();
        $data = prom::where('id',$id)->first();
        return view('prom.edit')->with('data',$data)->with('prom_types',$prom_types);
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
        $prom = Prom::where('id',$id)->first();
        $prom->type = $request->type;
        $prom->name = $request->name;
        $prom->seq = $request->seq;
        $prom->status = $request->status;
        $prom->is_custom_product = $request->is_custom_product;
        $prom->save();
        return redirect()->route('proms');
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

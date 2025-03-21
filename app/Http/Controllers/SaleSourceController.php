<?php

namespace App\Http\Controllers;

use App\Models\SaleSource;
use Illuminate\Http\Request;

class SaleSourceController extends Controller
{
    public function index(){
        $datas = SaleSource::orderby('seq','asc')->orderby('status','asc')->paginate(50);
        return view('source.index')->with('datas',$datas);
    }

    public function create(){
        return view('source.create');
    }

    public function store(Request $request){
        $source = new SaleSource();
        $source->name = $request->name;
        $source->code = $request->code;
        $source->seq = $request->seq;
        $source->status = $request->status;
        $source->save();
        return redirect()->route('sources');
    }

    public function show($id){
        $source = SaleSource::where('id',$id)->first();
        return view('source.edit')->with('source',$source);
    }

    public function update($id, Request $request){
        $source = SaleSource::where('id',$id)->first();
        $source->name = $request->name;
        $source->code = $request->code;
        $source->seq = $request->seq;
        $source->status = $request->status;
        $source->save();
        return redirect()->route('sources');
    }

    public function delete($id){
        $source = SaleSource::where('id',$id)->first();
        return view('source.del')->with('source',$source);
    }

    public function destroy($id, Request $request){
        SaleSource::where('id',$id)->delete();
        return redirect()->route('sources');
    }
}

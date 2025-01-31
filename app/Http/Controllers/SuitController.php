<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suit;

class SuitController extends Controller
{
    public function index(){
        $datas = Suit::orderby('seq','asc')->paginate(50);
        return view('suit.index')->with('datas',$datas);
    }

    public function create(){
        return view('suit.create');
    }

    public function store(Request $request){
        $suit = new Suit();
        $suit->name = $request->name;
        $suit->seq = $request->seq;
        $suit->status = $request->status;
        $suit->save();
        return redirect()->route('suits');
    }

    public function show($id){
        $suit = Suit::where('id',$id)->first();
        return view('suit.edit')->with('suit',$suit);
    }

    public function update($id, Request $request){
        $suit = Suit::where('id',$id)->first();
        $suit->name = $request->name;
        $suit->seq = $request->seq;
        $suit->status = $request->status;
        $suit->save();
        return redirect()->route('suits');
    }

    public function delete($id){
        $suit = Suit::where('id',$id)->first();
        return view('suit.del')->with('suit',$suit);
    }

    public function destroy($id, Request $request){
        Suit::where('id',$id)->delete();
        return redirect()->route('suits');
    }
}

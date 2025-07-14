<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merit;

class MeritController extends Controller
{
    public function index()
    {
        $datas = Merit::all();
        return view('merit.index', compact('datas'));
    }

    public function create()
    {
        return view('merit.create');
    }

    public function store(Request $request)
    {
        $merit = new Merit();
        $merit->date = $request->date;
        $merit->variety = $request->variety;
        $merit->user_id = auth()->user()->id;
        $merit->save();
        return redirect()->route('merit.index');
    }

    public function show($id)
    {
        $data = Merit::find($id);
        return view('merit.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $merit = Merit::find($id);
        $merit->date = $request->date;
        $merit->variety = $request->variety;
        $merit->save();
        return redirect()->route('merit.index');
    }
}

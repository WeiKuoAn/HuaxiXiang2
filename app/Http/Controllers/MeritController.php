<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merit;
use App\Models\User;

class MeritController extends Controller
{
    public function index(Request $request)
    {
        $datas = Merit::query();
        $after_date = $request->after_date;
            if ($after_date) {
                $datas = $datas->where('date', '>=', $after_date);
            }
            $before_date = $request->before_date;
            if ($before_date) {
                $datas = $datas->where('date', '<=', $before_date);
            }
        $datas = $datas->get();
        return view('merit.index', compact('datas', 'request'));
    }

    public function create()
    {
        $users = User::where('status', 1)->get();
        return view('merit.create', compact('users'));
    }

    public function store(Request $request)
    {
        $merit = new Merit();
        $merit->date = $request->date;
        $merit->variety = $request->variety;
        $merit->kg = $request->kg;
        $merit->user_id = $request->user_id;
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
        $merit->kg = $request->kg;
        $merit->user_id = $request->user_id;
        $merit->save();
        return redirect()->route('merit.index');
    }
}

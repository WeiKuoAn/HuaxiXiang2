<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TargetCategories;

class TargetCategoriesController extends Controller
{
    public function index()
    {
        $datas = TargetCategories::paginate(50);
        return view('targetCategory.index')->with('datas',$datas);
    }

    public function create()
    {
        return view('targetCategory.create');
    }

    public function store(Request $request)
    {
        $TargetCategories = new TargetCategories;
        $TargetCategories->name = $request->name;
        $TargetCategories->description = $request->description;
        $TargetCategories->status = $request->status;
        $TargetCategories->save();
        return redirect()->route('targetCategories');
    }

    public function show($id)
    {
        $data = TargetCategories::where('id',$id)->first();
        return view('targetCategory.edit')->with('data',$data);
    }


    public function update(Request $request, $id)
    {
        $TargetCategories = TargetCategories::find($id);
        $TargetCategories->name = $request->name;
        $TargetCategories->status = $request->status;
        $TargetCategories->description = $request->description;
        $TargetCategories->save();
        return redirect()->route('targetCategories');
    }

    public function delete($id)
    {
        $data = TargetCategories::where('id',$id)->first();
        return view('targetCategory.del')->with('data',$data);
    }

    public function destory($id)
    {
        $data = TargetCategories::where('id',$id)->delete();
        return redirect()->route('targetCategories');
    }
}

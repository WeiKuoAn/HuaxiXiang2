<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SouvenirType;
use App\Models\Souvenir;

class SouvenirTypeController extends Controller
{
    public function souvenirType_search(Request $request)
    {
        if ($request->ajax()) {
            $output = "";
            $souvenirs = Souvenir::where('type', $request->souvenir_type_id)->where('status', 'up')->orderby('seq', 'asc')->get();

            if (isset($souvenirs)) {
                foreach ($souvenirs as $key => $souvenir) {
                    $output .=  '<option value="' . $souvenir->id . '">' . $souvenir->name . '</option>';
                }
            } else {
                $output .=  '<option value="">請選擇...</option>';
            }
            return Response($output);
        }
    }

    public function index(Request $request)
    {
        $datas = SouvenirType::get();

        return view('souvenir_type.index')->with('datas',$datas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('souvenir_type.create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $prom = new SouvenirType;
        $prom->name = $request->name;
        $prom->status = $request->status;
        $prom->save();
        return redirect()->route('souvenir_types');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = SouvenirType::where('id',$id)->first();
        return view('souvenir_type.edit')->with('data',$data);
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
        $prom = SouvenirType::where('id',$id)->first();
        $prom->name = $request->name;
        $prom->status = $request->status;
        $prom->save();
        return redirect()->route('souvenir_types');
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Souvenir;
use App\Models\SouvenirType;

class SouvenirController extends Controller
{
    
    public function souvenir_search(Request $request)
    {
        if ($request->ajax()) {
            $output = "";

            $souvenirs = Souvenir::where('type', $request->souvenir_id)->where('status', 'up')->orderby('seq', 'asc')->get();

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
        $datas = Souvenir::orderby('type','asc')->orderby('seq','asc')->orderby('status','asc');
        $type = $request->type;
        if($type){
            $datas = $datas->where('type', $type);
        }
        $datas = $datas->paginate(50);
        $souvenir_types = SouvenirType::where('status','up')->get();

        return view('souvenir.index')->with('datas',$datas)->with('request',$request)->with('souvenir_types',$souvenir_types);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $souvenir_types = SouvenirType::where('status','up')->get();
        return view('souvenir.create')->with('souvenir_types',$souvenir_types);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $data = new Souvenir;
        $data->type = $request->type;
        $data->name = $request->name;
        $data->seq = $request->seq;
        $data->price = $request->price;
        $data->status = $request->status;
        $data->save();
        return redirect()->route('souvenirs');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $souvenir_types = SouvenirType::where('status','up')->get();
        $data = Souvenir::where('id',$id)->first();
        return view('souvenir.edit')->with('data',$data)->with('souvenir_types',$souvenir_types);
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
        $data = Souvenir::where('id',$id)->first();
        $data->type = $request->type;
        $data->name = $request->name;
        $data->seq = $request->seq;
        $data->price = $request->price;
        $data->status = $request->status;
        $data->save();
        return redirect()->route('souvenirs');
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

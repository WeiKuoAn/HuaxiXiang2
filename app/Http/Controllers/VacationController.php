<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vacation;
use App\Models\User;
use Carbon\Carbon;

class VacationController extends Controller
{
    public function index()
    {
        $vacations = Vacation::get();
        $datas = [];
        foreach($vacations as $vacation)
        {
            $datas[$vacation->year]['year'] = $vacation->year;
            $datas[$vacation->year]['months'] = Vacation::where('year',$vacation->year)->get();
            $datas[$vacation->year]['total'] = 0;
        }

        foreach($datas as $data)
        {
            foreach($data['months'] as $month)
            {
                $datas[$vacation->year]['total'] += $month->day;
            }
        }
        return view('vacation.index')->with('datas',$datas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $years = range(Carbon::now()->year, 2023);
        return view('vacation.create')->with('years',$years)->with('hint',0);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $years = range(Carbon::now()->year, 2023);
        $vaction = Vacation::where('year',$request->year)->first();
        if(isset($vaction)){
            return view('vacation.create')->with('years',$years)->with(['hint' => '1']);
        }else{
            if(isset($request->months))
            {
                foreach($request->months as $key=>$month)
                {
                    $data = new Vacation;
                    $data->year = $request->year;
                    $data->month = $month;
                    $data->day = $request->days[$key];
                    $data->save();
                }
            }
            return redirect()->route('vacations');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($year)
    {
        $years = range(Carbon::now()->year, 2023);
        $data = Vacation::where('year',$year)->first();
        $now_year = $data->year;

        $vacations = Vacation::where('year',$year)->get();
        return view('vacation.edit')->with('data',$data)
                                    ->with('years',$years)
                                    ->with('now_year',$now_year)
                                    ->with('vacations',$vacations);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $year)
    {
        if(isset($request->months))
        {
            $datas = Vacation::where('year',$year)->get();
            foreach($datas as $key=>$data)
            {
                $data->day = $request->days[$key];
                $data->save();
            }
        }

        return redirect()->route('vacations');
    }
}

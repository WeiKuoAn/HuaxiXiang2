<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Works;
use App\Models\User;
use App\Models\Sale;
use App\Models\IncomeData;
use App\Models\Customer;
use App\Models\Pay;
use App\Models\PayData;
use App\Models\Sale_gdpaper;
use Illuminate\Support\Facades\Auth;

class WorkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function loginSuccess(){
        $now = Carbon::now()->locale('zh-tw');
        $work = Works::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
        if(Auth::user()->status != 1){
            return view('dashboard_index')->with('now',$now)->with('work',$work);
        }else{
            return redirect('/');
        }
    }
    public function index()
    {

        $now = Carbon::now()->locale('zh-tw');
        $today = Carbon::today();
        $firstDay = Carbon::now()->firstOfMonth();
        $lastDay = Carbon::now()->lastOfMonth();

        $sale_today = Sale::where('sale_date',$today->format("Y-m-d"))->whereIn('pay_id',['A','B','C'])->count();
        $price = Sale::where('sale_date',$today->format("Y-m-d"))->whereIn('pay_id',['A','B','C','D'])->sum('pay_price');
        
        //月營收
        $sale_month = Sale::where('sale_date','>=',$firstDay->format("Y-m-d"))->where('sale_date','<=',$lastDay->format("Y-m-d"))->sum('pay_price');
        $income_month = IncomeData::where('income_date','>=',$firstDay->format("Y-m-d"))->where('income_date','<=',$lastDay->format("Y-m-d"))->sum('price');
        $price_month = $sale_month + $income_month;
        $gdpaper_month = Sale_gdpaper::where('created_at','>=',$firstDay->format("Y-m-d"))->where('created_at','<=',$lastDay->format("Y-m-d"))->sum('gdpaper_total');
        
        //月支出
        $pay_month = PayData::where('pay_date','>=',$firstDay->format("Y-m-d"))->where('pay_date','<=',$lastDay->format("Y-m-d"))->sum('price');
        
        //營業淨利
        $net_income =  $price_month -  $pay_month;

        $income = IncomeData::where('income_date',$today->format("Y-m-d"))->sum('price');
        $total_today_incomes = intval($price) + intval($income);
        $check_sale = Sale::where('status',3)->count();
        $cust_nums = Customer::count();
        $work = Works::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
        // dd($work);
        // dd($now);
        if(Auth::user()->status != 1){
            return view('dashboard')->with(['now' => $now, 'work' => $work , 'sale_today'=>$sale_today 
            , 'cust_nums'=>$cust_nums , 'check_sale'=>$check_sale , 'total_today_incomes'=>$total_today_incomes
            , 'price_month'=>$price_month , 'pay_month'=>$pay_month , 'net_income'=>$net_income , 'gdpaper_month'=>$gdpaper_month]);
        }else{
            return view('auth.login');
        }
       
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $now = Carbon::now();
        // dd($$request->work_time);。
        //0是上班，1是中途上班，2是加班，3是下班
        if ($request->work_time == '0') {
            $work = new Works;
            $work->user_id = Auth::user()->id;
            $work->worktime = $now;
            $work->status = '0';
            $work->remark = ' ';
            $work->save();
            $work = Works::orderBy('id', 'desc')->first();
        }
        else{
            if ($request->overtime == '1') {
                $work = new Works;
                $work->user_id = Auth::user()->id;
                $work->worktime = $request->worktime;
                $work->dutytime = $request->dutytime;
                $work->status = '1';
                $work->total = floor(Carbon::parse($request->worktime)->floatDiffInHours($request->dutytime));
                $work->remark = $request->remark;
                $work->save();
            }
        }
        if ($request->dutytime == '2') {
            //判斷每個使用者的最新的一筆打卡紀錄，一定要where user，否則其他user點選下班會相衝突。
            $worktime = Works::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
            if ($worktime->worktime != null) {
                $worktime->dutytime = $now;
                $worktime->total = Works::work_sum($worktime->id);
                $worktime->save();
            }
        }
        // dd($request->overtime);
        return redirect()->route('dashboard');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function showuserwork($workId)
    {
        if (Auth::user()->level == '0') {
            $work = Works::where('id', $workId)->first();
            return view('edituserwork')->with(['work' => $work]);
        } else {
            return redirect()->route('dashboard');
        }
    }

    public function edituserwork(Request $request, $workId)
    {
        $work = Works::where('id', $workId)->first();
        $userId = $work->user_id;
        $work->worktime = $request->worktime;
        $work->dutytime = $request->dutytime;
        $work->status = $request->status;
        $work->total = floor(Carbon::parse($request->worktime)->floatDiffInHours($request->dutytime));
        if ($request->remark == '') {
            $work->remark = '';
        } else {
            $work->remark = $request->remark;
        }
        $work->save();

        return redirect()->route('userwork', $userId);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit(Request $request)
    {
        // $now = Carbon::now();
        // if($request->dutytime == 'dutytime'){
        //     $work = new Works;
        //     $work->user_id = Auth::user()->id;
        //     $work->worktime = $now;
        //     $work->dutytime = ' ';
        //     $work->status = '0';
        //     $work->remark = ' ';
        //     $work->save();
        // }
        // return view('dashboard')->with(['now'=>$now]);
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
        //
    }


    public function showdeluserwork($workId)
    {
        if (Auth::user()->level == '0') {
            $work = Works::where('id', $workId)->first();
            return view('deluserwork')->with(['work' => $work]);
        } else {
            return redirect()->route('dashboard');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deluserwork($workId)
    {
        $work = Works::where('id', $workId)->first();
        $userId = $work->user_id;
        $work->delete();
        return redirect()->route('userwork', $userId);
    }

    public function personwork(Request $request) //個人出勤紀錄
    {
        if ($request->startdate || $request->enddate) {
            if($request->startdate){
                $works = Works::where('user_id',  Auth::user()->id)->where('worktime','>=',$request->startdate)->orderBy('id', 'desc')->paginate(30);
                $work_sum = Works::where('user_id',  Auth::user()->id)->where('worktime','>=',$request->startdate)->sum('total');
            }
            if($request->enddate){
                $works = Works::where('user_id',  Auth::user()->id)->where('worktime','<=',$request->enddate)->orderBy('id', 'desc')->paginate(30);
                $work_sum = Works::where('user_id',  Auth::user()->id)->where('worktime','<=',$request->enddate)->sum('total');
            }
            if($request->startdate && $request->enddate){
                $works = Works::where('user_id',  Auth::user()->id)->where('worktime','>=',$request->startdate)->where('worktime','<=',$request->enddate)->orderBy('id', 'desc')->paginate(30);
                $work_sum = Works::where('user_id',  Auth::user()->id)->where('worktime','>=',$request->startdate)->where('worktime','<=',$request->enddate)->sum('total');
            }
            $condition = $request->all();
        } else {
            $works = Works::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->paginate(15);
            $work_sum = Works::where('user_id', Auth::user()->id)->sum('total');
            $condition = '';
        }
        return view('personwork')->with(['works' => $works, 'work_sum' => $work_sum, 'condition' => $condition , 'request'=>$request]);
    }

    public function userwork(Request $request, $userId) //管理者查看個人出勤紀錄
    {
        $user = User::find($userId);
        
        if ($request->startdate || $request->enddate) {
            if($request->startdate){
                $works = Works::where('user_id', $userId)->where('worktime','>=',$request->startdate)->orderBy('worktime','desc')->paginate(30);
                $work_sum = Works::where('user_id', $userId)->where('worktime','>=',$request->startdate)->sum('total');
            }
            if($request->enddate){
                $works = Works::where('user_id', $userId)->where('worktime','<=',$request->enddate)->orderBy('worktime','desc')->paginate(30);
                $work_sum = Works::where('user_id', $userId)->where('worktime','<=',$request->enddate)->sum('total');
            }
            if($request->startdate && $request->enddate){
                $works = Works::where('user_id', $userId)->where('worktime','>=',$request->startdate)->where('worktime','<=',$request->enddate)->orderBy('worktime','desc')->paginate(30);
                $work_sum = Works::where('user_id', $userId)->where('worktime','>=',$request->startdate)->where('worktime','<=',$request->enddate)->sum('total');
            }
            $condition = $request->all();
        } else {
            $works = Works::where('user_id', $userId)->orderBy('worktime','desc')->paginate(30);
            $work_sum = Works::where('user_id', $userId)->sum('total');
            $condition = '';
        }
        // $test = Works::work_sum($works->id);
        // dd($test);
        return view('userwork')->with(['works' => $works, 'user' => $user, 'work_sum' => $work_sum, 'condition' => $condition , 'request'=>$request]);
    }
}

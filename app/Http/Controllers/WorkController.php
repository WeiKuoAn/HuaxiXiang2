<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Works;
use App\Models\User;
use App\Models\Sale;
use App\Models\IncomeData;
use App\Models\Customer;
use App\Models\Pay;
use App\Models\PayData;
use App\Models\PayItem;
use App\Models\Sale_gdpaper;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;


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
        $pay_month = PayItem::where('pay_date','>=',$firstDay->format("Y-m-d"))->where('pay_date','<=',$lastDay->format("Y-m-d"))->whereNotIn('pay_id',['23'])->sum('price');
        
        //營業淨利
        $net_income =  $price_month -  $pay_month;

        $income = IncomeData::where('income_date',$today->format("Y-m-d"))->sum('price');
        $total_today_incomes = intval($price) + intval($income);
        $check_sale = Sale::where('status',3)->count();
        $cust_nums = Customer::count();
        $work = Works::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();

        $contract_datas = Contract::whereIn('renew',[0,1])->get();

        
        // dd($now);
        if(Auth::user()->status != 1){
            return view('dashboard')->with(['now' => $now, 'work' => $work , 'sale_today'=>$sale_today 
            , 'cust_nums'=>$cust_nums , 'check_sale'=>$check_sale , 'total_today_incomes'=>$total_today_incomes
            , 'price_month'=>$price_month , 'pay_month'=>$pay_month , 'net_income'=>$net_income , 'gdpaper_month'=>$gdpaper_month
            , 'contract_datas'=>$contract_datas]);
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
     * 顯示批次新增出勤記錄的表單
     *
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function batchCreate($userId)
    {
        $user = User::find($userId);
        return view('work.create')->with(['user' => $user]);
    }

    /**
     * 批次儲存出勤記錄
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function batchStore(Request $request, $userId)
    {
        $records = $request->records;
        $successCount = 0;
        $errorDates = [];
        
        foreach ($records as $record) {
            // 檢查必填欄位
            if (empty($record['date']) || empty($record['worktime']) || empty($record['dutytime'])) {
                continue;
            }
            
            // 組合完整的日期時間
            $workDateTime = $record['date'] . ' ' . $record['worktime'];
            $dutyDateTime = $record['date'] . ' ' . $record['dutytime'];
            
            // 檢查該用戶該日期是否已存在記錄
            $existingWork = Works::where('user_id', $userId)
                ->whereDate('worktime', $record['date'])
                ->first();
            
            if ($existingWork) {
                $errorDates[] = $record['date'];
                continue;
            }
            
            // 計算工作時數
            $worktime = Carbon::parse($workDateTime);
            $dutytime = Carbon::parse($dutyDateTime);
            
            // 如果下班時間早於上班時間，表示跨日，下班時間加一天
            if ($dutytime->lt($worktime)) {
                $dutytime->addDay();
            }
            
            $work_hours = $worktime->floatDiffInHours($dutytime);
            
            // 滿8小時要休息1小時，所以如果工作滿9小時就要減1小時
            if ($work_hours >= 9) {
                $work_hours = $work_hours - 1;
            }
            
            // 創建出勤記錄
            $work = new Works();
            $work->user_id = $userId;
            $work->worktime = $workDateTime;
            $work->dutytime = $dutyDateTime;
            $work->total = floor($work_hours);
            $work->status = $record['status'] ?? '0';
            $work->remark = $record['remark'] ?? '';
            $work->save();
            
            $successCount++;
        }
        
        // 回傳結果訊息
        if ($successCount > 0 && count($errorDates) == 0) {
            return redirect()->route('user.work.index', $userId)
                ->with('success', "成功新增 {$successCount} 筆出勤記錄");
        } elseif ($successCount > 0 && count($errorDates) > 0) {
            $errorDateStr = implode(', ', $errorDates);
            return redirect()->route('user.work.index', $userId)
                ->with('warning', "成功新增 {$successCount} 筆記錄，但以下日期已存在：{$errorDateStr}");
        } else {
            return redirect()->route('user.work.batch.create', $userId)
                ->with('error', '新增失敗，所有日期都已存在或資料不完整');
        }
    }
    
    /**
     * 檢查指定日期是否已存在出勤記錄（API）
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkDateExists(Request $request)
    {
        $userId = $request->user_id;
        $date = $request->date;
        
        $exists = Works::where('user_id', $userId)
            ->whereDate('worktime', $date)
            ->exists();
        
        return response()->json(['exists' => $exists]);
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
                $work_hours = Carbon::parse($request->worktime)->floatDiffInHours($request->dutytime);
                
                // 滿8小時要休息1小時，所以如果工作滿9小時就要減1小時
                if ($work_hours >= 9) {
                    $work_hours = $work_hours - 1;
                }
                
                $work->total = floor($work_hours);
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
        // if (Auth::user()->level == '0') {
            $work = Works::where('id', $workId)->first();
            return view('work.edit')->with(['work' => $work]);
        // } else {
        //     return redirect()->route('dashboard');
        // }
    }

    public function edituserwork(Request $request, $workId)
    {
        $work = Works::where('id', $workId)->first();
        $userId = $work->user_id;
        $work->worktime = $request->worktime;
        $work->dutytime = $request->dutytime;
        $work->total = $request->total;
        $work->status = $request->status;
        // $work->total = floor(Carbon::parse($request->worktime)->floatDiffInHours($request->dutytime));
        if ($request->remark == '') {
            $work->remark = '';
        } else {
            $work->remark = $request->remark;
        }
        $work->save();

        return redirect()->route('user.work.index', $userId);
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
            return view('work.del')->with(['work' => $work]);
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
        return redirect()->route('user.work.index', $userId);
    }

    public function personwork(Request $request) //個人出勤紀錄
    {
        if ($request->startdate || $request->enddate) {
            $startdate = $request->startdate.' 00:00:00';
            $enddate = $request->enddate.' 23:59:59';
            if($request->startdate){
                $works = Works::where('user_id',  Auth::user()->id)->where('worktime','>=',$startdate)->orderBy('id', 'desc')->paginate(30);
                $work_sum = Works::where('user_id',  Auth::user()->id)->where('worktime','>=',$startdate)->sum('total');
            }
            if($request->enddate){
                $works = Works::where('user_id',  Auth::user()->id)->where('worktime','<=',$enddate)->orderBy('id', 'desc')->paginate(30);
                $work_sum = Works::where('user_id',  Auth::user()->id)->where('worktime','<=',$enddate)->sum('total');
            }
            if($request->startdate && $request->enddate){
                $works = Works::where('user_id',  Auth::user()->id)->where('worktime','>=',$startdate)->where('worktime','<=',$enddate)->orderBy('id', 'desc')->paginate(30);
                $work_sum = Works::where('user_id',  Auth::user()->id)->where('worktime','>=',$startdate)->where('worktime','<=',$enddate)->sum('total');
            }
            $condition = $request->all();
        } else {
            $works = Works::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->paginate(15);
            $work_sum = Works::where('user_id', Auth::user()->id)->sum('total');
            $condition = '';
        }
        return view('personwork')->with(['works' => $works, 'work_sum' => $work_sum, 'condition' => $condition , 'request'=>$request]);
    }

    public function user_work(Request $request, $userId) //管理者查看個人出勤紀錄
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
        return view('work.index')->with(['works' => $works, 'user' => $user, 'work_sum' => $work_sum, 'condition' => $condition , 'request'=>$request]);
    }

    public function export(Request $request,$userId)
    {
        if ($request->startdate || $request->enddate) {
            if($request->startdate){
                $works = Works::where('user_id', $userId)->where('worktime','>=',$request->startdate)->orderBy('worktime','desc')->get();
            }
            if($request->enddate){
                $works = Works::where('user_id', $userId)->where('worktime','<=',$request->enddate)->orderBy('worktime','desc')->get();
            }
            if($request->startdate && $request->enddate){
                $works = Works::where('user_id', $userId)->where('worktime','>=',$request->startdate)->where('worktime','<=',$request->enddate)->orderBy('worktime','desc')->get();
            }
        } else {
            $works = Works::where('user_id', $userId)->orderBy('worktime','desc')->get();
        }
        $user= User::find($userId);
        // dd($user);
        // dd($request->startdate);

        $fileName = $user->name.'班表匯出' . date("Y-m-d") . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        $columns = array('日期','上班時間', '下班時間', '時間', '備註' , '狀態');

        $callback = function() use($works, $columns) {
            
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF).chr(0xBB).chr(0xBF), 3); 
            fputcsv($file, $columns);
            // $row['姓名'] = date('Y-m-d', strtotime($data->worktime));
            foreach ($works as $key=>$data) {
                $row['日期'] = date('Y-m-d', strtotime($data->worktime));
                $row['上班時間'] = date('H:i', strtotime($data->worktime));
                if($data->dutytime != null){
                    $row['下班時間'] = date('H:i', strtotime($data->dutytime));
                }else{
                    $row['下班時間'] = '尚未下班';
                }
                $row['時間'] = $data->total;
                $row['備註'] = $data->remark;
                if($data->status == '0'){
                    $row['狀態'] = '值班';
                }else{
                    $row['狀態'] = '補簽';
                }
                fputcsv($file, array($row['日期'],$row['上班時間'],$row['下班時間']
                                    ,$row['時間'],$row['備註'], $row['狀態']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

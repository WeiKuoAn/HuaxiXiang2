<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Works;
use App\Models\Contract;
use App\Models\Lamp;
use App\Models\User;
use App\Models\Sale;
use App\Models\IncomeData;
use App\Models\Customer;
use App\Models\Pay;
use App\Models\PayData;
use App\Models\PayItem;
use App\Models\Sale_gdpaper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\TargetData;

class DashboardController extends Controller
{
    //打卡部份

    public function loginSuccess(){
        $now = Carbon::now()->locale('zh-tw');
        $now_day = Carbon::now()->subMonths(1)->format("Y-m-d");
        $two_month_day = Carbon::now()->addMonths(2)->format("Y-m-d");
        $one_month_day = Carbon::now()->addMonths(1)->format("Y-m-d");
        // dd($one_month_day);
        // dd(Auth::user());
        if(Auth::user()->status != 1){
            $work = Works::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
            $contract_datas = Contract::whereIn('renew',[0,1])->where('end_date','>=',$now_day)->where('end_date','<=',$two_month_day)->whereNull('close_date')->orderby('end_date','asc')->get();
            $lamp_datas = Lamp::whereIn('renew',[0,1])->where('end_date','>=',$now_day)->where('end_date','<=',$one_month_day)->whereNull('close_date')->orderby('end_date','asc')->get();
            // dd($contract_datas);
            return view('index')->with('now',$now)->with('work',$work)->with('contract_datas',$contract_datas)->with('lamp_datas',$lamp_datas);
        }else{
            return redirect('/');
        }
    }

    public function store(Request $request)
    {
        // dd('1');
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
        return redirect()->route('index');
    }

    //當月資訊
    public function index()
    {

        $now = Carbon::now()->locale('zh-tw');
        $today = Carbon::today();
        $firstDay = Carbon::now()->firstOfMonth();
        $lastDay = Carbon::now()->lastOfMonth();

        $sale_today = Sale::where('status','9')->where('sale_date',$today->format("Y-m-d"))->whereIn('pay_id', ['A', 'C', 'E'])->count();
        $price = Sale::where('status','9')->where('sale_date',$today->format("Y-m-d"))->sum('pay_price');
        
        //月營收
        $sale_month = Sale::where('status','9')->where('sale_date','>=',$firstDay->format("Y-m-d"))->where('sale_date','<=',$lastDay->format("Y-m-d"))->sum('pay_price');
        $income_month = IncomeData::where('income_date','>=',$firstDay->format("Y-m-d"))->where('income_date','<=',$lastDay->format("Y-m-d"))->sum('price');
        $price_month = $sale_month + $income_month;
        $gdpaper_month = DB::table('sale_data')
                             ->join('sale_gdpaper','sale_gdpaper.sale_id', '=' , 'sale_data.id')
                             ->where('sale_data.sale_date','>=',$firstDay->format("Y-m-d"))
                             ->where('sale_data.sale_date','<=',$lastDay->format("Y-m-d"))
                             ->where('sale_data.status','9')
                             ->sum('sale_gdpaper.gdpaper_total');
        // Sale_gdpaper::where('created_at','>=',$firstDay->format("Y-m-d"))->where('created_at','<=',$lastDay->format("Y-m-d"))->sum('gdpaper_total');
        
        //月支出
         $pay_month =PayItem::join('pay', 'pay_item.pay_id', '=', 'pay.id')
                                                        ->where('pay_item.status', '1')
                                                        ->where('pay_item.pay_date', '>=', $firstDay->format("Y-m-d"))
                                                        ->where('pay_item.pay_date', '<=', $lastDay->format("Y-m-d"))
                                                        ->where('pay.calculate', '!=', 1)
                                                        ->select('pay_item.*') // 選擇 pay_item 的欄位
                                                        ->sum('price');
        //營業淨利
        $net_income =  $price_month -  $pay_month;

        $income = IncomeData::where('income_date',$today->format("Y-m-d"))->sum('price');
        $total_today_incomes = intval($price) + intval($income);
        $check_sale = Sale::where('status',3)->count();
        $cust_nums = Customer::count();
        $work = Works::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
        // dd($work);
        // dd($now);
        //達標管理
        $jobId = strval(Auth::user()->job_id); // 確保它是字串

        $targetDatas = TargetData::join('target_item', 'target_data.id', '=', 'target_item.target_data_id')
                                   ->where('target_item.start_date','>=',$firstDay->format("Y-m-d"))
                                   ->where('target_item.end_date','<=',$lastDay->format("Y-m-d"))
                                   ->whereJsonContains('target_data.job_id', $jobId)->get();
        foreach($targetDatas as $targetData)
        {
            if($targetData->category_id == 1){//金紙銷售
                $targetData->manual_achieved =DB::table('sale_data')
                                                ->join('sale_gdpaper','sale_gdpaper.sale_id', '=' , 'sale_data.id')
                                                ->where('sale_data.status','9')
                                                ->where('sale_data.sale_date','>=',$firstDay->format("Y-m-d"))
                                                ->where('sale_data.sale_date','<=',$lastDay->format("Y-m-d"))
                                                ->sum('sale_gdpaper.gdpaper_total');
                
            }
            if($targetData->target_condition == "金額"){
                
            }

            if($targetData->target_condition == "數量"){
                
            }

            if($targetData->target_condition == "金額+數量"){
                
            }
            $targetData->percent = $targetData->target_amount == 0 ? 0 : round( intval($targetData->manual_achieved) / intval($targetData->target_amount)* 100, 2);
        }
        
        if(Auth::user()->status != 1){
            return view('dashboard')->with(['now' => $now, 'work' => $work , 'sale_today'=>$sale_today 
            , 'cust_nums'=>$cust_nums , 'check_sale'=>$check_sale , 'total_today_incomes'=>$total_today_incomes
            , 'price_month'=>$price_month , 'pay_month'=>$pay_month , 'net_income'=>$net_income , 'gdpaper_month'=>$gdpaper_month , 'targetDatas'=>$targetDatas]);
        }else{
            return view('auth.login');
        }
    }
}

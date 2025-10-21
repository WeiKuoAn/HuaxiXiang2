<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Works;
use App\Models\Contract;
use App\Models\Lamp;
use App\Models\User;
use App\Models\Sale;
use App\Models\Sale_prom;
use App\Models\IncomeData;
use App\Models\Customer;
use App\Models\Pay;
use App\Models\PayData;
use App\Models\PayItem;
use App\Models\Sale_gdpaper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\TargetData;
use App\Models\Task;
use App\Models\TaskItem;
use App\Models\Suit;
use App\Models\Product;
use App\Models\LeaveDay;

class DashboardController extends Controller
{
    //打卡部份

    public function loginSuccess()
    {
        $now = Carbon::now()->locale('zh-tw');
        $now_day = Carbon::now()->subMonths(1)->format("Y-m-d");
        $two_month_day = Carbon::now()->addMonths(2)->format("Y-m-d");
        $one_month_day = Carbon::now()->addMonths(1)->format("Y-m-d");
        // dd($one_month_day);
        // dd(Auth::user());
        if (Auth::user()->status != 1) {
            $users = User::where('status', '0')->get();
            // 顯示所有未完成的 Task（所有人都可以看到）
            $tasks = Task::where('status', 0)
                ->whereHas('items', function($query) {
                    $query->where('status', 0); // 只顯示還有未完成項目的任務
                })
                ->with(['created_users', 'items.user'])
                ->orderBy('end_date', 'asc')
                ->get();
            $work = Works::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
            $contract_datas = Contract::whereIn('renew', [0, 1])->where('end_date', '>=', $now_day)->where('end_date', '<=', $two_month_day)->whereNull('close_date')->orderby('end_date', 'asc')->get();
            $lamp_datas = Lamp::whereIn('renew', [0, 1])->where('end_date', '>=', $now_day)->where('end_date', '<=', $one_month_day)->whereNull('close_date')->orderby('end_date', 'asc')->get();
            // 使用工作流程系統取得需要當前使用者審核的假單
            $leaves_datas = LeaveDay::where('state', 2)
                ->whereHas('checks', function($query) {
                    $query->where('check_user_id', Auth::user()->id)
                          ->where('state', 2); // 待審核狀態
                })->get();
            // dd($contract_datas);
            // $low_stock_products = [];

            // $products = Product::where('status', 'up')->get();
            // foreach ($products as $product) {
            //     // 動態取得目前庫存（用你已經寫過的邏輯或我之前幫你封裝的）
            //     $current_stock = app()->call('App\Http\Controllers\InventoryController@calculateCurrentStock', [
            //         'productId' => $product->id
            //     ]);

            //     if ($product->alarm_num !== null && $current_stock < $product->alarm_num) {
            //         $low_stock_products[] = "{$product->name}（剩 $current_stock.，.警戒值：{$product->alarm_num}）";
            //     }
            // }
            // dd($low_stock_products);
            // ->with('low_stock_products', $low_stock_products)
            return view('index')->with('now', $now)->with('work', $work)->with('contract_datas', $contract_datas)->with('lamp_datas', $lamp_datas)->with('tasks', $tasks)->with('users', $users)->with('leaves_datas', $leaves_datas);
        } else {
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
        } else {
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
        return redirect()->route('index');
    }

    //當月資訊
    public function index()
    {
        $now = Carbon::now()->locale('zh-tw');
        $today = Carbon::today();
        $firstDay = Carbon::now()->firstOfMonth();
        $lastDay = Carbon::now()->lastOfMonth();

        $sale_today = Sale::where('status', '9')->where('sale_date', $today->format("Y-m-d"))->whereIn('pay_id', ['A', 'C', 'E'])->count();
        $price = Sale::where('status', '9')->where('sale_date', $today->format("Y-m-d"))->sum('pay_price');

        //月營收
        $sale_month = Sale::where('status', '9')->where('sale_date', '>=', $firstDay->format("Y-m-d"))->where('sale_date', '<=', $lastDay->format("Y-m-d"))->sum('pay_price');
        $income_month = IncomeData::where('income_date', '>=', $firstDay->format("Y-m-d"))->where('income_date', '<=', $lastDay->format("Y-m-d"))->sum('price');
        $price_month = $sale_month + $income_month;

        // Sale_gdpaper::where('created_at','>=',$firstDay->format("Y-m-d"))->where('created_at','<=',$lastDay->format("Y-m-d"))->sum('gdpaper_total');

        //月支出
        $pay_month = PayItem::join('pay', 'pay_item.pay_id', '=', 'pay.id')
            ->where('pay_item.status', '1')
            ->where('pay_item.pay_date', '>=', $firstDay->format("Y-m-d"))
            ->where('pay_item.pay_date', '<=', $lastDay->format("Y-m-d"))
            ->where('pay.calculate', '!=', 1)
            ->select('pay_item.*') // 選擇 pay_item 的欄位
            ->sum('price');
        //營業淨利
        $net_income =  $price_month -  $pay_month;

        $income = IncomeData::where('income_date', $today->format("Y-m-d"))->sum('price');
        $total_today_incomes = intval($price) + intval($income);
        $check_sale = Sale::where('status', 3)->count();
        $cust_nums = Customer::count();
        $work = Works::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();

        //套裝：
        $suits = Suit::where('status', 'up')->whereNotIn('id', [1])->get();

        //專員看到的獎金統計
        //1.金紙（金紙的賣出總額）
        $gdpaper_month = DB::table('sale_data')
            ->join('sale_gdpaper', 'sale_gdpaper.sale_id', '=', 'sale_data.id')
            ->where('sale_data.sale_date', '>=', $firstDay->format("Y-m-d"))
            ->where('sale_data.sale_date', '<=', $lastDay->format("Y-m-d"))
            ->where('sale_data.status', '9')
            ->where('sale_data.type_list', 'dispatch')
            ->sum('sale_gdpaper.gdpaper_total');

        // //2.花樹葬（花樹葬的數量）
        $flower_month = DB::table('sale_data')
            ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
            ->where('sale_data.sale_date', '>=', $firstDay->format("Y-m-d"))
            ->where('sale_data.sale_date', '<=', $lastDay->format("Y-m-d"))
            ->where('sale_data.status', '9')
            ->where('sale_prom.prom_id', '15')
            ->whereNotNull('sale_prom.prom_id')
            ->where('sale_prom.prom_id', '<>', '')
            ->count();
        // //3.盆栽（盆栽的數量）
        $potted_plant_month = DB::table('sale_data')
            ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
            ->where('sale_data.sale_date', '>=', $firstDay->format("Y-m-d"))
            ->where('sale_data.sale_date', '<=', $lastDay->format("Y-m-d"))
            ->where('sale_data.status', '9')
            ->where('sale_prom.prom_id', '16')
            ->whereNotNull('sale_prom.prom_id')
            ->where('sale_prom.prom_id', '<>', '')
            ->count();

        //4.骨灰罐（骨灰罐的總額）
        $urn_month = DB::table('sale_data')
            ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
            ->where('sale_data.sale_date', '>=', $firstDay->format("Y-m-d"))
            ->where('sale_data.sale_date', '<=', $lastDay->format("Y-m-d"))
            ->where('sale_data.status', '9')
            ->where('sale_prom.prom_id', '14')
            ->whereNotNull('sale_prom.prom_id')
            ->where('sale_prom.prom_id', '<>', '')
            ->sum('sale_prom.prom_total');

        //5.指定款獎金（VVG骨罐、拍拍骨罐、玉罐、大理石罐、寵物花盅、VVG紀念品、指定款紀念品）加總
        $specify_month = DB::table('sale_data')
            ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
            ->where('sale_data.sale_date', '>=', $firstDay->format("Y-m-d"))
            ->where('sale_data.sale_date', '<=', $lastDay->format("Y-m-d"))
            ->where('sale_data.status', '9')
            ->whereIn('sale_prom.prom_id', [28, 31, 46, 47, 20, 24, 32])
            ->whereNotNull('sale_prom.prom_id')
            ->where('sale_prom.prom_id', '<>', '')
            ->sum('sale_prom.prom_total');

        //本月平安燈數量
        $lamp_month = DB::table('sale_data')
            ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
            ->where('sale_data.sale_date', '>=', $firstDay->format("Y-m-d"))
            ->where('sale_data.sale_date', '<=', $lastDay->format("Y-m-d"))
            ->where('sale_data.status', '9')
            ->whereIn('sale_prom.prom_id', [41, 42])
            ->whereNotNull('sale_prom.prom_id')
            ->where('sale_prom.prom_id', '<>', '')
            ->count();

        //本月美化數量
        $beautify_month = DB::table('sale_data')
            ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
            ->where('sale_data.sale_date', '>=', $firstDay->format("Y-m-d"))
            ->where('sale_data.sale_date', '<=', $lastDay->format("Y-m-d"))
            ->where('sale_data.status', '9')
            ->where('sale_prom.prom_id', 30)
            ->whereNotNull('sale_prom.prom_id')
            ->where('sale_prom.prom_id', '<>', '')
            ->count();

        // 取得本季起訖
        $start_season = $today->copy()->firstOfQuarter()->toDateString(); // e.g. '2025-04-01'
        $end_season   = $today->copy()->lastOfQuarter()->toDateString();  // e.g. '2025-06-30'

        //6.季獎金（火化套裝）
        $suit_seasons = [];
        foreach ($suits as $suit) {
            $suit_seasons[$suit->id]['name'] = $suit->name;
            $suit_seasons[$suit->id]['count'] = DB::table('sale_data')
                ->where('sale_data.sale_date', '>=', $start_season)
                ->where('sale_data.sale_date', '<=', $end_season)
                ->where('sale_data.status', '9')
                ->where('sale_data.suit_id', $suit->id)
                ->whereNotNull('sale_data.suit_id')
                // 排除空字串，如果是數值型別改用 ->where('sale_data.suit_id', '>', 0)
                ->where('sale_data.suit_id', '<>', '')
                ->count();
        }

        //7.季獎金（骨灰罐＋紀念品）
        $urn_souvenir_season = DB::table('sale_data')
            ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
            ->where('sale_data.sale_date', '>=', $start_season)
            ->where('sale_data.sale_date', '<=', $end_season)
            ->where('sale_data.status', '9')
            ->whereIn('sale_prom.prom_id', [14, 4])
            ->whereNotNull('sale_prom.prom_id')
            ->where('sale_prom.prom_id', '<>', '')
            ->sum('sale_prom.prom_total');


        // ➕ 新增庫存預警提醒邏輯



        if (Auth::user()->status != 1) {
            return view('dashboard')->with([
                'now' => $now,
                'work' => $work,
                'sale_today' => $sale_today,
                'cust_nums' => $cust_nums,
                'check_sale' => $check_sale,
                'total_today_incomes' => $total_today_incomes,
                'price_month' => $price_month,
                'pay_month' => $pay_month,
                'net_income' => $net_income,
                'gdpaper_month' => $gdpaper_month,
                'flower_month' => $flower_month,
                'potted_plant_month' => $potted_plant_month,
                'urn_month' => $urn_month,
                'specify_month' => $specify_month,
                'suit_seasons' => $suit_seasons,
                'urn_souvenir_season' => $urn_souvenir_season,
                'lamp_month' => $lamp_month,
                'beautify_month' => $beautify_month,
            ]);
        } else {
            return view('auth.login');
        }
    }

    public function sale_index()
    {

        $now = Carbon::now()->locale('zh-tw');
        $today = Carbon::today();
        $firstDay = Carbon::now()->firstOfMonth();
        $lastDay = Carbon::now()->lastOfMonth();

        //套裝：
        $suits = Suit::where('status', 'up')->whereNotIn('id', [1])->get();

        //專員看到的獎金統計
        //1.金紙（金紙的賣出總額）
        $gdpaper_month = DB::table('sale_data')
            ->join('sale_gdpaper', 'sale_gdpaper.sale_id', '=', 'sale_data.id')
            ->where('sale_data.sale_date', '>=', $firstDay->format("Y-m-d"))
            ->where('sale_data.sale_date', '<=', $lastDay->format("Y-m-d"))
            ->where('sale_data.status', '9')
            ->where('sale_data.type_list', 'dispatch')
            ->sum('sale_gdpaper.gdpaper_total');

        // //2.花樹葬（花樹葬的數量）
        $flower_month = DB::table('sale_data')
            ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
            ->where('sale_data.sale_date', '>=', $firstDay->format("Y-m-d"))
            ->where('sale_data.sale_date', '<=', $lastDay->format("Y-m-d"))
            ->where('sale_data.status', '9')
            ->where('sale_prom.prom_id', '15')
            ->whereNotNull('sale_prom.prom_id')
            ->where('sale_prom.prom_id', '<>', '')
            ->count();
        // //3.盆栽（盆栽的數量）
        $potted_plant_month = DB::table('sale_data')
            ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
            ->where('sale_data.sale_date', '>=', $firstDay->format("Y-m-d"))
            ->where('sale_data.sale_date', '<=', $lastDay->format("Y-m-d"))
            ->where('sale_data.status', '9')
            ->where('sale_prom.prom_id', '16')
            ->whereNotNull('sale_prom.prom_id')
            ->where('sale_prom.prom_id', '<>', '')
            ->count();

        //4.骨灰罐（骨灰罐的總額）
        $urn_month = DB::table('sale_data')
            ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
            ->where('sale_data.sale_date', '>=', $firstDay->format("Y-m-d"))
            ->where('sale_data.sale_date', '<=', $lastDay->format("Y-m-d"))
            ->where('sale_data.status', '9')
            ->where('sale_prom.prom_id', '14')
            ->whereNotNull('sale_prom.prom_id')
            ->where('sale_prom.prom_id', '<>', '')
            ->sum('sale_prom.prom_total');

        // dd($flower_month);

        //5.指定款獎金（VVG+拍拍+寵物花忠+vvg紀念品+指定款紀念品+[玉罐+大理石罐]）加總
        $specify_month = DB::table('sale_data')
            ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
            ->where('sale_data.sale_date', '>=', $firstDay->format("Y-m-d"))
            ->where('sale_data.sale_date', '<=', $lastDay->format("Y-m-d"))
            ->where('sale_data.status', '9')
            ->whereIn('sale_prom.prom_id', [28, 31, 46, 47, 20, 24, 32])
            ->whereNotNull('sale_prom.prom_id')
            ->where('sale_prom.prom_id', '<>', '')
            ->sum('sale_prom.prom_total');

        //本月平安燈數量
        $lamp_month = DB::table('sale_data')
            ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
            ->where('sale_data.sale_date', '>=', $firstDay->format("Y-m-d"))
            ->where('sale_data.sale_date', '<=', $lastDay->format("Y-m-d"))
            ->where('sale_data.status', '9')
            ->whereIn('sale_prom.prom_id', [41, 42])
            ->whereNotNull('sale_prom.prom_id')
            ->where('sale_prom.prom_id', '<>', '')
            ->count();

        //本月美化數量
        $beautify_month = DB::table('sale_data')
            ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
            ->where('sale_data.sale_date', '>=', $firstDay->format("Y-m-d"))
            ->where('sale_data.sale_date', '<=', $lastDay->format("Y-m-d"))
            ->where('sale_data.status', '9')
            ->where('sale_prom.prom_id', 30)
            ->whereNotNull('sale_prom.prom_id')
            ->where('sale_prom.prom_id', '<>', '')
            ->count();

        // 取得本季起訖
        $start_season = $today->copy()->firstOfQuarter()->toDateString(); // e.g. '2025-04-01'
        $end_season   = $today->copy()->lastOfQuarter()->toDateString();  // e.g. '2025-06-30'

        //6.季獎金（火化套裝）
        $suit_seasons = [];
        foreach ($suits as $suit) {
            $suit_seasons[$suit->id]['name'] = $suit->name;
            $suit_seasons[$suit->id]['count'] = DB::table('sale_data')
                ->where('sale_data.sale_date', '>=', $start_season)
                ->where('sale_data.sale_date', '<=', $end_season)
                ->where('sale_data.status', '9')
                ->where('sale_data.suit_id', $suit->id)
                ->whereNotNull('sale_data.suit_id')
                // 排除空字串，如果是數值型別改用 ->where('sale_data.suit_id', '>', 0)
                ->where('sale_data.suit_id', '<>', '')
                ->count();
        }

        //7.季獎金（骨灰罐＋紀念品）
        $urn_souvenir_season = DB::table('sale_data')
            ->join('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
            ->where('sale_data.sale_date', '>=', $start_season)
            ->where('sale_data.sale_date', '<=', $end_season)
            ->where('sale_data.status', '9')
            ->whereIn('sale_prom.prom_id', [14, 4])
            ->whereNotNull('sale_prom.prom_id')
            ->where('sale_prom.prom_id', '<>', '')
            ->sum('sale_prom.prom_total');

        return view('sale_dashboard')->with([
            'now' => $now,
            'gdpaper_month' => $gdpaper_month,
            'flower_month' => $flower_month,
            'potted_plant_month' => $potted_plant_month,
            'urn_month' => $urn_month,
            'specify_month' => $specify_month,
            'suit_seasons' => $suit_seasons,
            'lamp_month' => $lamp_month,
            'beautify_month' => $beautify_month,
            'urn_souvenir_season' => $urn_souvenir_season
        ]);
    }
}

//達標管理
// $jobId = strval(Auth::user()->job_id); // 確保它是字串

// $targetDatas = TargetData::join('target_item', 'target_data.id', '=', 'target_item.target_data_id')
//                            ->where('target_item.start_date','>=',$firstDay->format("Y-m-d"))
//                            ->where('target_item.end_date','<=',$lastDay->format("Y-m-d"))
//                            ->whereJsonContains('target_data.job_id', $jobId)->get();
// foreach($targetDatas as $targetData)
// {
//     if($targetData->category_id == 1){//金紙銷售
//         $targetData->manual_achieved =DB::table('sale_data')
//                                         ->join('sale_gdpaper','sale_gdpaper.sale_id', '=' , 'sale_data.id')
//                                         ->where('sale_data.status','9')
//                                         ->where('sale_data.sale_date','>=',$firstDay->format("Y-m-d"))
//                                         ->where('sale_data.sale_date','<=',$lastDay->format("Y-m-d"))
//                                         ->sum('sale_gdpaper.gdpaper_total');
        
//     }
//     if($targetData->target_condition == "金額"){
        
//     }

//     if($targetData->target_condition == "數量"){
        
//     }

//     if($targetData->target_condition == "金額+數量"){
        
//     }
//     $targetData->percent = $targetData->target_amount == 0 ? 0 : round( intval($targetData->manual_achieved) / intval($targetData->target_amount)* 100, 2);
// }

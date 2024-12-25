<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserLog;
use App\Models\Debit;
use App\Models\Customer;
use App\Models\Sale_gdpaper;
use App\Models\LeaveDay;
use App\Models\Sale;
use App\Models\PayData;
use App\Models\PayItem;
use App\Models\Pay;
use App\Models\Product;
use App\Models\CustGroup;
use App\Models\LeaveDayCheck;
use App\Models\SaleCompanyCommission;
use App\Models\GdpaperInventoryData;
use App\Models\Leaves;
use App\Models\GdpaperInventoryItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

class PersonController extends Controller
{
    public function sale_statistics(Request $request)
    {
        $years = range(Carbon::now()->year, 2022);
        // 獲取當前年份
        
        if(isset($request->year))
        {
            $currentYear = $request->year;
        }else{
            $currentYear = Carbon::now()->year;
        }
        // 初始化一個陣列來存儲結果
        $months = [];

        // 定義月份名稱的陣列
        $monthNames = [
            '一月', '二月', '三月', '四月', '五月', '六月',
            '七月', '八月', '九月', '十月', '十一月', '十二月'
        ];

        // 循環每個月
        for ($month = 1; $month <= 12; $month++) {
            // 獲取該月的第一天
            $startOfMonth = Carbon::create($currentYear, $month, 1)->startOfMonth();
            
            // 獲取該月的最後一天
            $endOfMonth = $startOfMonth->copy()->endOfMonth();
            
            // 將結果存儲到陣列中
            $months[] = [
                'monthName' => $monthNames[$month - 1], // 從$monthNames陣列中獲取國字月份名稱
                'start' => $startOfMonth->toDateString(),
                'end' => $endOfMonth->toDateString(),
            ];
        }
        
        $datas = [];

        $user = User::where('id', Auth::user()->id)->first();

            $datas[$user->id] = [];
            $datas[$user->id]['name'] = $user->name;
            foreach($months as $key=>$month) {
                $datas[$user->id]['months'][$key]['plan_1'] = Sale::where('user_id',$user->id)->where('plan_id',1)->where('sale_date', '>=' ,$month['start'])->where('sale_date', '<=' ,$month['end'])->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();
                $datas[$user->id]['months'][$key]['plan_2'] = Sale::where('user_id',$user->id)->where('plan_id',2)->where('sale_date', '>=' ,$month['start'])->where('sale_date', '<=' ,$month['end'])->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();
                $datas[$user->id]['months'][$key]['plan_3'] = Sale::where('user_id',$user->id)->where('plan_id',3)->where('sale_date', '>=' ,$month['start'])->where('sale_date', '<=' ,$month['end'])->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();
            }
            $datas[$user->id]['total_1'] = Sale::where('user_id',$user->id)->where('plan_id',1)->where('sale_date', '>=' ,$currentYear.'-01-01')->where('sale_date', '<=' ,$currentYear.'-12-31')->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();
            $datas[$user->id]['total_2'] = Sale::where('user_id',$user->id)->where('plan_id',2)->where('sale_date', '>=' ,$currentYear.'-01-01')->where('sale_date', '<=' ,$currentYear.'-12-31')->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();
            $datas[$user->id]['total_3'] = Sale::where('user_id',$user->id)->where('plan_id',3)->where('sale_date', '>=' ,$currentYear.'-01-01')->where('sale_date', '<=' ,$currentYear.'-12-31')->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();

        // dd($datas);
        
        return view('person.sale_statistics')->with('datas',$datas)->with('years',$years)->with('months',$months)->with('request',$request);
    }
    public function __construct()
    {
        // $this->middleware('auth')->redirect()->route('login');
    }

    public function show()
    {
        $json = file_get_contents(public_path('assets/data/banks.json'));
        $banks = collect(json_decode($json, true));
        $groupedBanks = $banks->groupBy('銀行代號/總機構代碼');
        $user = User::where('id', Auth::user()->id)->first();
        return view('person.edit-profile')->with('user', $user)
                                       ->with('hint', '0')->with('groupedBanks', $groupedBanks);
    }

    public function update(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->first();
        //取得舊資料，要寫入log中
        $old_name = $user->name;
        $old_mobile = $user->mobile;
        $old_email = $user->email;
        $old_address = $user->address;

        if(Auth::user()->level == 0 && Auth::user()->level == 1){
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->email = $request->email;
            $user->address = $request->address;
            $user->entry_date = $request->entry_date;
            $user->level = $request->level;
            $user->status = $request->status;
            $user->bank = $request->bank; //銀行
            $user->branch = $request->branch; //銀行分行
            $user->save();
        }else{
            //不是管理員的話，要紀錄到log中
            $user_log = new UserLog();
            $user_log->type = 'edit';
            $user_log->user_id = $user->id;
            $user_log->title = ' ';
            $user_log->text = ' ';
            if ($old_name != $request->name) {
                $user_log->title .= '姓名' . "*";
                $user_log->text .= $old_name . "→" . $request->name . "*";
            }
            if ($old_email != $request->email) {
                $user_log->title .= '信箱' . "*";
                $user_log->text .= $old_email . "→" . $request->email . "*";
            }
            if ($old_mobile != $request->mobile) {
                $user_log->title .= '電話' . "*";
                $user_log->text .= $old_mobile . "→" . $request->mobile . "*";
            }
            if ($old_address != $request->address) {
                $user_log->title .= '地址' . "*";
                $user_log->text .= $old_address . "→" . $request->address . "*";
            }
            $user_log->Update_at = Auth::user()->id;
            $user_log->save();
            

            $user->name = $request->name;
            $user->sex = $request->sex;
            $user->birthday = $request->birthday;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
           
            $user->ic_card = $request->ic_card;
            $user->marriage = $request->marriage;
            $user->address = $request->address;
            $user->census_address = $request->census_address;
            $user->bank_id = $request->bank_id;
            $user->bank = $request->bank; //銀行
            $user->branch = $request->branch; //銀行分行
            $user->bank_number = $request->bank_number;
            $user->urgent_name = $request->urgent_name;
            $user->urgent_relation = $request->urgent_relation;
            $user->urgent_mobile = $request->urgent_mobile;
            $user->education_school = $request->education_school;
            $user->education_level = $request->education_level;
            $user->is_graduated = $request->is_graduated;
            $user->state = 0; //用戶只能修改第一次,第一次修改後 只能透過人資去修改，所以狀態是0
            $user->save();
        }
        // dd($request->all());

        $json = file_get_contents(public_path('assets/data/banks.json'));
        $banks = collect(json_decode($json, true));
        $groupedBanks = $banks->groupBy('銀行代號/總機構代碼');
        return view('person.edit-profile')->with('user', $user)
                                        ->with('hint','1')->with('groupedBanks', $groupedBanks);
    }

     //員工送出借出。補錢單
     public function debit_create()
     {
         return view('debit.new_debit')->with('hint', '0');
     }
 
     public function debit_store(Request $request)
     {
         $data = new Debit();
         $data->user_id = Auth::user()->id;
         $data->type = $request->type;
         $data->price = $request->price;
         $data->state = $request->type;
         $data->comment = $request->comment;
         $data->save();
         return redirect()->route('new-debit');
     }

    //個人支出
    public function pay_index(Request $request)
    {
        $pays = Pay::orderby('seq','asc')->get();
        if($request){
            
            $status = $request->status;
            if ($status) {
                $datas = PayData::where('status',  $status)->where('user_id', Auth::user()->id);
                $sum_pay = PayData::where('status', $status)->where('user_id', Auth::user()->id);
            }else{
                $datas = PayData::where('status', 0)->where('user_id', Auth::user()->id);
                $sum_pay = PayData::where('status', 0)->where('user_id', Auth::user()->id);
            }
            $after_date = $request->after_date;
            if ($after_date) {
                $datas =  $datas->where('pay_date', '>=', $after_date);
                $sum_pay  = $sum_pay->where('pay_date', '>=', $after_date);
            }
            $before_date = $request->before_date;
            if ($before_date) {
                $datas =  $datas->where('pay_date', '<=', $before_date);
                $sum_pay  = $sum_pay->where('pay_date', '<=', $before_date);
            }
            if($after_date && $before_date){
                $datas =  $datas->where('pay_date', '>=', $after_date)->where('pay_date', '<=', $before_date);
                $sum_pay  = $sum_pay->where('pay_date', '>=', $after_date)->where('pay_date', '<=', $before_date);
            }
            $pay = $request->pay;
            if ($pay != "null") {
                if (isset($pay)) {
                    $datas =  $datas->where('pay_id', $pay);
                    $sum_pay  = $sum_pay->where('pay_id', $pay);
                } else {
                    $datas = $datas;
                    $sum_pay  = $sum_pay;
                }
            }
            $sum_pay  = $sum_pay->sum('price');
            $datas = $datas->orderby('pay_date','desc')->paginate(50);
            $condition = $request->all();
        }else{
            $datas = PayData::orderby('pay_date','desc')->paginate(50);
            $sum_pay  = PayData::sum('price');
            $condition = '';
        }
        return view('person.pays')->with('datas',$datas)->with('request',$request)->with('pays',$pays)->with('condition',$condition)
                                     ->with('sum_pay',$sum_pay);
    }

     //員工業務
     public function sale_index(Request $request)
     {
            if ($request) {
                $status = $request->status;
                if (!isset($status) || $status == 'not_check') {
                    $sales = Sale::where('user_id', Auth::user()->id)->whereIn('status', [1, 2]);
                }
                if ($status == 'check') {
                    $sales = Sale::where('user_id', Auth::user()->id)->whereIn('status', [9,100]);
                }
                $after_date = $request->after_date;
                if ($after_date) {
                    $sales = $sales->where('sale_date', '>=', $after_date);
                }
                $before_date = $request->before_date;
                if ($before_date) {
                    $sales = $sales->where('sale_date', '<=', $before_date);
                }
                $sale_on = $request->sale_on;
                if ($sale_on) {
                    $sales = $sales->where('sale_on', $sale_on);
                }
                $cust_mobile = $request->cust_mobile;
                if ($cust_mobile) {
                    $customer = Customer::where('mobile', $cust_mobile)->first();
                    $sales = $sales->where('customer_id', $customer->id);
                }
                $plan = $request->plan;
                if ($plan != "null") {
                    if (isset($plan)) {
                        $sales = $sales->where('plan_id', $plan);
                    } else {
                        $sales = $sales;
                    }
                }

                $pet_name = $request->pet_name;
                if ($pet_name) {
                    $pet_name = $request->pet_name.'%';
                    $sales = $sales->where('pet_name', 'like' ,$pet_name);
                }

                $pay_id = $request->pay_id;
                if ($pay_id) {
                    if($pay_id == 'A'){
                        $sales = $sales->whereIn('pay_id', ['A','B']);
                    }else{
                        $sales = $sales->where('pay_id', $pay_id);
                    }
                }
                $sales = $sales->orderby('sale_date', 'desc')->paginate(50);
                $price_total = $sales->sum('pay_price');
                $condition = $request->all();

                foreach ($sales as $sale) {
                    $sale_ids[] = $sale->id;
                }
                if (isset($sale_ids)) {
                    $gdpaper_total = Sale_gdpaper::whereIn('sale_id', $sale_ids)->sum('gdpaper_total');
                } else {
                    $gdpaper_total = 0;
                }
            } else {
                $condition = ' ';
                $price_total = Sale::where('status', '1')->sum('pay_price');
                $sales = Sale::orderby('sale_date', 'desc')->where('status', '1')->paginate(15);
            }
            return view('person.sales')->with('sales', $sales)
                                       ->with('request', $request)
                                       ->with('condition', $condition)
                                       ->with('price_total', $price_total)
                                       ->with('gdpaper_total', $gdpaper_total);
     }

     public function wait_sale_index(Request $request)
     {
        $sales = Sale::where('status', 3)->where('user_id', Auth::user()->id)->orderby('sale_date', 'desc')->get();
        return view('person.wait')->with('sales', $sales);
     }

     public function leave_index(Request $request)
     {

        $leaves= Leaves::where('status',0)->orderby('seq')->get();
        $datas = LeaveDay::orderby('created_at','desc')->where('user_id',Auth::user()->id)->orderby('created_at');
        if($request)
        {
            $state = $request->state;
            if($state){
                $datas = $datas->where('state',$state);
            }else{
                $datas = $datas->where('state',1);
            }
            $start_date_start = $request->start_date_start;
            if($start_date_start){
                $start_date_start = $request->start_date_start.' 00:00:00';
                $datas = $datas->where('start_datetime','>=',$start_date_start);
            }
            $start_date_end = $request->start_date_end;
            if($start_date_end){
                $start_date_end = $request->start_date_end.' 11:59:59';
                $datas = $datas->where('start_datetime','<=',$start_date_end);
            }
            $end_date_start = $request->end_date_start;
            if($end_date_start){
                $end_date_start = $request->end_date_start.' 00:00:00';
                $datas = $datas->where('end_datetime','>=',$end_date_start);
            }
            $end_date_end = $request->end_date_end;
            if($end_date_end){
                $end_date_end = $request->end_date_end.' 11:59:59';
                $datas = $datas->where('end_datetime','<=',$end_date_end);
            }
            $leave_day = $request->leave_day;
            if ($leave_day != "null") {
                if (isset($leave_day)) {
                    $datas = $datas->where('leave_day', $leave_day);
                } else {
                    $datas = $datas;
                }
            }
            $condition = $condition = $request->all();
            $datas = $datas->paginate(50);
        }else{
            $datas = $datas->paginate(50);
            $condition = '';
        }

        $condition = $condition = $request->all();


        
        // dd($leave_datas);
        return view('person.leave_days')->with('datas', $datas)->with('request', $request)->with('condition',$condition)->with('leaves',$leaves);
     }

     public function leave_check_show($id)
     {
         $data = LeaveDay::where('id', $id)->first();
         $items = LeaveDayCheck::where('leave_day_id',$data->id)->get();
         $leaves= Leaves::where('status',0)->orderby('seq')->get();
         return view('person.leave_check')->with('data', $data)->with('items',$items)->with('leaves',$leaves);
     }

    public function leave_check_update($id ,Request $request)
    {

        $data = LeaveDay::where('id', $id)->first();
        $data->state = 2;
        $data->save();

        
        $leave_data = LeaveDay::orderby('id','desc')->first();
        $item = new LeaveDayCheck;
        $item->leave_day_id = $leave_data->id;
        $item->check_day = Carbon::now()->locale('zh-tw')->format('Y-m-d');
        $item->state = 2;
        $item->check_user_id = Auth::user()->id;
        $item->created_at = Carbon::now()->locale('zh-tw');
        $item->updated_at = Carbon::now()->locale('zh-tw');
        $item->save();

        return redirect()->route('person.leave_days');
    }

    public function last_leave_days()
    {
        //計算假別天數
        $year = Carbon::now()->year;//取得當年
        $now_year = Carbon::now()->year;//取當年計算特休的年度
        $user = User::where('id',Auth::user()->id)->first();
        $leave_datas = [];
        $leaves= DB::table('leaves')
                    ->join('leave_setting','leave_setting.leave_id', '=' , 'leaves.id')
                    ->whereIn('leaves.id',[1])
                    ->where('leaves.status',0)
                    ->where('leave_setting.year' , '=' , $year)
                    ->select('leaves.*' , 'leave_setting.approved_days as day')
                    ->get();
        $dates = [];
        foreach($leaves as $leave){
            $dates[$leave->id]['name'] = $leave->name;
            $dates[$leave->id]['day'] = $leave->day;
            $dates[$leave->id]['hour'] = intval($leave->day) * 8;
        }
        // dd($leaves);

        //每人的特休天數
        $dates['1']['user_day'][$user->id]['day'] = $this->specil_vacation($user->entry_date);
        $dates['1']['user_day'][$user->id]['hour'] = intval($this->specil_vacation($user->entry_date))*8;

        $leave_datas[$user->id]['name'] = $user->name;
        $leave_datas[$user->id]['year'] = $year;
        foreach($dates as $leave_day=>$date)
        {
            if($leave_day == 1){
                $leave_datas[$user->id]['leavedays'][$leave_day]['datas'] = LeaveDay::where('state','9')->where('start_datetime','>=','2020-01-01 00:00:00')->where('end_datetime','<=',$now_year.'-12-31 11:59:59')->where('leave_day',$leave_day)->where('user_id', $user->id)->get();
            }else{
                $leave_datas[$user->id]['leavedays'][$leave_day]['datas'] = LeaveDay::where('state','9')->where('start_datetime','>=',$year.'-01-01 00:00:00')->where('end_datetime','<=',$year.'-12-31 11:59:59')->where('leave_day',$leave_day)->where('user_id', $user->id)->get();
            }
            $leave_datas[$user->id]['leavedays'][$leave_day]['hour'] = 0;
            //剩餘天數
            $leave_datas[$user->id]['leavedays'][$leave_day]['day'] = 0; 
            //累積天數
            $leave_datas[$user->id]['leavedays'][$leave_day]['add_day'] = 0; 
        }
        
        foreach ($leave_datas as &$data) {
            foreach($data['leavedays'] as &$leave_days)
            {
                if(count($leave_days['datas']) >= 0)
                {
                    foreach($leave_days['datas'] as $key=>$leave_data)
                    {
                        // dd($leave_data->unit);
                        if($leave_data->unit == "day"){
                            $leave_days['hour'] += intval($leave_data->total)*8;
                        }else{
                            $leave_days['hour'] += intval($leave_data->total);
                        }
                    }
                }
            }
        }

        // dd($leave_datas);

        // 創建一個基準時間點的 Carbon 實例（例如：現在）
        $baseTime = Carbon::now();

        foreach($leave_datas as $user_id=>&$data)
        {
            foreach($data['leavedays'] as $leaveday_type=>&$leave_days)
            {
                if($leaveday_type == '1'){
                    //如果是特休，要重新寫條鍵;
                    if ($dates['1']['user_day'][$user_id]['hour'] < $leave_datas[$user_id]['leavedays']['1']['hour']) {
                        // Set hours to 0 if requested hours are less
                        $timeAfterHours = $baseTime->copy(); // No hours added
                        $add_timeAfterHours = $baseTime->copy(); // No hours added for cumulative calculation
                    } else {
                        //如果是特休，要重新寫條鍵;
                        $timeAfterHours = $baseTime->copy()->addHours($dates['1']['user_day'][$user_id]['hour'] - $leave_datas[$user_id]['leavedays']['1']['hour']);
                        //累積天數
                        $add_timeAfterHours = $baseTime->copy()->addHours($leave_datas[$user_id]['leavedays']['1']['hour']);
                    }
                    // 計算天數差異
                    $hoursDifference = $timeAfterHours->diffInHours($baseTime);
                    $daysBasedOn8Hours = intdiv($hoursDifference, 8);
                    // 計算剩餘小時數
                    $remainingHours = $hoursDifference % 8;
                    //剩餘天數
                    if($leave_datas[$user_id]['leavedays']['1']['hour'] > 0){
                        $leave_datas[$user_id]['leavedays']['1']['day'] = $daysBasedOn8Hours . "天，又" . $remainingHours . "小時"; 
                    }else{
                        $leave_datas[$user_id]['leavedays']['1']['day'] = $daysBasedOn8Hours . "天"; 
                    }

                    //累積天數
                    $add_timeAfterHours = $baseTime->copy()->addHours($leave_datas[$user_id]['leavedays']['1']['hour']);
                    // 計算天數差異
                    $add_hoursDifference = $add_timeAfterHours->diffInHours($baseTime);
                    $add_daysBasedOn8Hours = intdiv($add_hoursDifference, 8);
                    // 計算剩餘小時數
                    $add_remainingHours = $add_hoursDifference % 8;
                    if($leave_datas[$user_id]['leavedays']['1']['hour'] > 0){
                        $leave_datas[$user_id]['leavedays']['1']['add_day'] = $add_daysBasedOn8Hours . "天，又" . $add_remainingHours . "小時"; 
                    }else{
                        $leave_datas[$user_id]['leavedays']['1']['add_day'] = $add_remainingHours . "小時"; 
                    }
                }else{
                    if($dates[$leaveday_type]['hour'] < $leave_datas[$user_id]['leavedays'][$leaveday_type]['hour']){
                        $timeAfterHours = $baseTime->copy();
                    }else{
                        $timeAfterHours = $baseTime->copy()->addHours($dates[$leaveday_type]['hour'] - $leave_datas[$user_id]['leavedays'][$leaveday_type]['hour']);
                    }
                    // 計算天數差異
                    $hoursDifference = $timeAfterHours->diffInHours($baseTime);
                    $daysBasedOn8Hours = intdiv($hoursDifference, 8);
                    // 計算剩餘小時數
                    $remainingHours = $hoursDifference % 8;
                    //剩餘天數
                    if($leave_datas[$user_id]['leavedays'][$leaveday_type]['hour'] > 0){
                        $leave_datas[$user_id]['leavedays'][$leaveday_type]['day'] = $daysBasedOn8Hours . "天，又" . $remainingHours . "小時"; 
                    }else{
                        $leave_datas[$user_id]['leavedays'][$leaveday_type]['day'] = $daysBasedOn8Hours . "天"; 
                    }

                    //累積天數
                    $add_timeAfterHours = $baseTime->copy()->addHours($leave_datas[$user_id]['leavedays'][$leaveday_type]['hour']);
                    // 計算天數差異
                    $add_hoursDifference = $add_timeAfterHours->diffInHours($baseTime);
                    $add_daysBasedOn8Hours = intdiv($add_hoursDifference, 8);
                    // 計算剩餘小時數
                    $add_remainingHours = $add_hoursDifference % 8;
                    if($leave_datas[$user_id]['leavedays'][$leaveday_type]['hour'] > 8){
                        $leave_datas[$user_id]['leavedays'][$leaveday_type]['add_day'] = $add_daysBasedOn8Hours . "天，又" . $add_remainingHours . "小時"; 
                    }else{
                        $leave_datas[$user_id]['leavedays'][$leaveday_type]['add_day'] = $add_remainingHours . "小時"; 
                    }
                }
            }
        }

        return view('person.last_leave_days')->with('dates',$dates)->with('leave_datas', $leave_datas);
    }


    //員工盤點
    public function person_inventory(Request $request)
    {
        $datas = GdpaperInventoryData::where('update_user_id',Auth::user()->id);
        if($request)
        {
          $after_date = $request->after_date;
          if($after_date){
            $datas = $datas->where('date','>=',$after_date);
          }
          $before_date = $request->before_date;
          if($before_date){
            $datas = $datas->where('date','<=',$before_date);
          }
          $state = $request->state;
          if (isset($state)) {
              $datas = $datas->where('state', $state);
          } else {
              $datas = $datas->where('state', '0');
          }
          $datas = $datas->get();
        }else{
          $datas = $datas->get();
        }
        return view('person.inventorys')->with('datas',$datas)->with('request',$request);
    }

    private function specil_vacation($user_entry_date)
    {
        if($user_entry_date!=null){
            $today = date('Y-m-d', strtotime(Carbon::now()->locale('zh-tw')));
            $startDate = Carbon::parse($user_entry_date); // 將起始日期字串轉換為 Carbon 日期物件
            $endDate = Carbon::parse($today); // 將結束日期字串轉換為 Carbon 日期物件
            $diffDays = $startDate->diffInDays($endDate);// 計算年數差距
            $diffYears = $diffDays / 365;
            $diffYears = round($diffYears,2);
        }else{
            $diffYears = 0;
        }

        $specil_day = '';
        //特休條件
        if($diffYears < 0.5){ //小於半年
            $specil_day = 0;
        }elseif($diffYears >= 0.5 && $diffYears < 1){ //大於半年小於一年
            $specil_day = 3;
        }elseif($diffYears >= 1 && $diffYears < 2){//大於一年小於兩年
            $specil_day = 7;
        }elseif($diffYears >= 2 && $diffYears < 4){//大於一年小於兩年
            $specil_day = 14;
        }elseif($diffYears >= 4 && $diffYears < 10){//大於四年小於十年
            $specil_day = 15;
        }elseif($diffYears >= 10 && $diffYears < 11){//大於十年小於十一年
            $specil_day = 16;
        }elseif($diffYears >= 11 && $diffYears < 12){//大於十一年小於十二年
            $specil_day = 17;
        }elseif($diffYears >= 12 && $diffYears < 13){//大於十二年小於十三年
            $specil_day = 18;
        }elseif($diffYears >= 13 && $diffYears < 14){//大於十三年小於十四年
            $specil_day = 19;
        }elseif($diffYears >= 14 && $diffYears < 15){//大於十四年小於十五年
            $specil_day = 20;
        }elseif($diffYears >= 15 && $diffYears < 16){//大於十五年小於十六年
            $specil_day = 21;
        }elseif($diffYears >= 16 && $diffYears < 17){//大於十六年小於十七年
            $specil_day = 22;
        }elseif($diffYears >= 17 && $diffYears < 18){//大於十七年小於十八年
            $specil_day = 23;
        }elseif($diffYears >= 18 && $diffYears < 19){//大於十八年小於十九年
            $specil_day = 24;
        }elseif($diffYears >= 19 && $diffYears < 20){//大於十九年小於二十年
            $specil_day = 25;
        }elseif($diffYears >= 20 && $diffYears < 21){//大於二十年小於二一年
            $specil_day = 26;
        }elseif($diffYears >= 21 && $diffYears < 22){//大於二一年小於二二年
            $specil_day = 27;
        }elseif($diffYears >= 22 && $diffYears < 23){//大於二二年小於二三年
            $specil_day = 28;
        }elseif($diffYears >= 23 && $diffYears < 24){//大於二三年小於二四年
            $specil_day = 29;
        }elseif($diffYears >= 24 && $diffYears < 25){//大於二四年小於二伍年
            $specil_day = 30;
        }elseif($diffYears >= 25){//大於二伍年
            $specil_day = 30;
        }


        // dd($today);
        return $specil_day;
    }

}

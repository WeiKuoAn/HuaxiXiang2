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
use App\Services\LeaveWorkflowService;
use App\Models\GdpaperInventoryData;
use App\Models\Leaves;
use App\Models\GdpaperInventoryItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use App\Models\Workflow;

class PersonController extends Controller
{
    public function sale_statistics(Request $request)
    {
        $years = range(Carbon::now()->year, 2022);
        // 獲取當前年份

        if (isset($request->year)) {
            $currentYear = $request->year;
        } else {
            $currentYear = Carbon::now()->year;
        }
        // 初始化一個陣列來存儲結果
        $months = [];

        // 定義月份名稱的陣列
        $monthNames = [
            '一月',
            '二月',
            '三月',
            '四月',
            '五月',
            '六月',
            '七月',
            '八月',
            '九月',
            '十月',
            '十一月',
            '十二月'
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
        foreach ($months as $key => $month) {
            $datas[$user->id]['months'][$key]['plan_1'] = Sale::where('user_id', $user->id)->where('plan_id', 1)->where('sale_date', '>=', $month['start'])->where('sale_date', '<=', $month['end'])->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();
            $datas[$user->id]['months'][$key]['plan_2'] = Sale::where('user_id', $user->id)->where('plan_id', 2)->where('sale_date', '>=', $month['start'])->where('sale_date', '<=', $month['end'])->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();
            $datas[$user->id]['months'][$key]['plan_3'] = Sale::where('user_id', $user->id)->where('plan_id', 3)->where('sale_date', '>=', $month['start'])->where('sale_date', '<=', $month['end'])->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();
        }
        $datas[$user->id]['total_1'] = Sale::where('user_id', $user->id)->where('plan_id', 1)->where('sale_date', '>=', $currentYear . '-01-01')->where('sale_date', '<=', $currentYear . '-12-31')->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();
        $datas[$user->id]['total_2'] = Sale::where('user_id', $user->id)->where('plan_id', 2)->where('sale_date', '>=', $currentYear . '-01-01')->where('sale_date', '<=', $currentYear . '-12-31')->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();
        $datas[$user->id]['total_3'] = Sale::where('user_id', $user->id)->where('plan_id', 3)->where('sale_date', '>=', $currentYear . '-01-01')->where('sale_date', '<=', $currentYear . '-12-31')->where('status', '9')->whereIn('pay_id', ['A', 'C'])->count();

        // dd($datas);

        return view('person.sale_statistics')->with('datas', $datas)->with('years', $years)->with('months', $months)->with('request', $request);
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

        if (Auth::user()->level == 0 && Auth::user()->level == 1) {
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
        } else {
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
            ->with('hint', '1')->with('groupedBanks', $groupedBanks);
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
        $pays = Pay::orderby('seq', 'asc')->get();
        if ($request) {

            $status = $request->status;
            if ($status !== null && $status !== '') {
                $datas = PayData::where('status',  $status)->where('user_id', Auth::user()->id);
                $sum_pay = PayData::where('status', $status)->where('user_id', Auth::user()->id);
            } else {
                // 預設顯示未審核(status=0)和退單(status=2)的支出
                $datas = PayData::whereIn('status', [0, 2])->where('user_id', Auth::user()->id);
                $sum_pay = PayData::whereIn('status', [0, 2])->where('user_id', Auth::user()->id);
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
            if ($after_date && $before_date) {
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
            $datas = $datas->orderby('pay_date', 'desc')->paginate(50);
            $condition = $request->all();
        } else {
            $datas = PayData::orderby('pay_date', 'desc')->paginate(50);
            $sum_pay  = PayData::sum('price');
            $condition = '';
        }
        return view('person.pays')->with('datas', $datas)->with('request', $request)->with('pays', $pays)->with('condition', $condition)
            ->with('sum_pay', $sum_pay);
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
                $sales = Sale::where('user_id', Auth::user()->id)->whereIn('status', [9, 100]);
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
                $pet_name = $request->pet_name . '%';
                $sales = $sales->where('pet_name', 'like', $pet_name);
            }

            $pay_id = $request->pay_id;
            if ($pay_id) {
                if ($pay_id == 'A') {
                    $sales = $sales->whereIn('pay_id', ['A', 'B']);
                } else {
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

        $leaves = Leaves::where('status', 0)->orderby('seq')->get();
        $datas = LeaveDay::orderby('created_at', 'desc')->where('user_id', Auth::user()->id)->orderby('created_at');
        if ($request) {
            $state = $request->state;
            if ($state) {
                $datas = $datas->where('state', $state);
            } else {
                $datas = $datas->where('state', 1);
            }
            $start_date_start = $request->start_date_start;
            if ($start_date_start) {
                $start_date_start = $request->start_date_start . ' 00:00:00';
                $datas = $datas->where('start_datetime', '>=', $start_date_start);
            }
            $start_date_end = $request->start_date_end;
            if ($start_date_end) {
                $start_date_end = $request->start_date_end . ' 11:59:59';
                $datas = $datas->where('start_datetime', '<=', $start_date_end);
            }
            $end_date_start = $request->end_date_start;
            if ($end_date_start) {
                $end_date_start = $request->end_date_start . ' 00:00:00';
                $datas = $datas->where('end_datetime', '>=', $end_date_start);
            }
            $end_date_end = $request->end_date_end;
            if ($end_date_end) {
                $end_date_end = $request->end_date_end . ' 11:59:59';
                $datas = $datas->where('end_datetime', '<=', $end_date_end);
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
        } else {
            $datas = $datas->paginate(50);
            $condition = '';
        }

        $condition = $condition = $request->all();



        // dd($leave_datas);
        return view('person.leave_days')->with('datas', $datas)->with('request', $request)->with('condition', $condition)->with('leaves', $leaves);
    }

    public function leave_check_show($id)
    {
        $data = LeaveDay::with(['workflow', 'workflow.steps', 'checks.user', 'checks.step'])->where('id', $id)->first();
        $items = LeaveDayCheck::where('leave_day_id', $data->id)->get();
        $leaves = Leaves::where('status', 0)->orderby('seq')->get();
        
        return view('person.leave_check')->with('data', $data)->with('items', $items)->with('leaves', $leaves);
    }

    public function leave_check_update($id, Request $request)
    {
        $data = LeaveDay::with(['user.job_data', 'workflow.steps'])->where('id', $id)->first();
        
        if (Auth::id() != $data->user_id) {
            return redirect()->back()->with('error', '只有申請人可以送出此假單');
        }

        try {
            // 記錄原始狀態
            $originalState = $data->state;
            
            // 根據申請人的職稱取得對應的審核流程
            $workflow = null;
            
            if ($data->user && $data->user->job_data) {
                // 先尋找該職稱專用的請假審核流程
                $workflow = \App\Models\Workflow::where('is_active', true)
                    ->where('category', 'leave')
                    ->where('job_id', $data->user->job_data->id)
                    ->first();
            }
            
            // 如果沒有找到專用流程，使用預設流程（沒有指定職稱的流程）
            if (!$workflow) {
                $workflow = \App\Models\Workflow::where('is_active', true)
                    ->where('category', 'leave')
                    ->whereNull('job_id')
                    ->first();
            }
            
            if (!$workflow) {
                return redirect()->back()->with('error', '找不到適合的請假審核流程');
            }

            $lastRejectedCheck = $data->checks()
                ->where('state', 3)
                ->orderByDesc('created_at')
                ->first();

            $lastApprovedCheck = $data->checks()
                ->where('state', 9)
                ->orderByDesc('created_at')
                ->first();

            $shouldResumeFromRejection = false;
            if ($lastRejectedCheck) {
                if (!$lastApprovedCheck || Carbon::parse($lastRejectedCheck->created_at)->gt(Carbon::parse($lastApprovedCheck->created_at))) {
                    $shouldResumeFromRejection = true;
                }
            }

            if ($shouldResumeFromRejection) {
                $resumeStepId = $lastRejectedCheck->step_id;
                if (!$resumeStepId && $data->workflow) {
                    $resumeStep = $data->workflow->steps
                        ->sortBy('step_order')
                        ->firstWhere('approver_user_id', $lastRejectedCheck->check_user_id);
                    $resumeStepId = optional($resumeStep)->id;
                }

                // 創建送出審核記錄（state = 10）
                $submitCheck = new \App\Models\LeaveDayCheck;
                $submitCheck->leave_day_id = $data->id;
                $submitCheck->check_user_id = $data->user_id; // 申請人自己送出
                $submitCheck->state = 10; // 送出審核狀態
                $submitCheck->check_day = \Carbon\Carbon::now()->locale('zh-tw')->format('Y-m-d');
                $submitCheck->created_at = \Carbon\Carbon::now()->locale('zh-tw');
                $submitCheck->save();
                
                // 移除舊的待審核紀錄，避免重複
                $data->checks()
                    ->where('state', 2)
                    ->delete();
                
                // 重新開始審核流程
                $workflowService = new LeaveWorkflowService();
                $workflowService->startWorkflow($data, $workflow->id, false, $resumeStepId); // 不創建送出審核記錄，因為已經創建了
            } else {
                // 一般送出，使用正常流程
                $workflowService = new LeaveWorkflowService();
                $workflowService->startWorkflow($data, $workflow->id);
            }
            
            $message = $originalState == 3 ? '假單已重新送出，等待審核' : '假單已送出，等待審核';
            return redirect()->route('person.leave_days')->with('success', $message);
            
        } catch (\Exception $e) {
            \Log::error('假單送出失敗', [
                'leave_day_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', '送出失敗：' . $e->getMessage());
        }
    }

    public function last_leave_days()
    {
        $year = Carbon::now()->year;
        $now_year = Carbon::now()->year;
        $user = User::where('id', Auth::user()->id)->first();
        $leave_datas = [];

        // 假別設定只抓特休
        $leaves = DB::table('leaves')
            ->join('leave_setting', 'leave_setting.leave_id', '=', 'leaves.id')
            ->whereIn('leaves.id', [1])
            ->where('leaves.status', 0)
            ->where('leave_setting.year', '=', $year)
            ->select('leaves.*', 'leave_setting.approved_days as day')
            ->get();

        $dates = [];
        foreach ($leaves as $leave) {
            $dates[$leave->id]['name'] = $leave->name;
            $dates[$leave->id]['day'] = $leave->day;
            $dates[$leave->id]['hour'] = intval($leave->day) * 8;
        }

        // 依照年資判斷目前特休資格天數
        $specil_days = $this->specil_vacation($user->entry_date);
        $specil_hours = $specil_days * 8;
        $dates['1']['user_day'][$user->id]['day'] = $specil_days;
        $dates['1']['user_day'][$user->id]['hour'] = $specil_hours;

        $leave_datas[$user->id]['name'] = $user->name;
        $leave_datas[$user->id]['year'] = $year;

        foreach ($dates as $leave_day => $date) {
            if ($leave_day == 1) {
                // **特休請假只抓資格年度起算日後的資料**
                $specil_start = $this->specil_vacation_start($user->entry_date, Carbon::now());
                if ($specil_start) {
                    $leave_datas[$user->id]['leavedays'][$leave_day]['datas'] = LeaveDay::where('state', '9')
                        ->where('start_datetime', '>=', $specil_start->toDateTimeString())
                        ->where('leave_day', $leave_day)
                        ->where('user_id', $user->id)
                        ->get();
                } else {
                    $leave_datas[$user->id]['leavedays'][$leave_day]['datas'] = collect();
                }
            } else {
                // 其他假別年度區間
                $leave_datas[$user->id]['leavedays'][$leave_day]['datas'] = LeaveDay::where('state', '9')
                    ->where('start_datetime', '>=', $year . '-01-01 00:00:00')
                    ->where('end_datetime', '<=', $year . '-12-31 11:59:59')
                    ->where('leave_day', $leave_day)
                    ->where('user_id', $user->id)
                    ->get();
            }
            $leave_datas[$user->id]['leavedays'][$leave_day]['hour'] = 0;
            $leave_datas[$user->id]['leavedays'][$leave_day]['day'] = 0;
            $leave_datas[$user->id]['leavedays'][$leave_day]['add_day'] = 0;
        }

        // 統計已請假時數
        foreach ($leave_datas as &$data) {
            foreach ($data['leavedays'] as &$leave_days) {
                foreach ($leave_days['datas'] as $leave_data) {
                    if ($leave_data->unit == "day") {
                        $leave_days['hour'] += intval($leave_data->total) * 8;
                    } else {
                        $leave_days['hour'] += intval($leave_data->total);
                    }
                }
            }
        }

        // 計算剩餘與累積天數
        foreach ($leave_datas as $user_id => &$data) {
            foreach ($data['leavedays'] as $leaveday_type => &$leave_days) {
                if ($leaveday_type == '1') {
                    // 特休
                    $total_hour = $dates['1']['user_day'][$user_id]['hour'];
                    $used_hour = $leave_datas[$user_id]['leavedays'][1]['hour'];
                    $remaining_hour = max($total_hour - $used_hour, 0);

                    $daysBasedOn8Hours = intdiv($remaining_hour, 8);
                    $remainingHours = $remaining_hour % 8;

                    $leave_days['day'] = $daysBasedOn8Hours . "天";
                    if ($remainingHours > 0) {
                        $leave_days['day'] .= "，又" . $remainingHours . "小時";
                    }

                    // 累積
                    $usedDays = intdiv($used_hour, 8);
                    $usedRemainHour = $used_hour % 8;
                    $leave_days['add_day'] = $usedDays . "天";
                    if ($usedRemainHour > 0) {
                        $leave_days['add_day'] .= "，又" . $usedRemainHour . "小時";
                    }
                } else {
                    $total_hour = $dates[$leaveday_type]['hour'];
                    $used_hour = $leave_days['hour'];
                    $remaining_hour = max($total_hour - $used_hour, 0);

                    $daysBasedOn8Hours = intdiv($remaining_hour, 8);
                    $remainingHours = $remaining_hour % 8;

                    $leave_days['day'] = $daysBasedOn8Hours . "天";
                    if ($remainingHours > 0) {
                        $leave_days['day'] .= "，又" . $remainingHours . "小時";
                    }

                    $usedDays = intdiv($used_hour, 8);
                    $usedRemainHour = $used_hour % 8;
                    $leave_days['add_day'] = $usedDays . "天";
                    if ($usedRemainHour > 0) {
                        $leave_days['add_day'] .= "，又" . $usedRemainHour . "小時";
                    }
                }
            }
        }

        return view('person.last_leave_days')->with('dates', $dates)->with('leave_datas', $leave_datas);
    }



    //員工盤點
    public function person_inventory(Request $request)
    {
        $datas = GdpaperInventoryData::where('update_user_id', Auth::user()->id);
        if ($request) {
            $after_date = $request->after_date;
            if ($after_date) {
                $datas = $datas->where('date', '>=', $after_date);
            }
            $before_date = $request->before_date;
            if ($before_date) {
                $datas = $datas->where('date', '<=', $before_date);
            }
            $state = $request->state;
            if (isset($state)) {
                $datas = $datas->where('state', $state);
            } else {
                $datas = $datas->where('state', '0');
            }
            $datas = $datas->get();
        } else {
            $datas = $datas->get();
        }
        return view('person.inventorys')->with('datas', $datas)->with('request', $request);
    }

    private function specil_vacation($user_entry_date)
    {
        if (!$user_entry_date) {
            return 0;
        }

        $start = Carbon::parse($user_entry_date);
        $now = Carbon::now();
        $diffDays = $start->diffInDays($now);
        $diffYears = $diffDays / 365;
        $diffYears = round($diffYears, 2);

        if ($diffYears < 0.5) {
            return 0;
        } elseif ($diffYears >= 0.5 && $diffYears < 1) {
            return 3;
        }

        $fullYears = floor($diffYears);

        if ($fullYears < 2) {
            return 7;
        } elseif ($fullYears < 3) {
            return 10;
        } elseif ($fullYears < 5) {
            return 14;
        } elseif ($fullYears < 10) {
            return 15;
        } else {
            $days = 15 + ($fullYears - 10);
            return ($days > 30) ? 30 : $days;
        }
    }

    private function specil_vacation_start($user_entry_date, $now = null)
    {
        if (!$user_entry_date) {
            return null;
        }
        $now = $now ?: Carbon::now();
        $start = Carbon::parse($user_entry_date);
        $diffDays = $start->diffInDays($now);
        $diffYears = $diffDays / 365;
        $diffYears = round($diffYears, 2);

        if ($diffYears < 0.5) {
            return null;
        } elseif ($diffYears >= 0.5 && $diffYears < 1) {
            return $start->copy()->addMonths(6);
        } else {
            $fullYears = floor($diffYears);
            return $start->copy()->addYears($fullYears);
        }
    }
}

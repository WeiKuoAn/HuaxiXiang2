<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\Job;
use App\Models\Branch;
use App\Models\PayData;
use App\Models\UserBank;
use App\Models\Cash;
use App\Models\Vacation;
use Carbon\Carbon;
use App\Models\UserHoliday;
use App\Models\LeaveDay;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class PersonnelController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('status','0')->orderby('level')->paginate(30);
        $year = Vacation::where('year',Carbon::now()->year)->first();//取得當年
        //計算當前專員餘額
        $datas = [];

        foreach($users as $user)
        {
            $user_bank = UserBank::where('user_id',$user->id)->first();//使用者最初餘額
            $user_pay_data = PayData::where('status','1')->where('pay_date','>=','2023-05-23')->where('user_id',$user->id)->sum('price');
            $cash = Cash::where('status','1')->where('give_user_id',$user->id)->sum('price');
            // dd($cash);
            $user_balance = '';
            $user_cash = '';

            if(isset($user_bank)){
                $user_balance = $user_bank->money;
            }else{
                $user_balance = 0;
            }

            if(isset($cash)){
                $user_cash = $cash;
            }else{
                $user_cash = 0;
            }
            if($year == null){
                $day = 0;
            }else{
                $day = $year->day;
            }



            $datas[$user->id]['pay_data'] = $user_pay_data;
            $datas[$user->id]['balance'] = intval($user_balance) + intval($user_cash) - intval($user_pay_data);
            $datas[$user->id]['seniority'] = $this->seniority($user->entry_date);
            $datas[$user->id]['specil_vacation'] = $this->specil_vacation($user->entry_date);
            $datas[$user->id]['remain_specil_vacation'] = intval($this->specil_vacation($user->entry_date)) + intval($day);//剩餘休假天數
        }
        // dd($datas);
        
        return view('personnel.index')->with('users', $users)->with('datas',$datas);
    }

    public function holidays(Request $request)
    {
        $months = [
            '01'=> [ 'name'=>'一月'],
            '02'=> [ 'name'=>'二月'],
            '03'=> [ 'name'=>'三月'],
            '04'=> [ 'name'=>'四月'],
            '05'=> [ 'name'=>'五月'],
            '06'=> [ 'name'=>'六月'],
            '07'=> [ 'name'=>'七月'],
            '08'=> [ 'name'=>'八月'],
            '09'=> [ 'name'=>'九月'],
            '10'=> [ 'name'=>'十月'],
            '11'=> [ 'name'=>'十一月'],
            '12'=> [ 'name'=>'十二月'],
        ];
        if(isset($request->year))
        {
            $year = $request->year;
        }else{
            $year = Carbon::now()->year;//取得當年
        }
        $years = range(Carbon::now()->year,2022);
        $users = User::where('status','0')->whereIn('job_id',[3,4,5])->orderby('job_id')->get();
        $year_holiday = Vacation::where('year',$year)->first();//取放假天數
        $datas = [];

        foreach ($users as $user) {
            $datas[$user->id]['name'] = $user->name;
            $datas[$user->id]['year'] = $year;
            $user_holidays = UserHoliday::where('year', $year)->where('user_id', $user->id)->get();
            if(isset($year_holiday)){
                $datas[$user->id]['last_day'] = intval($year_holiday->day);
            }else{
                $datas[$user->id]['last_day'] = 0;
            }
            $datas[$user->id]['total_day'] = 0;
            foreach ($user_holidays as $user_holiday) {
                $datas[$user->id]['holidays'][$user_holiday->month] = $user_holiday->holiday;
            }
        }
        
        foreach ($datas as &$data) {
            if (isset($data['holidays'])) {
                foreach ($data['holidays'] as $key => $holiday) {
                    $data['last_day'] -= intval($holiday);
                }
            }
            if (isset($data['holidays'])) {
                foreach ($data['holidays'] as $key => $holiday) {
                    $data['total_day'] += intval($holiday);
                }
            }
        }

        $vacations = Vacation::where('year',$year)->get();
        $vdatas = [];
        foreach($vacations as $vacation)
        {
            $vdatas[$vacation->year]['year'] = $vacation->year;
            $vdatas[$vacation->year]['months'] = Vacation::where('year',$vacation->year)->get();
            $vdatas[$vacation->year]['total'] = 0;
        }

        foreach($vdatas as $vdata)
        {
            foreach($vdata['months'] as $month)
            {
                $vdatas[$vacation->year]['total'] += $month->day;
            }
        }
        
        return view('personnel.holidays')->with('months',$months)->with('years',$years)
                                         ->with('request',$request)->with('datas',$datas)->with('vdatas',$vdatas);
    }

    public function holiday_create()
    {
        $year = Carbon::now()->year;//取得當年
        $this_month = Carbon::now()->month;
        $users = User::where('status','0')->whereIn('job_id',[3,4,5])->orderby('job_id')->get();
        $months = [
            '01'=> [ 'name'=>'一月'],
            '02'=> [ 'name'=>'二月'],
            '03'=> [ 'name'=>'三月'],
            '04'=> [ 'name'=>'四月'],
            '05'=> [ 'name'=>'五月'],
            '06'=> [ 'name'=>'六月'],
            '07'=> [ 'name'=>'七月'],
            '08'=> [ 'name'=>'八月'],
            '09'=> [ 'name'=>'九月'],
            '10'=> [ 'name'=>'十月'],
            '11'=> [ 'name'=>'十一月'],
            '12'=> [ 'name'=>'十二月'],
        ];

        return view('personnel.holiday_create')->with('year',$year)
                                               ->with('months',$months)
                                               ->with('this_month',$this_month)
                                               ->with('users',$users);
    }

    public function holiday_store(Request $request)
    {
        // dd($request->users);
        foreach($request->users as $key =>$user)
        {
            if(isset($user))
            {
                $user_holiday = UserHoliday::where('year',$request->year)->where('month',$request->month)->where('user_id',$user)->first();
                if($user_holiday == null)
                {
                    $data = new UserHoliday;
                    $data->year = $request->year;
                    $data->month = $request->month;
                    $data->holiday = $request->holiday[$key];
                    $data->user_id = $request->users[$key];
                    $data->save();
                }else{
                    $user_holiday->holiday = $request->holiday[$key];
                    $user_holiday->save();
                }
                    
            }
        }

        
        return redirect()->route('personnel.holidays');
    }

    public function holiday_edit(Request $request  ,$user_id , $year, $month )
    {
        $year = $year;
        $month = $month;
        $user = User::where('id',$user_id)->first();
        $data = UserHoliday::where('year',$year)->where('month',$month)->where('user_id',$user_id)->first();
        // dd($data);
        return view('personnel.holiday_edit')->with('year',$year)
                                               ->with('month',$month)
                                               ->with('data',$data)
                                               ->with('user',$user);
    }

    public function holiday_update(Request $request  ,$user_id , $year, $month )
    {
        $data = UserHoliday::where('year',$year)->where('month',$month)->where('user_id',$user_id)->first();
        $data->holiday = $request->holiday;
        $data->save();
        return redirect()->route('personnel.holidays');
    }

    private function seniority($user_entry_date)
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
        return $diffYears;
    }

    public function other_holidays(Request $request)
    {
        $months = [
            '01'=> [ 'name'=>'一月'],
            '02'=> [ 'name'=>'二月'],
            '03'=> [ 'name'=>'三月'],
            '04'=> [ 'name'=>'四月'],
            '05'=> [ 'name'=>'五月'],
            '06'=> [ 'name'=>'六月'],
            '07'=> [ 'name'=>'七月'],
            '08'=> [ 'name'=>'八月'],
            '09'=> [ 'name'=>'九月'],
            '10'=> [ 'name'=>'十月'],
            '11'=> [ 'name'=>'十一月'],
            '12'=> [ 'name'=>'十二月'],
        ];
        if(isset($request->year))
        {
            $year = $request->year;
        }else{
            $year = Carbon::now()->year;//取得當年
        }
        $years = range(Carbon::now()->year,2022);
        $users = User::where('status','0')->whereIn('job_id',[3,4,5])->orderby('job_id')->get();
        $datas = [];
        $dates= [   'special'=>['name'=>'特休'], 
                    // 'marriage'=>['name'=>'婚假' , 'day'=>'14'], 
                    'sick'=>['name'=>'病假'  , 'day'=>'28' , 'hour'=>'224'], 
                    'personal'=>['name'=>'事假', 'day'=>'7' , 'hour'=>'56'], 
                    // 'bereavement'=>['name'=>'喪假' , 'day'=>'2'], 
                    // 'work-related'=>['name'=>'工傷假' , 'day'=>'2'], 
                    // 'public'=>['name'=>'公假' , 'day'=>'2'] ,
                    // 'menstrual'=>['name'=>'生理假' , 'day'=>'2'],
                    // 'maternity'=>['name'=>'產假' , 'day'=>'8'] ,
                    // 'prenatalCheckUp'=>['name'=>'產檢假' , 'day'=>'7'] ,
                    // 'paternity'=>['name'=>'陪產假' , 'day'=>'7'] ,
                    // 'fetalProtection'=>['name'=>'安胎假' , 'day'=>'2'],
                    // 'familyCare'=>['name'=>'家庭照顧假' , 'day'=>'7' , 'hour'=>'56'],
                ];

        foreach ($users as $user) {
            //每人的特休天數
            $dates['special']['user_day'][$user->id]['day'] = $this->specil_vacation($user->entry_date);
            $dates['special']['user_day'][$user->id]['hour'] = intval($this->specil_vacation($user->entry_date)*8);
        }

    // dd($dates);

        
        foreach ($users as $user) {
            $datas[$user->id]['name'] = $user->name;
            $datas[$user->id]['year'] = $year;
            foreach($dates as $leave_day=>$date)
            {
                $datas[$user->id]['leavedays'][$leave_day]['datas'] = LeaveDay::where('start_datetime','>=',$year.'-01-01 00:00:00')->where('end_datetime','<=',$year.'-12-31 11:59:59')->where('leave_day',$leave_day)->where('user_id', $user->id)->get();
                $datas[$user->id]['leavedays'][$leave_day]['hour'] = 0;
                //剩餘天數
                $datas[$user->id]['leavedays'][$leave_day]['day'] = 0; 
                //累積天數
                $datas[$user->id]['leavedays'][$leave_day]['add_day'] = 0; 
            }
        }
        
        foreach ($datas as &$data) {
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

        // dd($datas);

        // 創建一個基準時間點的 Carbon 實例（例如：現在）
        $baseTime = Carbon::now();

        foreach($datas as $user_id=>&$data)
        {
            foreach($data['leavedays'] as $leaveday_type=>&$leave_days)
            {
                if($leaveday_type == 'special'){
                    //如果是特休，要重新寫條鍵;
                    $timeAfterHours = $baseTime->copy()->addHours($dates['special']['user_day'][$user_id]['hour'] - $datas[$user_id]['leavedays']['special']['hour']);
                    // 計算天數差異
                    $hoursDifference = $timeAfterHours->diffInHours($baseTime);
                    $daysBasedOn8Hours = intdiv($hoursDifference, 8);
                    // 計算剩餘小時數
                    $remainingHours = $hoursDifference % 8;
                    //剩餘天數
                    if($datas[$user_id]['leavedays']['special']['hour'] > 0){
                        $datas[$user_id]['leavedays']['special']['day'] = $daysBasedOn8Hours . "天，又" . $remainingHours . "小時"; 
                    }else{
                        $datas[$user_id]['leavedays']['special']['day'] = $daysBasedOn8Hours . "天"; 
                    }

                    //累積天數
                    $add_timeAfterHours = $baseTime->copy()->addHours($datas[$user_id]['leavedays']['special']['hour']);
                    // 計算天數差異
                    $add_hoursDifference = $add_timeAfterHours->diffInHours($baseTime);
                    $add_daysBasedOn8Hours = intdiv($add_hoursDifference, 8);
                    // 計算剩餘小時數
                    $add_remainingHours = $add_hoursDifference % 8;
                    if($datas[$user_id]['leavedays']['special']['hour'] > 0){
                        $datas[$user_id]['leavedays']['special']['add_day'] = $add_daysBasedOn8Hours . "天，又" . $add_remainingHours . "小時"; 
                    }else{
                        $datas[$user_id]['leavedays']['special']['add_day'] = $add_remainingHours . "小時"; 
                    }
                }else{
                    //如果是特休，要重新寫條鍵;
                    $timeAfterHours = $baseTime->copy()->addHours($dates[$leaveday_type]['hour'] - $datas[$user_id]['leavedays'][$leaveday_type]['hour']);
                    // 計算天數差異
                    $hoursDifference = $timeAfterHours->diffInHours($baseTime);
                    $daysBasedOn8Hours = intdiv($hoursDifference, 8);
                    // 計算剩餘小時數
                    $remainingHours = $hoursDifference % 8;
                    //剩餘天數
                    if($datas[$user_id]['leavedays'][$leaveday_type]['hour'] > 0){
                        $datas[$user_id]['leavedays'][$leaveday_type]['day'] = $daysBasedOn8Hours . "天，又" . $remainingHours . "小時"; 
                    }else{
                        $datas[$user_id]['leavedays'][$leaveday_type]['day'] = $daysBasedOn8Hours . "天"; 
                    }

                    //累積天數
                    $add_timeAfterHours = $baseTime->copy()->addHours($datas[$user_id]['leavedays'][$leaveday_type]['hour']);
                    // 計算天數差異
                    $add_hoursDifference = $add_timeAfterHours->diffInHours($baseTime);
                    $add_daysBasedOn8Hours = intdiv($add_hoursDifference, 8);
                    // 計算剩餘小時數
                    $add_remainingHours = $add_hoursDifference % 8;
                    if($datas[$user_id]['leavedays'][$leaveday_type]['hour'] > 8){
                        $datas[$user_id]['leavedays'][$leaveday_type]['add_day'] = $add_daysBasedOn8Hours . "天，又" . $add_remainingHours . "小時"; 
                    }else{
                        $datas[$user_id]['leavedays'][$leaveday_type]['add_day'] = $add_remainingHours . "小時"; 
                    }
                }
            }
        }

        // dd($dates);
        return view('personnel.other_holidays')->with('months',$months)->with('years',$years)->with('request',$request)->with('datas',$datas)->with('dates',$dates);
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
        }elseif($diffYears > 0.5 && $diffYears < 1){ //大於半年小於一年
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

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
use Illuminate\Support\Facades\DB;
use App\Models\Leaves;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class PersonnelController extends Controller
{
    public function index(Request $request)
    {

        if (!isset($request->status) || $request->status == 0) {
            $users = User::where('status', '0');
        } else {
            $users = User::where('status', '1');
        }

        if ($request->name) {
            $users = $users->where('name', 'like', $request->name . '%');
        }

        $users = $users->orderby('level')->orderby('seq')->paginate(30);


        $year = Vacation::where('year', Carbon::now()->year)->first(); //取得當年
        //計算當前專員餘額
        $datas = [];

        foreach ($users as $user) {
            $user_bank = UserBank::where('user_id', $user->id)->first(); //使用者最初餘額
            $user_pay_data = PayData::where('status', '1')->where('pay_date', '>=', '2023-05-23')->where('user_id', $user->id)->sum('price');
            $cash = Cash::where('status', '1')->where('give_user_id', $user->id)->sum('price');
            // dd($cash);
            $user_balance = '';
            $user_cash = '';

            if (isset($user_bank)) {
                $user_balance = $user_bank->money;
            } else {
                $user_balance = 0;
            }

            if (isset($cash)) {
                $user_cash = $cash;
            } else {
                $user_cash = 0;
            }
            if ($year == null) {
                $day = 0;
            } else {
                $day = $year->day;
            }



            $datas[$user->id]['pay_data'] = $user_pay_data;
            $datas[$user->id]['balance'] = intval($user_balance) + intval($user_cash) - intval($user_pay_data);
            $datas[$user->id]['seniority'] = $this->seniority($user->entry_date);
            $datas[$user->id]['specil_vacation'] = $this->specil_vacation($user->entry_date);
            $datas[$user->id]['remain_specil_vacation'] = intval($this->specil_vacation($user->entry_date)) + intval($day); //剩餘休假天數
        }
        // dd($datas);

        return view('personnel.index')->with('users', $users)->with('datas', $datas)->with('request', $request);
    }

    public function holidays(Request $request)
    {
        $months = [
            '01' => ['name' => '一月'],
            '02' => ['name' => '二月'],
            '03' => ['name' => '三月'],
            '04' => ['name' => '四月'],
            '05' => ['name' => '五月'],
            '06' => ['name' => '六月'],
            '07' => ['name' => '七月'],
            '08' => ['name' => '八月'],
            '09' => ['name' => '九月'],
            '10' => ['name' => '十月'],
            '11' => ['name' => '十一月'],
            '12' => ['name' => '十二月'],
        ];
        if (isset($request->year)) {
            $year = $request->year;
        } else {
            $year = Carbon::now()->year; //取得當年
        }
        $years = range(Carbon::now()->year, 2022);
        $users = User::where('status', '0')->whereIn('job_id', [3, 4, 5])->orderby('job_id')->get();
        $year_holiday = Vacation::where('year', $year)->sum('day'); //取放假天數
        // dd($year_holiday);
        $datas = [];

        $vacations = Vacation::where('year', $year)->get();
        $vdatas = [];
        foreach ($vacations as $vacation) {
            $vdatas[$vacation->year]['year'] = $vacation->year;
            $vdatas[$vacation->year]['months'] = Vacation::where('year', $vacation->year)->get();
            $vdatas[$vacation->year]['total'] = 0;
        }

        foreach ($vdatas as $vdata) {
            foreach ($vdata['months'] as $month) {
                $vdatas[$vacation->year]['total'] += $month->day;
            }
        }

        foreach ($users as $user) {
            $datas[$user->id]['name'] = $user->name;
            $datas[$user->id]['year'] = $year;
            $user_holidays = UserHoliday::where('year', $year)->where('user_id', $user->id)->get();
            if (isset($year_holiday)) {
                $datas[$user->id]['last_day'] = intval($year_holiday);
            } else {
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
        // dd($datas);


        return view('personnel.holidays')->with('months', $months)->with('years', $years)
            ->with('request', $request)->with('datas', $datas)->with('vdatas', $vdatas);
    }

    public function holiday_create()
    {
        $year = Carbon::now()->year; //取得當年
        $this_month = Carbon::now()->month;
        $users = User::where('status', '0')->whereIn('job_id', [3, 4, 5])->orderby('job_id')->get();
        $months = [
            '01' => ['name' => '一月'],
            '02' => ['name' => '二月'],
            '03' => ['name' => '三月'],
            '04' => ['name' => '四月'],
            '05' => ['name' => '五月'],
            '06' => ['name' => '六月'],
            '07' => ['name' => '七月'],
            '08' => ['name' => '八月'],
            '09' => ['name' => '九月'],
            '10' => ['name' => '十月'],
            '11' => ['name' => '十一月'],
            '12' => ['name' => '十二月'],
        ];

        return view('personnel.holiday_create')->with('year', $year)
            ->with('months', $months)
            ->with('this_month', $this_month)
            ->with('users', $users);
    }

    public function holiday_store(Request $request)
    {
        // dd($request->users);
        foreach ($request->users as $key => $user) {
            if (isset($user)) {
                $user_holiday = UserHoliday::where('year', $request->year)->where('month', $request->month)->where('user_id', $user)->first();
                if ($user_holiday == null) {
                    $data = new UserHoliday;
                    $data->year = $request->year;
                    $data->month = $request->month;
                    $data->holiday = $request->holiday[$key];
                    $data->user_id = $request->users[$key];
                    $data->save();
                } else {
                    $user_holiday->holiday = $request->holiday[$key];
                    $user_holiday->save();
                }
            }
        }


        return redirect()->route('personnel.holidays');
    }

    public function holiday_edit(Request $request, $user_id, $year, $month)
    {
        $year = $year;
        $month = $month;
        $user = User::where('id', $user_id)->first();
        $data = UserHoliday::where('year', $year)->where('month', $month)->where('user_id', $user_id)->first();
        // dd($data);
        return view('personnel.holiday_edit')->with('year', $year)
            ->with('month', $month)
            ->with('data', $data)
            ->with('user', $user);
    }

    public function holiday_update(Request $request, $user_id, $year, $month)
    {
        $data = UserHoliday::where('year', $year)->where('month', $month)->where('user_id', $user_id)->first();
        $data->holiday = $request->holiday;
        $data->save();
        return redirect()->route('personnel.holidays');
    }

    private function seniority($user_entry_date)
    {
        if ($user_entry_date != null) {
            $today = date('Y-m-d', strtotime(Carbon::now()->locale('zh-tw')));
            $startDate = Carbon::parse($user_entry_date); // 將起始日期字串轉換為 Carbon 日期物件
            $endDate = Carbon::parse($today); // 將結束日期字串轉換為 Carbon 日期物件
            $diffDays = $startDate->diffInDays($endDate); // 計算年數差距
            $diffYears = $diffDays / 365;
            $diffYears = round($diffYears, 2);
        } else {
            $diffYears = 0;
        }
        return $diffYears;
    }

    public function other_holidays(Request $request)
    {
        $months = [
            '01' => ['name' => '一月'],
            '02' => ['name' => '二月'],
            '03' => ['name' => '三月'],
            '04' => ['name' => '四月'],
            '05' => ['name' => '五月'],
            '06' => ['name' => '六月'],
            '07' => ['name' => '七月'],
            '08' => ['name' => '八月'],
            '09' => ['name' => '九月'],
            '10' => ['name' => '十月'],
            '11' => ['name' => '十一月'],
            '12' => ['name' => '十二月'],
        ];
        if (isset($request->year)) {
            $year = $request->year;
        } else {
            $year = Carbon::now()->year; //取得當年
        }
        $now_year = Carbon::now()->year; //取當年計算特休的年度
        $years = range(Carbon::now()->year, 2022);
        $users = User::where('status', '0')->whereIn('job_id', [3, 4, 5])->orderby('job_id')->get();
        $datas = [];
        $leaves = DB::table('leaves')
            ->join('leave_setting', 'leave_setting.leave_id', '=', 'leaves.id')
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
        // dd($dates);
        foreach ($users as $user) {
            //每人的特休天數
            $dates['1']['user_day'][$user->id]['day'] = $this->specil_vacation($user->entry_date);
            $dates['1']['user_day'][$user->id]['hour'] = intval($this->specil_vacation($user->entry_date)) * 8;
        }

        foreach ($users as $user) {
            $datas[$user->id]['name'] = $user->name;
            $datas[$user->id]['year'] = $year;
            foreach ($dates as $leave_day => $date) {
                if ($leave_day == 1) {
                    $datas[$user->id]['leavedays'][$leave_day]['datas'] = LeaveDay::where('state', '9')->where('start_datetime', '>=', '2020-01-01 00:00:00')->where('end_datetime', '<=', $now_year . '-12-31 11:59:59')->where('leave_day', $leave_day)->where('user_id', $user->id)->get();
                } else {
                    $datas[$user->id]['leavedays'][$leave_day]['datas'] = LeaveDay::where('state', '9')->where('start_datetime', '>=', $year . '-01-01 00:00:00')->where('end_datetime', '<=', $year . '-12-31 11:59:59')->where('leave_day', $leave_day)->where('user_id', $user->id)->get();
                }
                $datas[$user->id]['leavedays'][$leave_day]['hour'] = 0;
                //剩餘天數
                $datas[$user->id]['leavedays'][$leave_day]['day'] = 0;
                //累積天數
                $datas[$user->id]['leavedays'][$leave_day]['add_day'] = 0;
            }
        }

        foreach ($datas as &$data) {
            foreach ($data['leavedays'] as &$leave_days) {
                if (count($leave_days['datas']) >= 0) {
                    foreach ($leave_days['datas'] as $key => $leave_data) {
                        // dd($leave_data->unit);
                        if ($leave_data->unit == "day") {
                            $leave_days['hour'] += intval($leave_data->total) * 8;
                        } else {
                            $leave_days['hour'] += intval($leave_data->total);
                        }
                    }
                }
            }
        }

        // dd($datas);

        // 創建一個基準時間點的 Carbon 實例（例如：現在）
        $baseTime = Carbon::now();

        foreach ($datas as $user_id => &$data) {
            foreach ($data['leavedays'] as $leaveday_type => &$leave_days) {
                if ($leaveday_type == '1') {
                    //如果是特休，要重新寫條鍵;
                    if ($dates['1']['user_day'][$user_id]['hour'] < $datas[$user_id]['leavedays']['1']['hour']) {
                        // Set hours to 0 if requested hours are less
                        $timeAfterHours = $baseTime->copy(); // No hours added
                        $add_timeAfterHours = $baseTime->copy(); // No hours added for cumulative calculation
                    } else {
                        //如果是特休，要重新寫條鍵;
                        $timeAfterHours = $baseTime->copy()->addHours($dates['1']['user_day'][$user_id]['hour'] - $datas[$user_id]['leavedays']['1']['hour']);
                        //累積天數
                        $add_timeAfterHours = $baseTime->copy()->addHours($datas[$user_id]['leavedays']['1']['hour']);
                    }
                    // 計算天數差異
                    $hoursDifference = $timeAfterHours->diffInHours($baseTime);
                    $daysBasedOn8Hours = intdiv($hoursDifference, 8);
                    // 計算剩餘小時數
                    $remainingHours = $hoursDifference % 8;
                    //剩餘天數
                    if ($datas[$user_id]['leavedays']['1']['hour'] > 0) {
                        $datas[$user_id]['leavedays']['1']['day'] = $daysBasedOn8Hours . "天，又" . $remainingHours . "小時";
                    } else {
                        $datas[$user_id]['leavedays']['1']['day'] = $daysBasedOn8Hours . "天";
                    }

                    //累積天數
                    $add_timeAfterHours = $baseTime->copy()->addHours($datas[$user_id]['leavedays']['1']['hour']);
                    // 計算天數差異
                    $add_hoursDifference = $add_timeAfterHours->diffInHours($baseTime);
                    $add_daysBasedOn8Hours = intdiv($add_hoursDifference, 8);
                    // 計算剩餘小時數
                    $add_remainingHours = $add_hoursDifference % 8;
                    if ($datas[$user_id]['leavedays']['1']['hour'] > 0) {
                        $datas[$user_id]['leavedays']['1']['add_day'] = $add_daysBasedOn8Hours . "天，又" . $add_remainingHours . "小時";
                    } else {
                        $datas[$user_id]['leavedays']['1']['add_day'] = $add_remainingHours . "小時";
                    }
                } else {
                    if ($dates[$leaveday_type]['hour'] < $datas[$user_id]['leavedays'][$leaveday_type]['hour']) {
                        $timeAfterHours = $baseTime->copy();
                    } else {
                        $timeAfterHours = $baseTime->copy()->addHours($dates[$leaveday_type]['hour'] - $datas[$user_id]['leavedays'][$leaveday_type]['hour']);
                    }
                    // 計算天數差異
                    $hoursDifference = $timeAfterHours->diffInHours($baseTime);
                    $daysBasedOn8Hours = intdiv($hoursDifference, 8);
                    // 計算剩餘小時數
                    $remainingHours = $hoursDifference % 8;
                    //剩餘天數
                    if ($datas[$user_id]['leavedays'][$leaveday_type]['hour'] > 0) {
                        $datas[$user_id]['leavedays'][$leaveday_type]['day'] = $daysBasedOn8Hours . "天，又" . $remainingHours . "小時";
                    } else {
                        $datas[$user_id]['leavedays'][$leaveday_type]['day'] = $daysBasedOn8Hours . "天";
                    }

                    //累積天數
                    $add_timeAfterHours = $baseTime->copy()->addHours($datas[$user_id]['leavedays'][$leaveday_type]['hour']);
                    // 計算天數差異
                    $add_hoursDifference = $add_timeAfterHours->diffInHours($baseTime);
                    $add_daysBasedOn8Hours = intdiv($add_hoursDifference, 8);
                    // 計算剩餘小時數
                    $add_remainingHours = $add_hoursDifference % 8;
                    if ($datas[$user_id]['leavedays'][$leaveday_type]['hour'] > 8) {
                        $datas[$user_id]['leavedays'][$leaveday_type]['add_day'] = $add_daysBasedOn8Hours . "天，又" . $add_remainingHours . "小時";
                    } else {
                        $datas[$user_id]['leavedays'][$leaveday_type]['add_day'] = $add_remainingHours . "小時";
                    }
                }
            }
        }
        // dd($datas);
        return view('personnel.other_holidays')->with('months', $months)->with('years', $years)->with('request', $request)->with('datas', $datas)->with('dates', $dates);
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
}

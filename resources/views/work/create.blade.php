@extends('layouts.vertical', ['page_title' => '批次新增/編輯出勤記錄'])

@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
<!-- third party css end -->
<style>
    .month-calendar {
        width: 100%;
        border-collapse: collapse;
    }
    .month-calendar th {
        background-color: #f8f9fa;
        padding: 12px;
        text-align: center;
        font-weight: 600;
        border: 1px solid #dee2e6;
    }
    .month-calendar td {
        border: 1px solid #dee2e6;
        padding: 8px;
        vertical-align: top;
        height: 120px;
    }
    .day-header {
        font-weight: bold;
        margin-bottom: 5px;
        padding: 5px;
        border-radius: 4px;
    }
    .weekend {
        background-color: #fff3cd;
    }
    .has-record {
        background-color: #d1ecf1;
    }
    .day-number {
        display: inline-block;
        min-width: 25px;
        text-align: center;
    }
    .time-input-group {
        margin-top: 5px;
    }
    .time-input-group label {
        font-size: 11px;
        margin-bottom: 2px;
        display: block;
    }
    .time-input-group input {
        font-size: 12px;
        padding: 4px 6px;
        height: 30px;
    }
    .remark-input {
        margin-top: 5px;
    }
    .remark-input input {
        font-size: 11px;
        padding: 3px 5px;
        height: 26px;
    }
    .month-selector {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }
    .month-selector select {
        width: 120px;
    }
    .btn-month-nav {
        min-width: 100px;
    }
    .record-status {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 3px;
        display: inline-block;
        margin-top: 3px;
    }
    .status-new {
        background-color: #d4edda;
        color: #155724;
    }
    .status-exists {
        background-color: #cce5ff;
        color: #004085;
    }
    .total-hours {
        font-size: 10px;
        color: #6c757d;
        margin-top: 3px;
    }
</style>
@endsection

@section('content')
<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Huaxixiang</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">用戶管理</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.work.index', $user->id) }}">出勤列表</a></li>
                        <li class="breadcrumb-item active">批次新增/編輯出勤記錄</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ $user->name }} - 批次新增/編輯出勤記錄</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information-outline me-2"></i>
                        <strong>提示：</strong>選擇年月後，系統會顯示該月所有天數。已有資料的會自動填入，可以直接修改；沒有資料的可以直接填寫。清空時間欄位可刪除該日記錄。
                    </div>

                    <!-- 月份選擇器 -->
                    <div class="month-selector">
                        <button type="button" class="btn btn-secondary btn-sm btn-month-nav" onclick="changeMonth(-1)">
                            <i class="mdi mdi-chevron-left"></i> 上個月
                        </button>
                        
                        <select class="form-select" id="yearSelect" onchange="loadMonth()">
                            @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}年</option>
                            @endfor
                        </select>
                        
                        <select class="form-select" id="monthSelect" onchange="loadMonth()">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ $m }}月</option>
                            @endfor
                        </select>
                        
                        <button type="button" class="btn btn-secondary btn-sm btn-month-nav" onclick="changeMonth(1)">
                            下個月 <i class="mdi mdi-chevron-right"></i>
                        </button>
                        
                        <button type="button" class="btn btn-info btn-sm" onclick="goToCurrentMonth()">
                            <i class="mdi mdi-calendar-today"></i> 本月
                        </button>
                    </div>

                    <form action="{{ route('user.work.batch.store', $user->id) }}" method="POST" id="batchWorkForm">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <input type="hidden" name="month" value="{{ $month }}">

                        <!-- 月曆表格 -->
                        <div class="table-responsive">
                            <table class="month-calendar">
                                <thead>
                                    <tr>
                                        <th style="width: 14.28%;">週日</th>
                                        <th style="width: 14.28%;">週一</th>
                                        <th style="width: 14.28%;">週二</th>
                                        <th style="width: 14.28%;">週三</th>
                                        <th style="width: 14.28%;">週四</th>
                                        <th style="width: 14.28%;">週五</th>
                                        <th style="width: 14.28%;">週六</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $weekDays = [];
                                        $currentWeek = [];
                                        $firstDayOfWeek = $days[0]['dayOfWeek'];
                                        
                                        // 填充第一週之前的空格
                                        for ($i = 0; $i < $firstDayOfWeek; $i++) {
                                            $currentWeek[] = null;
                                        }
                                        
                                        // 填充所有日期
                                        foreach ($days as $day) {
                                            $currentWeek[] = $day;
                                            
                                            // 如果這週滿了（7天），就存入並開始新的一週
                                            if (count($currentWeek) == 7) {
                                                $weekDays[] = $currentWeek;
                                                $currentWeek = [];
                                            }
                                        }
                                        
                                        // 填充最後一週剩餘的空格
                                        if (count($currentWeek) > 0) {
                                            while (count($currentWeek) < 7) {
                                                $currentWeek[] = null;
                                            }
                                            $weekDays[] = $currentWeek;
                                        }
                                    @endphp
                                    
                                    @foreach ($weekDays as $week)
                                        <tr>
                                            @foreach ($week as $day)
                                                <td class="{{ $day && $day['isWeekend'] ? 'weekend' : '' }} {{ $day && $day['record'] ? 'has-record' : '' }}">
                                                    @if ($day)
                                                        <div class="day-header">
                                                            <span class="day-number">{{ $day['day'] }}</span>
                                                            @if ($day['record'])
                                                                <span class="record-status status-exists">已有記錄</span>
                                                            @endif
                                                        </div>
                                                        
                                                        <input type="hidden" name="records[{{ $loop->parent->index * 7 + $loop->index }}][date]" value="{{ $day['date'] }}">
                                                        
                                                        <div class="time-input-group">
                                                            <label>上班</label>
                                                            <input type="time" 
                                                                   class="form-control work-time" 
                                                                   name="records[{{ $loop->parent->index * 7 + $loop->index }}][worktime]"
                                                                   value="{{ $day['record'] ? \Carbon\Carbon::parse($day['record']->worktime)->format('H:i') : '' }}"
                                                                   onchange="calculateHours(this)">
                                                        </div>
                                                        
                                                        <div class="time-input-group">
                                                            <label>下班</label>
                                                            <input type="time" 
                                                                   class="form-control work-time" 
                                                                   name="records[{{ $loop->parent->index * 7 + $loop->index }}][dutytime]"
                                                                   value="{{ $day['record'] ? \Carbon\Carbon::parse($day['record']->dutytime)->format('H:i') : '' }}"
                                                                   onchange="calculateHours(this)">
                                                        </div>
                                                        
                                                        <div class="remark-input">
                                                            <input type="text" 
                                                                   class="form-control" 
                                                                   name="records[{{ $loop->parent->index * 7 + $loop->index }}][remark]"
                                                                   value="{{ $day['record'] ? $day['record']->remark : '' }}"
                                                                   placeholder="備註">
                                                        </div>
                                                        
                                                        <div class="total-hours" data-index="{{ $loop->parent->index * 7 + $loop->index }}">
                                                            @if ($day['record'])
                                                                工時: {{ $day['record']->total }}小時
                                                            @endif
                                                        </div>
                                                        
                                                        <input type="hidden" name="records[{{ $loop->parent->index * 7 + $loop->index }}][status]" value="0">
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg waves-effect waves-light m-1">
                                    <i class="fe-check-circle me-1"></i>儲存所有變更
                                </button>
                                <a href="{{ route('user.work.index', $user->id) }}" class="btn btn-secondary btn-lg waves-effect waves-light m-1">
                                    <i class="fe-x me-1"></i>取消返回
                                </a>
                            </div>
                        </div>
                    </form>
                </div> <!-- end card-body -->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>
    <!-- end row-->

</div> <!-- container -->
@endsection

@section('script')
<!-- third party js -->
<script src="{{asset('assets/libs/select2/select2.min.js')}}"></script>
<script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>
<!-- third party js ends -->

<script>
const userId = {{ $user->id }};

// 載入指定月份
function loadMonth() {
    const year = document.getElementById('yearSelect').value;
    const month = document.getElementById('monthSelect').value;
    window.location.href = `{{ route('user.work.batch.create', $user->id) }}?year=${year}&month=${month}`;
}

// 切換月份
function changeMonth(offset) {
    let year = parseInt(document.getElementById('yearSelect').value);
    let month = parseInt(document.getElementById('monthSelect').value);
    
    month += offset;
    
    if (month > 12) {
        month = 1;
        year++;
    } else if (month < 1) {
        month = 12;
        year--;
    }
    
    window.location.href = `{{ route('user.work.batch.create', $user->id) }}?year=${year}&month=${month}`;
}

// 跳轉到本月
function goToCurrentMonth() {
    const now = new Date();
    const year = now.getFullYear();
    const month = now.getMonth() + 1;
    window.location.href = `{{ route('user.work.batch.create', $user->id) }}?year=${year}&month=${month}`;
}

// 計算工時
function calculateHours(element) {
    const td = element.closest('td');
    const worktimeInput = td.querySelector('input[name*="[worktime]"]');
    const dutytimeInput = td.querySelector('input[name*="[dutytime]"]');
    const totalHoursDiv = td.querySelector('.total-hours');
    
    if (!worktimeInput.value || !dutytimeInput.value) {
        totalHoursDiv.textContent = '';
        return;
    }
    
    const dateInput = td.querySelector('input[name*="[date]"]');
    const date = dateInput.value;
    
    const startTime = new Date(`${date} ${worktimeInput.value}`);
    let endTime = new Date(`${date} ${dutytimeInput.value}`);
    
    // 如果下班時間早於上班時間，表示跨日
    if (endTime < startTime) {
        endTime.setDate(endTime.getDate() + 1);
    }
    
    let hours = (endTime - startTime) / (1000 * 60 * 60);
    
    // 滿9小時要減1小時休息時間
    if (hours >= 9) {
        hours = hours - 1;
    }
    
    totalHoursDiv.textContent = `工時: ${Math.floor(hours)}小時`;
}

// 表單提交前驗證
document.getElementById('batchWorkForm').addEventListener('submit', function(e) {
    const workTimes = document.querySelectorAll('input[name*="[worktime]"]');
    const dutyTimes = document.querySelectorAll('input[name*="[dutytime]"]');
    
    let hasData = false;
    
    // 檢查是否至少有一筆資料
    for (let i = 0; i < workTimes.length; i++) {
        if (workTimes[i].value || dutyTimes[i].value) {
            hasData = true;
            break;
        }
    }
    
    if (!hasData) {
        e.preventDefault();
        alert('請至少填寫一筆出勤記錄！');
        return false;
    }
    
    // 檢查有填寫時間的記錄是否完整
    for (let i = 0; i < workTimes.length; i++) {
        const hasWorktime = workTimes[i].value;
        const hasDutytime = dutyTimes[i].value;
        
        if (hasWorktime && !hasDutytime) {
            e.preventDefault();
            alert('有上班時間但沒有下班時間，請檢查資料完整性！');
            return false;
        }
        
        if (!hasWorktime && hasDutytime) {
            e.preventDefault();
            alert('有下班時間但沒有上班時間，請檢查資料完整性！');
            return false;
        }
    }
});

// 頁面載入時計算所有已填寫的工時
document.addEventListener('DOMContentLoaded', function() {
    const workTimes = document.querySelectorAll('input[name*="[worktime]"]');
    workTimes.forEach(input => {
        if (input.value) {
            calculateHours(input);
        }
    });
});
</script>
@endsection

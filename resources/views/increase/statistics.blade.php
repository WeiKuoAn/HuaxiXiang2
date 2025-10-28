@extends('layouts.vertical', ['page_title' => '加成統計'])

@section('css')
<style>
    .statistics-table {
        font-size: 0.875rem;
    }
    
    .statistics-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border: 1px solid #dee2e6;
        padding: 8px;
        text-align: center;
        vertical-align: middle;
    }
    
    .statistics-table td {
        border: 1px solid #dee2e6;
        padding: 6px;
        text-align: center;
        vertical-align: middle;
    }
    
    .user-name-cell {
        background-color: #f8f9fa;
        font-weight: 600;
        text-align: left;
        padding-left: 12px;
    }
    
    .date-header {
        background-color: #e9ecef;
        font-weight: 600;
        writing-mode: vertical-rl;
        text-orientation: mixed;
        min-width: 40px;
        max-width: 40px;
    }
    
    .amount-cell {
        font-size: 0.75rem;
        padding: 4px 6px;
    }
    
    .total-row {
        background-color: #fff3cd;
        font-weight: 600;
    }
    
    .monthly-total-row {
        background-color: #d1ecf1;
        font-weight: 700;
    }
    
    .category-badge {
        font-size: 0.6rem;
        padding: 2px 4px;
        margin: 1px;
    }
    
    .overtime-hours {
        font-size: 0.7rem;
        color: #6c757d;
    }
    
    .scroll-container {
        max-height: 80vh;
        overflow-x: auto;
        overflow-y: auto;
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">加成管理</a></li>
                        <li class="breadcrumb-item active">加成統計</li>
                    </ol>
                </div>
                <h4 class="page-title">加成統計</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <!-- 查詢條件 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="d-flex flex-wrap align-items-center" action="{{ route('increase.statistics') }}" method="GET">
                        <div class="me-3">
                            <label for="year" class="form-label">年份</label>
                            <select class="form-control" name="year" id="year">
                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}年</option>
                                @endfor
                            </select>
                        </div>
                        <div class="me-3">
                            <label for="month" class="form-label">月份</label>
                            <select class="form-control" name="month" id="month">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $m }}月</option>
                                @endfor
                            </select>
                        </div>
                        <div class="me-3 mt-3">
                            <button type="submit" class="btn btn-success waves-effect waves-light me-1">
                                <i class="fe-search me-1"></i>查詢
                            </button>
                            <a href="{{ route('increase.index') }}" class="btn btn-secondary waves-effect waves-light me-1">
                                <i class="fe-arrow-left me-1"></i>返回列表
                            </a>
                        </div>
                    </form>
                    <form class="d-flex flex-wrap align-items-center mt-2" action="{{ route('increase.export-combined') }}" method="GET">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <input type="hidden" name="month" value="{{ $month }}">
                        <button type="submit" class="btn btn-info waves-effect waves-light">
                            <i class="fe-download me-1"></i>匯出月報表（含出勤）
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 統計表格 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fe-bar-chart-2 me-2"></i>
                        {{ $year }}年{{ $month }}月 加成統計明細
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                            <thead class="table-light">
                                <tr align="center">
                                    <th scope="col" width="15%">日期</th>
                                    @foreach($users as $user)
                                        <th scope="col">{{ $user->name }}</th>
                                    @endforeach
                                    <th scope="col" width="15%">當日總計</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- 月度總計行 -->
                                <tr align="center" style="color:red;font-weight:500;">
                                    <td>月度總計</td>
                                    @foreach($users as $user)
                                        @php
                                            $monthlyData = $statistics[$user->id]['monthly_total'] ?? null;
                                        @endphp
                                        @if($monthlyData && ($monthlyData['total_amount'] > 0 || $monthlyData['overtime_134_hours'] > 0 || $monthlyData['overtime_167_hours'] > 0))
                                            <td>
                                                @if($monthlyData['total_amount'] > 0)
                                                    <div class="fw-bold text-primary">${{ number_format($monthlyData['total_amount'], 0) }}</div>
                                                @else
                                                    <div class="fw-bold text-primary">$0</div>
                                                @endif
                                                <div class="small">
                                                    @if($monthlyData['phone_amount'] > 0)
                                                        <div class="text-info">電話加成 ${{ number_format($monthlyData['phone_amount'], 0) }}</div>
                                                    @endif
                                                    @if($monthlyData['receive_amount'] > 0)
                                                        <div class="text-success">接件加成 ${{ number_format($monthlyData['receive_amount'], 0) }}</div>
                                                    @endif
                                                    @if($monthlyData['furnace_amount'] > 0)
                                                        <div class="text-secondary">夜間開爐 ${{ number_format($monthlyData['furnace_amount'], 0) }}</div>
                                                    @endif
                                                    @if($monthlyData['overtime_amount'] > 0)
                                                        <div class="text-warning">加班費 ${{ number_format($monthlyData['overtime_amount'], 0) }}</div>
                                                    @endif
                                                </div>
                                                @if($monthlyData['overtime_134_hours'] > 0 || $monthlyData['overtime_167_hours'] > 0)
                                                    <div class="small text-muted">
                                                        @if($monthlyData['overtime_134_hours'] > 0)
                                                            <div class="text-warning">加班費-1.34×{{ number_format($monthlyData['overtime_134_hours'], 1) }}h</div>
                                                        @endif
                                                        @if($monthlyData['overtime_167_hours'] > 0)
                                                            <div class="text-warning">加班費-1.67×{{ number_format($monthlyData['overtime_167_hours'], 1) }}h</div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                        @else
                                            <td>-</td>
                                        @endif
                                    @endforeach
                                    <td>
                                        <div class="fw-bold text-primary">${{ number_format($monthlyTotals['total_amount'], 0) }}</div>
                                        <div class="small">
                                            @if($monthlyTotals['phone_amount'] > 0)
                                                <div class="text-info">電話加成 ${{ number_format($monthlyTotals['phone_amount'], 0) }}</div>
                                            @endif
                                            @if($monthlyTotals['receive_amount'] > 0)
                                                <div class="text-success">接件加成 ${{ number_format($monthlyTotals['receive_amount'], 0) }}</div>
                                            @endif
                                            @if($monthlyTotals['furnace_amount'] > 0)
                                                <div class="text-secondary">夜間開爐 ${{ number_format($monthlyTotals['furnace_amount'], 0) }}</div>
                                            @endif
                                            @if($monthlyTotals['overtime_amount'] > 0)
                                                <div class="text-warning">加班費 ${{ number_format($monthlyTotals['overtime_amount'], 0) }}</div>
                                            @endif
                                        </div>
                                        @if($monthlyTotals['overtime_134_hours'] > 0 || $monthlyTotals['overtime_167_hours'] > 0)
                                            <div class="small text-muted">
                                                @if($monthlyTotals['overtime_134_hours'] > 0)
                                                    加班費-1.34×{{ number_format($monthlyTotals['overtime_134_hours'], 1) }}h
                                                @endif
                                                @if($monthlyTotals['overtime_167_hours'] > 0)
                                                    @if($monthlyTotals['overtime_134_hours'] > 0)<br>@endif
                                                    加班費-1.67×{{ number_format($monthlyTotals['overtime_167_hours'], 1) }}h
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                
                                @foreach($dailyStats as $dateKey => $dayData)
                                    <tr align="center">
                                        <td>{{ date('m/d', strtotime($dateKey)) }}</td>
                                        @foreach($users as $user)
                                            @php
                                                $userData = $dayData['users'][$user->id] ?? null;
                                            @endphp
                                            @if($userData && ($userData['total_amount'] > 0 || $userData['overtime_134_hours'] > 0 || $userData['overtime_167_hours'] > 0))
                                                <td>
                                                    <div class="fw-bold text-primary">${{ number_format($userData['total_amount'], 0) }}</div>
                                                    <div class="small">
                                                        @php
                                                            // 根據人員類型顯示不同的統計
                                                            $isPhonePerson = false;
                                                            $isReceivePerson = false;
                                                            $isFurnacePerson = false;
                                                            $isOvertimePerson = false;
                                                            
                                                            // 判斷人員類型
                                                            if($userData['phone_amount'] > 0) $isPhonePerson = true;
                                                            if($userData['receive_amount'] > 0) $isReceivePerson = true;
                                                            if($userData['furnace_amount'] > 0) $isFurnacePerson = true;
                                                            if($userData['overtime_amount'] > 0) $isOvertimePerson = true;
                                                            
                                                            $categoryCounts = array_count_values($userData['categories']);
                                                        @endphp
                                                        
                                                        @if($isPhonePerson)
                                                            @if($userData['night_phone_amount'] > 0)
                                                                <div class="text-info">夜電×{{ $categoryCounts['夜間'] ?? 1 }} ${{ number_format($userData['night_phone_amount'], 0) }}</div>
                                                            @endif
                                                            @if($userData['evening_phone_amount'] > 0)
                                                                <div class="text-info">晚電×{{ $categoryCounts['晚間'] ?? 1 }} ${{ number_format($userData['evening_phone_amount'], 0) }}</div>
                                                            @endif
                                                            @if($userData['typhoon_phone_amount'] > 0)
                                                                <div class="text-info">颱電×{{ $categoryCounts['颱風'] ?? 1 }} ${{ number_format($userData['typhoon_phone_amount'], 0) }}</div>
                                                            @endif
                                                        @endif
                                                        
                                                        @if($isReceivePerson)
                                                            @if($userData['night_receive_amount'] > 0)
                                                                <div class="text-success">夜間×{{ $categoryCounts['夜間'] ?? 1 }} ${{ number_format($userData['night_receive_amount'], 0) }}</div>
                                                            @endif
                                                            @if($userData['evening_receive_amount'] > 0)
                                                                <div class="text-success">晚間×{{ $categoryCounts['晚間'] ?? 1 }} ${{ number_format($userData['evening_receive_amount'], 0) }}</div>
                                                            @endif
                                                            @if($userData['typhoon_receive_amount'] > 0)
                                                                <div class="text-success">颱風×{{ $categoryCounts['颱風'] ?? 1 }} ${{ number_format($userData['typhoon_receive_amount'], 0) }}</div>
                                                            @endif
                                                        @endif
                                                        
                                                        @if($isFurnacePerson)
                                                            <div class="text-secondary">夜間開爐 ${{ number_format($userData['furnace_amount'], 0) }}</div>
                                                        @endif
                                                        
                                                        @if($userData['overtime_134_hours'] > 0 || $userData['overtime_167_hours'] > 0)
                                                            @if($userData['overtime_134_hours'] > 0)
                                                                <div class="text-warning">加班費-1.34×{{ number_format($userData['overtime_134_hours'], 1) }}h</div>
                                                            @endif
                                                            @if($userData['overtime_167_hours'] > 0)
                                                                <div class="text-warning">加班費-1.67×{{ number_format($userData['overtime_167_hours'], 1) }}h</div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                            @else
                                                <td></td>
                                            @endif
                                        @endforeach
                                        <td>
                                            @php
                                                $dailyTotal = $dayData['daily_total'] ?? null;
                                            @endphp
                                            @if($dailyTotal && $dailyTotal['total_amount'] > 0)
                                                <div class="fw-bold text-primary">${{ number_format($dailyTotal['total_amount'], 0) }}</div>
                                                <div class="small">
                                                    @if($dailyTotal['phone_amount'] > 0)
                                                        <div class="text-info">電話加成 ${{ number_format($dailyTotal['phone_amount'], 0) }}</div>
                                                    @endif
                                                    @if($dailyTotal['receive_amount'] > 0)
                                                        <div class="text-success">接件加成 ${{ number_format($dailyTotal['receive_amount'], 0) }}</div>
                                                    @endif
                                                    @if($dailyTotal['furnace_amount'] > 0)
                                                        <div class="text-secondary">夜間開爐 ${{ number_format($dailyTotal['furnace_amount'], 0) }}</div>
                                                    @endif
                                                    @if($dailyTotal['overtime_amount'] > 0)
                                                        <div class="text-warning">加班費 ${{ number_format($dailyTotal['overtime_amount'], 0) }}</div>
                                                    @endif
                                                </div>
                                                @if($dailyTotal['overtime_134_hours'] > 0 || $dailyTotal['overtime_167_hours'] > 0)
                                                    <div class="small text-muted">
                                                        @if($dailyTotal['overtime_134_hours'] > 0)
                                                            加班費-1.34×{{ number_format($dailyTotal['overtime_134_hours'], 1) }}h
                                                        @endif
                                                        @if($dailyTotal['overtime_167_hours'] > 0)
                                                            @if($dailyTotal['overtime_134_hours'] > 0)<br>@endif
                                                            加班費-1.67×{{ number_format($dailyTotal['overtime_167_hours'], 1) }}h
                                                        @endif
                                                    </div>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 統計摘要 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fe-pie-chart me-2"></i>
                        統計摘要
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="text-white">${{ number_format($monthlyTotals['phone_amount'] + $monthlyTotals['receive_amount'] + $monthlyTotals['furnace_amount'] + $monthlyTotals['overtime_amount'], 0) }}</h4>
                                            <p class="mb-0">月度總金額</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fe-dollar-sign" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="text-white">${{ number_format($monthlyTotals['phone_amount'], 0) }}</h4>
                                            <p class="mb-0">接電話獎金</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fe-phone" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="text-white">${{ number_format($monthlyTotals['receive_amount'], 0) }}</h4>
                                            <p class="mb-0">接件獎金</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fe-package" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-secondary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="text-white">${{ number_format($monthlyTotals['furnace_amount'], 0) }}</h4>
                                            <p class="mb-0">夜間開爐</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fe-zap" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 加班費統計 -->
                    @if($monthlyTotals['overtime_amount'] > 0)
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="text-white">${{ number_format($monthlyTotals['overtime_amount'], 0) }}</h4>
                                                <p class="mb-0">加班費總計</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fe-clock" style="font-size: 2rem;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- 加班小時數統計 -->
                    @if($monthlyTotals['overtime_134_hours'] > 0 || $monthlyTotals['overtime_167_hours'] > 0)
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="text-primary">{{ number_format($monthlyTotals['overtime_134_hours'], 1) }} 小時</h5>
                                                <p class="mb-0 text-muted">1.34倍加班時數</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fe-clock text-primary" style="font-size: 1.5rem;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="text-success">{{ number_format($monthlyTotals['overtime_167_hours'], 1) }} 小時</h5>
                                                <p class="mb-0 text-muted">1.67倍加班時數</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fe-clock text-success" style="font-size: 1.5rem;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div> <!-- container -->
@endsection

@section('script')
<script>
    // 自動調整表格高度
    function adjustTableHeight() {
        const container = document.querySelector('.scroll-container');
        const windowHeight = window.innerHeight;
        const headerHeight = document.querySelector('.page-title-box').offsetHeight;
        const filterHeight = document.querySelector('.card:first-of-type').offsetHeight;
        const summaryHeight = document.querySelector('.card:last-of-type').offsetHeight;
        const padding = 100; // 額外的padding
        
        const availableHeight = windowHeight - headerHeight - filterHeight - summaryHeight - padding;
        container.style.maxHeight = Math.max(availableHeight, 300) + 'px';
    }
    
    // 頁面載入時調整高度
    document.addEventListener('DOMContentLoaded', function() {
        adjustTableHeight();
    });
    
    // 視窗大小改變時調整高度
    window.addEventListener('resize', function() {
        adjustTableHeight();
    });
</script>
@endsection

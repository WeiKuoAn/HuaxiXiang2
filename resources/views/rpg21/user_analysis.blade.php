@extends('layouts.vertical', ["page_title"=> "專員業績分析 - " . $user->name])

@section('css')
<style>
    .stat-card {
        border-left: 4px solid;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .growth-positive {
        color: #10b981;
        font-weight: bold;
    }
    .growth-negative {
        color: #ef4444;
        font-weight: bold;
    }
    .chart-container {
        position: relative;
        height: 400px;
    }
    .trend-icon {
        font-size: 1.5rem;
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
                        <li class="breadcrumb-item"><a href="{{ route('rpg21') }}">專員年度業務金額統計</a></li>
                        <li class="breadcrumb-item active">{{ $user->name }}</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-account-chart me-2"></i>
                    {{ $user->name }} - 年度業績分析
                </h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <!-- 年度選擇 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-auto">
                            <form class="d-flex align-items-center" method="GET">
                                <label for="year-select" class="me-2 mb-0">年度</label>
                                <select class="form-select me-2" id="year-select" name="year" onchange="this.form.submit()" style="width: auto;">
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" @if($request->year == $year) selected @endif>{{ $year }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-success waves-effect waves-light">
                                    <i class="fe-search me-1"></i>搜尋
                                </button>
                            </form>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('rpg21', ['year' => $currentYear]) }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left me-1"></i>返回總覽
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 關鍵指標卡片 -->
    <div class="row">
        <div class="col-md-3">
            <div class="card stat-card" style="border-left-color: #3b82f6;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">年度總案件數</p>
                            <h3 class="mb-0">{{ number_format($total_count) }}</h3>
                            <small class="text-muted">已完成案件</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-primary rounded">
                                <i class="mdi mdi-file-document-multiple font-24 text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card" style="border-left-color: #10b981;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">年度總業績</p>
                            <h3 class="mb-0">${{ number_format($total_amount, 0) }}</h3>
                            <small class="text-muted">業務營收</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-success rounded">
                                <i class="mdi mdi-currency-usd font-24 text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card" style="border-left-color: #f59e0b;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">月平均案件數</p>
                            <h3 class="mb-0">{{ number_format($total_count / 12, 1) }}</h3>
                            <small class="text-muted">每月平均</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-warning rounded">
                                <i class="mdi mdi-chart-line font-24 text-warning"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card" style="border-left-color: #8b5cf6;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">月平均業績</p>
                            <h3 class="mb-0">${{ number_format($total_amount / 12, 0) }}</h3>
                            <small class="text-muted">每月平均</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-purple rounded">
                                <i class="mdi mdi-cash-multiple font-24 text-purple"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 趨勢圖表 -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-chart-bar me-1"></i>
                        案件數趨勢
                    </h5>
                    <div class="chart-container">
                        <canvas id="countChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-chart-line me-1"></i>
                        業績金額趨勢
                    </h5>
                    <div class="chart-container">
                        <canvas id="amountChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 月比月成長分析 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-trending-up me-1"></i>
                        月比月成長分析
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr align="center">
                                    <th style="width: 150px;">項目</th>
                                    @foreach($monthly_data as $key => $data)
                                        @if($key != '01')
                                            <th>{{ $data['month'] }}</th>
                                        @endif
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr align="center">
                                    <td class="text-start"><strong>案件數成長率</strong></td>
                                    @php
                                        $prev_count = $monthly_data['01']['count'];
                                    @endphp
                                    @foreach($monthly_data as $key => $data)
                                        @if($key != '01')
                                            @php
                                                $current_count = $data['count'];
                                                $growth = $prev_count > 0 ? (($current_count - $prev_count) / $prev_count) * 100 : 0;
                                            @endphp
                                            <td>
                                                @if($growth > 0)
                                                    <span class="growth-positive">
                                                        <i class="mdi mdi-arrow-up-bold"></i>
                                                        +{{ number_format($growth, 1) }}%
                                                    </span>
                                                @elseif($growth < 0)
                                                    <span class="growth-negative">
                                                        <i class="mdi mdi-arrow-down-bold"></i>
                                                        {{ number_format($growth, 1) }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted">0%</span>
                                                @endif
                                                <br>
                                                <small class="text-muted">({{ $prev_count }}→{{ $current_count }})</small>
                                            </td>
                                            @php
                                                $prev_count = $current_count;
                                            @endphp
                                        @endif
                                    @endforeach
                                </tr>
                                <tr align="center">
                                    <td class="text-start"><strong>業績成長率</strong></td>
                                    @php
                                        $prev_amount = $monthly_data['01']['amount'];
                                    @endphp
                                    @foreach($monthly_data as $key => $data)
                                        @if($key != '01')
                                            @php
                                                $current_amount = $data['amount'];
                                                $growth = $prev_amount > 0 ? (($current_amount - $prev_amount) / $prev_amount) * 100 : 0;
                                            @endphp
                                            <td>
                                                @if($growth > 0)
                                                    <span class="growth-positive">
                                                        <i class="mdi mdi-arrow-up-bold"></i>
                                                        +{{ number_format($growth, 1) }}%
                                                    </span>
                                                @elseif($growth < 0)
                                                    <span class="growth-negative">
                                                        <i class="mdi mdi-arrow-down-bold"></i>
                                                        {{ number_format($growth, 1) }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted">0%</span>
                                                @endif
                                                <br>
                                                <small class="text-muted">(${{ number_format($prev_amount, 0) }}→${{ number_format($current_amount, 0) }})</small>
                                            </td>
                                            @php
                                                $prev_amount = $current_amount;
                                            @endphp
                                        @endif
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 詳細月度數據 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-table-large me-1"></i>
                        月度詳細數據
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-centered table-striped table-hover">
                            <thead class="table-light">
                                <tr align="center">
                                    <th>月份</th>
                                    <th>案件數</th>
                                    <th>業績金額</th>
                                    <th>平均單價</th>
                                    <th>與上月比較</th>
                                </tr>
                            </thead>
                            <tbody align="center">
                                @php
                                    $prev_count = 0;
                                    $prev_amount = 0;
                                @endphp
                                @foreach($monthly_data as $key => $data)
                                <tr>
                                    <td><strong>{{ $data['month'] }}</strong></td>
                                    <td>
                                        <span class="badge bg-primary">{{ number_format($data['count']) }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-success">${{ number_format($data['amount'], 0) }}</strong>
                                    </td>
                                    <td>
                                        @if($data['count'] > 0)
                                            <span class="text-muted">${{ number_format($data['amount'] / $data['count'], 0) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($prev_count > 0 || $prev_amount > 0)
                                            @php
                                                $count_change = $data['count'] - $prev_count;
                                                $amount_change = $data['amount'] - $prev_amount;
                                            @endphp
                                            <div>
                                                案件：
                                                @if($count_change > 0)
                                                    <span class="growth-positive">+{{ $count_change }}</span>
                                                @elseif($count_change < 0)
                                                    <span class="growth-negative">{{ $count_change }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </div>
                                            <div>
                                                業績：
                                                @if($amount_change > 0)
                                                    <span class="growth-positive">+${{ number_format($amount_change, 0) }}</span>
                                                @elseif($amount_change < 0)
                                                    <span class="growth-negative">-${{ number_format(abs($amount_change), 0) }}</span>
                                                @else
                                                    <span class="text-muted">$0</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @php
                                    $prev_count = $data['count'];
                                    $prev_amount = $data['amount'];
                                @endphp
                                @endforeach
                                <tr class="table-active fw-bold">
                                    <td>總計</td>
                                    <td>
                                        <span class="badge bg-primary">{{ number_format($total_count) }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-success">${{ number_format($total_amount, 0) }}</strong>
                                    </td>
                                    <td>
                                        @if($total_count > 0)
                                            <span class="text-muted">${{ number_format($total_amount / $total_count, 0) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 業績分析 -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-poll me-1"></i>
                        業績表現分析
                    </h5>
                    @php
                        $max_count_month = collect($monthly_data)->sortByDesc('count')->first();
                        $min_count_month = collect($monthly_data)->where('count', '>', 0)->sortBy('count')->first();
                        $max_amount_month = collect($monthly_data)->sortByDesc('amount')->first();
                        $min_amount_month = collect($monthly_data)->where('amount', '>', 0)->sortBy('amount')->first();
                    @endphp
                    
                    <div class="mb-3">
                        <h6 class="text-muted">📈 最佳表現月份（案件數）</h6>
                        <p class="mb-1">
                            <strong class="text-primary">{{ $max_count_month['month'] }}</strong> - 
                            <span class="badge bg-primary">{{ $max_count_month['count'] }} 件</span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted">📉 最低表現月份（案件數）</h6>
                        <p class="mb-1">
                            @if($min_count_month)
                                <strong class="text-warning">{{ $min_count_month['month'] }}</strong> - 
                                <span class="badge bg-warning">{{ $min_count_month['count'] }} 件</span>
                            @else
                                <span class="text-muted">無資料</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted">💰 最高業績月份</h6>
                        <p class="mb-1">
                            <strong class="text-success">{{ $max_amount_month['month'] }}</strong> - 
                            <strong>${{ number_format($max_amount_month['amount'], 0) }}</strong>
                        </p>
                    </div>

                    <div class="mb-0">
                        <h6 class="text-muted">💸 最低業績月份</h6>
                        <p class="mb-0">
                            @if($min_amount_month)
                                <strong class="text-danger">{{ $min_amount_month['month'] }}</strong> - 
                                <strong>${{ number_format($min_amount_month['amount'], 0) }}</strong>
                            @else
                                <span class="text-muted">無資料</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-lightbulb-on-outline me-1"></i>
                        業績洞察
                    </h5>
                    
                    @php
                        // 計算整體趨勢
                        $first_half_count = array_sum(array_slice(array_column($monthly_data, 'count'), 0, 6));
                        $second_half_count = array_sum(array_slice(array_column($monthly_data, 'count'), 6, 6));
                        $first_half_amount = array_sum(array_slice(array_column($monthly_data, 'amount'), 0, 6));
                        $second_half_amount = array_sum(array_slice(array_column($monthly_data, 'amount'), 6, 6));
                        
                        $count_trend = $second_half_count > $first_half_count ? 'up' : ($second_half_count < $first_half_count ? 'down' : 'stable');
                        $amount_trend = $second_half_amount > $first_half_amount ? 'up' : ($second_half_amount < $first_half_amount ? 'down' : 'stable');
                    @endphp

                    <div class="mb-3">
                        <h6 class="text-muted">📊 年度整體趨勢</h6>
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2">案件數：</span>
                            @if($count_trend == 'up')
                                <span class="growth-positive trend-icon">
                                    <i class="mdi mdi-trending-up"></i> 上升趨勢
                                </span>
                            @elseif($count_trend == 'down')
                                <span class="growth-negative trend-icon">
                                    <i class="mdi mdi-trending-down"></i> 下降趨勢
                                </span>
                            @else
                                <span class="text-muted trend-icon">
                                    <i class="mdi mdi-trending-neutral"></i> 持平
                                </span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="me-2">業績：</span>
                            @if($amount_trend == 'up')
                                <span class="growth-positive trend-icon">
                                    <i class="mdi mdi-trending-up"></i> 上升趨勢
                                </span>
                            @elseif($amount_trend == 'down')
                                <span class="growth-negative trend-icon">
                                    <i class="mdi mdi-trending-down"></i> 下降趨勢
                                </span>
                            @else
                                <span class="text-muted trend-icon">
                                    <i class="mdi mdi-trending-neutral"></i> 持平
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted">📅 上半年 vs 下半年</h6>
                        <table class="table table-sm table-borderless">
                            <tbody>
                                <tr>
                                    <td>上半年案件數：</td>
                                    <td><strong>{{ number_format($first_half_count) }}</strong></td>
                                    <td>下半年案件數：</td>
                                    <td><strong>{{ number_format($second_half_count) }}</strong></td>
                                </tr>
                                <tr>
                                    <td>上半年業績：</td>
                                    <td><strong>${{ number_format($first_half_amount, 0) }}</strong></td>
                                    <td>下半年業績：</td>
                                    <td><strong>${{ number_format($second_half_amount, 0) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mb-0">
                        <h6 class="alert-heading">
                            <i class="mdi mdi-information-outline me-1"></i>
                            專員資訊
                        </h6>
                        <p class="mb-0"><strong>專員姓名：</strong>{{ $user->name }}</p>
                        <p class="mb-0"><strong>統計年度：</strong>{{ $currentYear }}</p>
                        <p class="mb-0"><strong>年度排名：</strong><span class="text-muted">可依總業績排序查看</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div> <!-- container -->
@endsection

@section('script')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
$(document).ready(function() {
    // 準備圖表資料
    const monthLabels = [@foreach($monthly_data as $data)'{{ $data['month'] }}',@endforeach];
    const countData = [@foreach($monthly_data as $data){{ $data['count'] }},@endforeach];
    const amountData = [@foreach($monthly_data as $data){{ $data['amount'] }},@endforeach];

    // 案件數趨勢圖
    const countCtx = document.getElementById('countChart').getContext('2d');
    const countChart = new Chart(countCtx, {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: '案件數',
                data: countData,
                backgroundColor: 'rgba(59, 130, 246, 0.6)',
                borderColor: '#3b82f6',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: '{{ $currentYear }} 年度 {{ $user->name }} 案件數趨勢',
                    font: { size: 14 }
                },
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '案件數: ' + context.parsed.y.toLocaleString() + ' 件';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // 業績金額趨勢圖
    const amountCtx = document.getElementById('amountChart').getContext('2d');
    const amountChart = new Chart(amountCtx, {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: '業績金額',
                data: amountData,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: '{{ $currentYear }} 年度 {{ $user->name }} 業績趨勢',
                    font: { size: 14 }
                },
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '業績: $' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection


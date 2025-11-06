@extends('layouts.vertical', ["page_title"=> "年度後續服務統計"])

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
    }
    .growth-negative {
        color: #ef4444;
    }
    .chart-container {
        position: relative;
        height: 400px;
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">報表管理</a></li>
                        <li class="breadcrumb-item active">年度後續服務統計</li>
                    </ol>
                </div>
                <h4 class="page-title">年度後續服務統計</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <!-- 年度選擇 -->
    {{-- <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg16') }}" method="GET">
                                <label for="status-select" class="me-2">年度</label>
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="year" onchange="this.form.submit()">
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" @if($request->year == $year) selected @endif>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="me-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                </div>
                            </form>
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
                            <h3 class="mb-0">{{ number_format($total_summary['count']) }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-primary rounded">
                                <i class="mdi mdi-file-document-multiple font-24 text-primary"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">{{ $search_year }} 年度</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card" style="border-left-color: #10b981;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">年度總金額</p>
                            <h3 class="mb-0">${{ number_format($total_summary['amount'], 0) }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-success rounded">
                                <i class="mdi mdi-currency-usd font-24 text-success"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">後續服務營收</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card" style="border-left-color: #f59e0b;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">月平均案件數</p>
                            <h3 class="mb-0">{{ number_format($total_summary['count'] / 12, 1) }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-warning rounded">
                                <i class="mdi mdi-chart-line font-24 text-warning"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">每月平均</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card" style="border-left-color: #8b5cf6;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">月平均金額</p>
                            <h3 class="mb-0">${{ number_format($total_summary['amount'] / 12, 0) }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-purple rounded">
                                <i class="mdi mdi-cash-multiple font-24 text-purple"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">每月平均</small>
                </div>
            </div>
        </div>
    </div>

    <!-- 趨勢圖表 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-chart-line me-1"></i>
                        月度業績趨勢
                    </h5>
                    <div class="chart-container">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- 詳細統計表格 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-table me-1"></i>
                        後續服務明細統計
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                            <thead class="table-light">
                                <tr align="center">
                                    <th scope="col" rowspan="2" style="vertical-align: middle;">後續服務</th>
                                    @foreach($months as $key=>$month)
                                        <th scope="col">{{ $month['month'] }}</th>
                                    @endforeach
                                    <th scope="col">總計</th>
                                </tr>
                                <tr align="center" class="table-light">
                                    @for($i = 0; $i < 12; $i++)
                                        <th style="font-size: 0.75rem;">次數</th>
                                        {{-- <th style="font-size: 0.75rem;">金額</th> --}}
                                    @endfor
                                    <th style="font-size: 0.75rem;">次數</th>
                                    {{-- <th style="font-size: 0.75rem;">金額</th> --}}
                                </tr>
                            </thead>
                            <tbody align="center">
                                @foreach($proms as $prom)
                                    <tr>
                                        <td class="text-start">
                                            <a href="{{ route('rpg16.service.analysis', ['prom_id' => $prom->id, 'year' => $search_year]) }}" 
                                               class="text-dark text-decoration-none">
                                                <i class="mdi mdi-chart-line me-1"></i>
                                                <strong>{{ $prom->name }}</strong>
                                                <i class="mdi mdi-chevron-right text-muted"></i>
                                            </a>
                                        </td>
                                        @foreach($datas as $key=>$data)
                                            <td>
                                                @if($data['proms'][$prom->id]['count'] > 0)
                                                    <a href="{{ route('rpg16.detail',['year'=>$request->year,'month'=>$key,'prom_id'=>$prom->id]) }}">
                                                        {{ $data['proms'][$prom->id]['count'] }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            {{-- <td>
                                                @if($data['proms'][$prom->id]['amount'] > 0)
                                                    <span class="text-primary">${{ number_format($data['proms'][$prom->id]['amount'], 0) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td> --}}
                                        @endforeach
                                        <td><strong>{{ $sums[$prom->id]['count'] }}</strong></td>
                                    </tr>
                                @endforeach
                                <tr class="table-active fw-bold">
                                    <td class="text-start">月度小計</td>
                                    @foreach($datas as $data)
                                        <td class="text-primary">{{ number_format($data['total_count']) }}</td>
                                    @endforeach
                                    <td class="text-primary">{{ number_format($total_summary['count']) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <!-- 月比月成長分析 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-trending-up me-1"></i>
                        月比月成長分析
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr align="center">
                                    <th>月份</th>
                                    @foreach($months as $key=>$month)
                                        @if($key != '01')
                                            <th>{{ $month['month'] }}</th>
                                        @endif
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr align="center">
                                    <td class="text-start"><strong>案件成長率</strong></td>
                                    @php
                                        $prevCount = $datas['01']['total_count'];
                                    @endphp
                                    @foreach($months as $key=>$month)
                                        @if($key != '01')
                                            @php
                                                $currentCount = $datas[$key]['total_count'];
                                                $growth = $prevCount > 0 ? (($currentCount - $prevCount) / $prevCount) * 100 : 0;
                                                $prevCount = $currentCount;
                                            @endphp
                                            <td>
                                                @if($growth > 0)
                                                    <span class="growth-positive">
                                                        <i class="mdi mdi-arrow-up-bold"></i>
                                                        {{ number_format($growth, 1) }}%
                                                    </span>
                                                @elseif($growth < 0)
                                                    <span class="growth-negative">
                                                        <i class="mdi mdi-arrow-down-bold"></i>
                                                        {{ number_format(abs($growth), 1) }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted">0%</span>
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                                <tr align="center">
                                    <td class="text-start"><strong>金額成長率</strong></td>
                                    @php
                                        $prevAmount = $datas['01']['total_amount'];
                                    @endphp
                                    @foreach($months as $key=>$month)
                                        @if($key != '01')
                                            @php
                                                $currentAmount = $datas[$key]['total_amount'];
                                                $growth = $prevAmount > 0 ? (($currentAmount - $prevAmount) / $prevAmount) * 100 : 0;
                                                $prevAmount = $currentAmount;
                                            @endphp
                                            <td>
                                                @if($growth > 0)
                                                    <span class="growth-positive">
                                                        <i class="mdi mdi-arrow-up-bold"></i>
                                                        {{ number_format($growth, 1) }}%
                                                    </span>
                                                @elseif($growth < 0)
                                                    <span class="growth-negative">
                                                        <i class="mdi mdi-arrow-down-bold"></i>
                                                        {{ number_format(abs($growth), 1) }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted">0%</span>
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

</div> <!-- container -->
@endsection

@section('script')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
$(document).ready(function() {
    // 準備圖表資料
    const monthLabels = [@foreach($months as $month)'{{ $month['month'] }}',@endforeach];
    const countData = [@foreach($datas as $data){{ $data['total_count'] }},@endforeach];
    const amountData = [@foreach($datas as $data){{ $data['total_amount'] }},@endforeach];

    // 建立雙軸折線圖
    const ctx = document.getElementById('trendChart').getContext('2d');
    const trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: '案件數',
                data: countData,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                yAxisID: 'y',
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointHoverRadius: 7
            }, {
                label: '金額',
                data: amountData,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                yAxisID: 'y1',
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                title: {
                    display: true,
                    text: '{{ $search_year }} 年度後續服務業績趨勢',
                    font: {
                        size: 16
                    }
                },
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.dataset.label === '金額') {
                                label += '$' + context.parsed.y.toLocaleString();
                            } else {
                                label += context.parsed.y.toLocaleString();
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: '案件數'
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: '金額 ($)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
});
</script>
@endsection

@extends('layouts.vertical', ['page_title' => $prom->name . ' - 每月數據比較'])

@section('css')
    <style>
        .month-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .month-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        .month-header {
            font-size: 18px;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }

        .metric-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .metric-item:last-child {
            border-bottom: none;
        }

        .metric-label {
            color: #6b7280;
            font-size: 14px;
        }

        .metric-value {
            font-weight: 600;
            font-size: 16px;
            color: #111827;
        }

        .metric-value.positive {
            color: #10b981;
        }

        .metric-value.negative {
            color: #ef4444;
        }

        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .source-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 8px;
        }

        .source-badge.taobao {
            background: #fee2e2;
            color: #dc2626;
        }

        .source-badge.manufacturer {
            background: #dbeafe;
            color: #2563eb;
        }

        .source-badge.unknown {
            background: #f3f4f6;
            color: #6b7280;
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
                            <li class="breadcrumb-item"><a href="{{ route('rpg37') }}">決策支援分析儀表板</a></li>
                            <li class="breadcrumb-item active">{{ $prom->name }} - 每月數據比較</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ $prom->name }} - 每月數據比較</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- 年份選擇 -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg37.detail', $prom->id) }}" method="GET">
                            <label for="year-select" class="me-2">選擇年份</label>
                            <div class="me-3">
                                <select class="form-select" id="year-select" name="year" onchange="this.form.submit()">
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" @if($year == $currentYear) selected @endif>{{ $year }}年</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="me-3">
                                <a href="{{ route('rpg37') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left me-1"></i>返回列表
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- 總計卡片 -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">總銷量</h6>
                        <h3 class="mb-0 text-primary">{{ number_format($totals['total_volume']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">總營收</h6>
                        <h3 class="mb-0 text-success">${{ number_format($totals['total_revenue']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">總利潤</h6>
                        <h3 class="mb-0 text-info">${{ number_format($totals['total_profit']) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- 每月數據圖表 -->
        <div class="row">
            <div class="col-12">
                <div class="chart-container">
                    <h5 class="mb-4">{{ $currentYear }}年每月數據趨勢</h5>
                    <div style="position: relative; height: 400px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 每月詳細數據 -->
        <div class="row">
            @foreach($monthlyData as $monthData)
                <div class="col-md-6 col-lg-4">
                    <div class="month-card">
                        <div class="month-header">
                            {{ $monthData['month_num'] }}月 ({{ $monthData['month'] }})
                        </div>
                        <div class="metric-item">
                            <span class="metric-label">銷量</span>
                            <span class="metric-value">{{ number_format($monthData['volume']) }}</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-label">營收</span>
                            <span class="metric-value">${{ number_format($monthData['revenue']) }}</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-label">利潤</span>
                            <span class="metric-value {{ $monthData['profit'] >= 0 ? 'positive' : 'negative' }}">
                                ${{ number_format($monthData['profit']) }}
                            </span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-label">平均單價</span>
                            <span class="metric-value">
                                ${{ number_format($monthData['avg_price'], 0) }}
                            </span>
                        </div>
                        @if(isset($monthlySourceData[$monthData['month']]) && $monthlySourceData[$monthData['month']]->count() > 0)
                            <div class="metric-item mt-3 pt-3" style="border-top: 2px solid #e5e7eb;">
                                <span class="metric-label">來源分布</span>
                                <div class="mt-2">
                                    @foreach($monthlySourceData[$monthData['month']] as $sourceItem)
                                        @php
                                            $sourceName = $sourceItem->source ?? '未知來源';
                                            $isTaobao = stripos($sourceName, '淘寶') !== false;
                                            $isManufacturer = stripos($sourceName, '廠商') !== false;
                                        @endphp
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            @if($isTaobao)
                                                <span class="source-badge taobao">{{ $sourceName }}</span>
                                            @elseif($isManufacturer)
                                                <span class="source-badge manufacturer">{{ $sourceName }}</span>
                                            @else
                                                <span class="source-badge unknown">{{ $sourceName }}</span>
                                            @endif
                                            <small class="text-muted">
                                                銷量: {{ $sourceItem->volume }} | 
                                                營收: ${{ number_format($sourceItem->revenue) }}
                                            </small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('script')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const monthlyData = @json($monthlyData);
            
            if (monthlyData && monthlyData.length > 0) {
                const ctx = document.getElementById('monthlyChart');
                if (ctx) {
                    const monthNames = ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'];
                    
                    new Chart(ctx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: monthlyData.map(d => monthNames[d.month_num - 1]),
                            datasets: [{
                                label: '銷量',
                                data: monthlyData.map(d => d.volume),
                                borderColor: '#667eea',
                                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                                yAxisID: 'y',
                                tension: 0.4,
                                fill: true
                            }, {
                                label: '營收',
                                data: monthlyData.map(d => d.revenue),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                yAxisID: 'y1',
                                tension: 0.4,
                                fill: true
                            }, {
                                label: '利潤',
                                data: monthlyData.map(d => d.profit),
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                yAxisID: 'y1',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            scales: {
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    title: {
                                        display: true,
                                        text: '銷量'
                                    }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    title: {
                                        display: true,
                                        text: '金額'
                                    },
                                    grid: {
                                        drawOnChartArea: false,
                                    },
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.dataset.label || '';
                                            let value = context.parsed.y;
                                            
                                            if (label === '銷量') {
                                                return label + ': ' + value.toLocaleString();
                                            } else {
                                                return label + ': $' + value.toLocaleString();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
    </script>
@endsection


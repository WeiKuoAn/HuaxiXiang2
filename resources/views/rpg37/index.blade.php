@extends('layouts.vertical', ['page_title' => '決策支援分析儀表板'])

@section('css')
    <style>
        /* ========== KPI 卡片樣式 ========== */
        .kpi-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .kpi-card.revenue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .kpi-card.profit {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .kpi-card.growth {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .kpi-label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 10px;
        }

        .kpi-value {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .kpi-change {
            font-size: 14px;
            opacity: 0.8;
        }

        /* ========== 圖表容器 ========== */
        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* ========== 產品表格樣式 ========== */
        .product-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .product-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .product-table thead th {
            border: none;
            padding: 15px;
            font-weight: 600;
        }

        .product-table tbody tr {
            transition: all 0.2s;
        }

        .product-table tbody tr:hover {
            background: #f8f9fa;
        }

        .product-table tbody td {
            padding: 15px;
            vertical-align: middle;
        }

        .source-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
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

        /* ========== 懸浮比較列 ========== */
        .floating-comparison-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.15);
            padding: 20px;
            z-index: 1000;
            display: none;
            border-top: 3px solid #667eea;
        }

        .floating-comparison-bar.show {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .comparison-count {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .comparison-count span {
            color: #667eea;
            font-size: 20px;
        }

        /* ========== 比較模態視窗 ========== */
        .comparison-card {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .comparison-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .comparison-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .comparison-item:last-child {
            border-bottom: none;
        }

        .comparison-label {
            font-weight: 600;
            color: #6b7280;
        }

        .comparison-value {
            font-size: 18px;
            font-weight: bold;
        }

        .comparison-value.winner {
            color: #10b981;
            font-weight: 900;
        }

        .comparison-conclusion {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 16px;
            font-weight: 600;
            color: #065f46;
        }

        /* ========== 響應式設計 ========== */
        @media (max-width: 768px) {
            .kpi-value {
                font-size: 24px;
            }
            
            .floating-comparison-bar {
                flex-direction: column;
                gap: 15px;
            }
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
                            <li class="breadcrumb-item active">決策支援分析儀表板</li>
                        </ol>
                    </div>
                    <h4 class="page-title">決策支援分析儀表板</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- 日期篩選 -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg37') }}" method="GET">
                            <label for="after_date" class="me-2">日期區間</label>
                            <div class="me-2">
                                <input type="date" class="form-control" name="after_date" id="after_date"
                                    value="{{ $request->after_date ?? $firstDay->format('Y-m-d') }}">
                            </div>
                            <label class="me-2">至</label>
                            <div class="me-3">
                                <input type="date" class="form-control" name="before_date" id="before_date"
                                    value="{{ $request->before_date ?? $lastDay->format('Y-m-d') }}">
                            </div>
                            <div class="me-3">
                                <button type="submit" class="btn btn-success waves-effect waves-light">
                                    <i class="fe-search me-1"></i>搜尋
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 卡片 -->
        <div class="row">
            <div class="col-md-4">
                <div class="kpi-card revenue">
                    <div class="kpi-label">總營業額</div>
                    <div class="kpi-value">${{ number_format($kpiData['total_revenue']) }}</div>
                    <div class="kpi-change">本期總收入</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card profit">
                    <div class="kpi-label">總利潤</div>
                    <div class="kpi-value">${{ number_format($kpiData['total_profit']) }}</div>
                    <div class="kpi-change">淨利潤</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card growth">
                    <div class="kpi-label">本月成長率</div>
                    <div class="kpi-value">{{ number_format($kpiData['growth_rate'], 1) }}%</div>
                    <div class="kpi-change">
                        @if($kpiData['growth_rate'] > 0)
                            <i class="mdi mdi-trending-up"></i> 成長
                        @elseif($kpiData['growth_rate'] < 0)
                            <i class="mdi mdi-trending-down"></i> 下降
                        @else
                            <i class="mdi mdi-trending-neutral"></i> 持平
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 趨勢圖表 -->
        <div class="row">
            <div class="col-12">
                <div class="chart-container">
                    <h5 class="mb-4">趨勢數據（銷售總量 vs 銷售總金額）</h5>
                    <div style="position: relative; height: 400px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 來源分析與衝量 vs 衝漂亮 -->
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="mb-4">來源分析</h5>
                    <div style="position: relative; height: 400px;">
                        <canvas id="sourceChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="mb-4">衝量 vs 衝漂亮</h5>
                    <div style="position: relative; height: 400px;">
                        <canvas id="volumeVsProfitChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 產品列表表格 -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-4">詳細產品分析表</h5>
                        <div class="table-responsive">
                            <table class="table product-table">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                                        </th>
                                        <th>產品名稱</th>
                                        <th>來源</th>
                                        <th>銷量</th>
                                        <th>平均單價</th>
                                        <th>總利潤</th>
                                        <th>毛利率</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($productList as $product)
                                        <tr>
                                            <td>
                                                <input type="checkbox" 
                                                       class="product-checkbox" 
                                                       data-item='@json($product)'
                                                       onchange="updateComparisonBar()">
                                            </td>
                                            <td>
                                                <a href="{{ route('rpg37.detail', $product['prom_id']) }}" 
                                                   class="text-primary fw-semibold" 
                                                   style="text-decoration: none;">
                                                    {{ $product['product_name'] }}
                                                    <i class="mdi mdi-arrow-right ms-1"></i>
                                                </a>
                                            </td>
                                            <td>
                                                @php
                                                    $sourceName = $product['source'] ?? '未知來源';
                                                    // 根據來源名稱判斷顏色（可以根據實際來源調整）
                                                    $isTaobao = stripos($sourceName, '淘寶') !== false || stripos($sourceName, 'taobao') !== false;
                                                    $isManufacturer = stripos($sourceName, '廠商') !== false || stripos($sourceName, 'manufacturer') !== false;
                                                @endphp
                                                @if($isTaobao)
                                                    <span class="source-badge taobao">{{ $sourceName }}</span>
                                                @elseif($isManufacturer)
                                                    <span class="source-badge manufacturer">{{ $sourceName }}</span>
                                                @else
                                                    <span class="source-badge unknown">{{ $sourceName }}</span>
                                                @endif
                                            </td>
                                            <td>{{ number_format($product['total_volume']) }}</td>
                                            <td>${{ number_format($product['avg_price'], 0) }}</td>
                                            <td>${{ number_format($product['total_profit'], 0) }}</td>
                                            <td>{{ number_format($product['margin_rate'], 1) }}%</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <p class="text-muted">目前沒有數據，請選擇日期範圍進行查詢</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 懸浮比較列 -->
    <div class="floating-comparison-bar" id="comparisonBar">
        <div class="comparison-count">
            已選擇 <span id="selectedCount">0</span> 個項目
        </div>
        <button type="button" class="btn btn-primary btn-lg" onclick="showComparisonModal()">
            <i class="mdi mdi-chart-line"></i> 進行比較分析
        </button>
    </div>

    <!-- 比較模態視窗 -->
    <div class="modal fade" id="comparisonModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">產品比較分析</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="comparisonContent">
                    <!-- 動態生成比較內容 -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // ========== 全局變數 ==========
        let selectedProducts = [];
        let trendChart, sourceChart, volumeVsProfitChart;

        // ========== 等待 DOM 和 Chart.js 載入完成 ==========
        function waitForChartJS(callback, maxAttempts = 10) {
            let attempts = 0;
            const checkChart = setInterval(function() {
                attempts++;
                if (typeof Chart !== 'undefined') {
                    clearInterval(checkChart);
                    callback();
                } else if (attempts >= maxAttempts) {
                    clearInterval(checkChart);
                    console.error('Chart.js 載入超時');
                    // 即使 Chart.js 未載入，也嘗試初始化（可能使用本地版本）
                    callback();
                }
            }, 100);
        }

        document.addEventListener('DOMContentLoaded', function() {
            waitForChartJS(function() {
                setTimeout(function() {
                    initializeCharts();
                }, 100);
            });
        });

        function initializeCharts() {
            // ========== 趨勢圖表 ==========
            const trendCtx = document.getElementById('trendChart');
            if (!trendCtx) {
                console.error('找不到 trendChart 元素');
                return;
            }

            const trendData = @json($trendData ?? []);
            
            if (trendData && trendData.length > 0) {
                trendChart = new Chart(trendCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: trendData.map(d => d.month || ''),
                        datasets: [{
                            label: '銷售總量',
                            data: trendData.map(d => parseFloat(d.volume) || 0),
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            yAxisID: 'y',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: '銷售總金額',
                            data: trendData.map(d => parseFloat(d.revenue) || 0),
                            borderColor: '#f5576c',
                            backgroundColor: 'rgba(245, 87, 108, 0.1)',
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
                                    text: '銷售總量'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: '銷售總金額'
                                },
                                grid: {
                                    drawOnChartArea: false,
                                },
                            }
                        }
                    }
                });
            } else {
                // 顯示空狀態
                trendCtx.parentElement.innerHTML = '<div class="text-center py-5"><p class="text-muted">暫無趨勢數據</p></div>';
            }

            // ========== 來源分析圖表 ==========
            const sourceCtx = document.getElementById('sourceChart');
            if (!sourceCtx) {
                console.error('找不到 sourceChart 元素');
                return;
            }

            const sourceData = @json($sourceAnalysis ?? []);
            
            if (sourceData && sourceData.length > 0) {
                sourceChart = new Chart(sourceCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: sourceData.map(d => d.source || '未知'),
                        datasets: [{
                            data: sourceData.map(d => parseFloat(d.revenue) || 0),
                            backgroundColor: [
                                '#ef4444',
                                '#3b82f6',
                                '#6b7280',
                                '#10b981',
                                '#f59e0b'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = '$' + context.parsed.toLocaleString();
                                        const data = sourceData[context.dataIndex];
                                        return [
                                            label + ': ' + value,
                                            '銷量: ' + (data.volume || 0),
                                            '利潤: $' + (data.profit || 0).toLocaleString()
                                        ];
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                // 顯示空狀態
                sourceCtx.parentElement.innerHTML = '<div class="text-center py-5"><p class="text-muted">暫無來源分析數據</p></div>';
            }

            // ========== 衝量 vs 衝漂亮圖表 ==========
            const volumeVsProfitCtx = document.getElementById('volumeVsProfitChart');
            if (!volumeVsProfitCtx) {
                console.error('找不到 volumeVsProfitChart 元素');
                return;
            }

            const volumeVsProfitData = @json($volumeVsProfit ?? []);
            
            if (volumeVsProfitData && volumeVsProfitData.length > 0) {
                volumeVsProfitChart = new Chart(volumeVsProfitCtx.getContext('2d'), {
                    type: 'scatter',
                    data: {
                        datasets: [{
                            label: '產品分布',
                            data: volumeVsProfitData.map(d => ({
                                x: parseFloat(d.volume) || 0,
                                y: parseFloat(d.profit) || 0,
                                label: d.product_name || ''
                            })),
                            backgroundColor: 'rgba(102, 126, 234, 0.6)',
                            borderColor: '#667eea',
                            pointRadius: 8,
                            pointHoverRadius: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: '銷量（衝量）'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: '利潤（衝漂亮）'
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const data = volumeVsProfitData[context.dataIndex];
                                        return [
                                            '產品: ' + (data.product_name || ''),
                                            '銷量: ' + (data.volume || 0),
                                            '利潤: $' + (data.profit || 0).toLocaleString(),
                                            '毛利率: ' + (data.margin_rate || 0).toFixed(1) + '%'
                                        ];
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                // 顯示空狀態
                volumeVsProfitCtx.parentElement.innerHTML = '<div class="text-center py-5"><p class="text-muted">暫無衝量 vs 衝漂亮數據</p></div>';
            }
        }

        // ========== 勾選功能 ==========
        function toggleSelectAll() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.product-checkbox');
            
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            
            updateComparisonBar();
        }

        function updateComparisonBar() {
            const checkboxes = document.querySelectorAll('.product-checkbox:checked');
            selectedProducts = Array.from(checkboxes).map(cb => JSON.parse(cb.getAttribute('data-item')));
            
            const comparisonBar = document.getElementById('comparisonBar');
            const selectedCount = document.getElementById('selectedCount');
            
            if (selectedProducts.length >= 2) {
                comparisonBar.classList.add('show');
                selectedCount.textContent = selectedProducts.length;
            } else {
                comparisonBar.classList.remove('show');
            }
        }

        // ========== 顯示比較模態視窗 ==========
        function showComparisonModal() {
            if (selectedProducts.length < 2) {
                alert('請至少選擇 2 個產品進行比較');
                return;
            }

            const modal = new bootstrap.Modal(document.getElementById('comparisonModal'));
            const content = document.getElementById('comparisonContent');
            
            let html = '<div class="row">';
            
            selectedProducts.forEach((product, index) => {
                html += `
                    <div class="col-md-${12 / selectedProducts.length}">
                        <div class="comparison-card">
                            <h5 class="mb-3">${product.product_name}</h5>
                            <div class="comparison-item">
                                <span class="comparison-label">來源</span>
                                <span class="comparison-value">${product.source}</span>
                            </div>
                            <div class="comparison-item">
                                <span class="comparison-label">銷量</span>
                                <span class="comparison-value ${getWinner('total_volume', product)}">${product.total_volume.toLocaleString()}</span>
                            </div>
                            <div class="comparison-item">
                                <span class="comparison-label">平均單價</span>
                                <span class="comparison-value">$${product.avg_price.toLocaleString()}</span>
                            </div>
                            <div class="comparison-item">
                                <span class="comparison-label">總利潤</span>
                                <span class="comparison-value ${getWinner('total_profit', product)}">$${product.total_profit.toLocaleString()}</span>
                            </div>
                            <div class="comparison-item">
                                <span class="comparison-label">毛利率</span>
                                <span class="comparison-value ${getWinner('margin_rate', product)}">${product.margin_rate}%</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            
            // 生成結論
            html += generateConclusion();
            
            content.innerHTML = html;
            modal.show();
        }

        // ========== 判斷獲勝者 ==========
        function getWinner(field, currentProduct) {
            const values = selectedProducts.map(p => p[field]);
            const maxValue = Math.max(...values);
            return currentProduct[field] === maxValue ? 'winner' : '';
        }

        // ========== 生成結論 ==========
        function generateConclusion() {
            if (selectedProducts.length < 2) return '';
            
            const product1 = selectedProducts[0];
            const product2 = selectedProducts[1];
            
            let conclusion = '<div class="comparison-conclusion">';
            conclusion += '<strong>分析結論：</strong><br>';
            
            // 銷量比較
            if (product1.total_volume > product2.total_volume) {
                conclusion += `${product1.product_name} 的銷量比 ${product2.product_name} 多 ${((product1.total_volume / product2.total_volume - 1) * 100).toFixed(1)}%。`;
            } else {
                conclusion += `${product2.product_name} 的銷量比 ${product1.product_name} 多 ${((product2.total_volume / product1.total_volume - 1) * 100).toFixed(1)}%。`;
            }
            
            conclusion += '<br>';
            
            // 毛利率比較
            if (product1.margin_rate > product2.margin_rate) {
                const diff = product1.margin_rate - product2.margin_rate;
                conclusion += `${product1.product_name} 的毛利率比 ${product2.product_name} 高 ${diff.toFixed(1)} 個百分點。`;
            } else {
                const diff = product2.margin_rate - product1.margin_rate;
                conclusion += `${product2.product_name} 的毛利率比 ${product1.product_name} 高 ${diff.toFixed(1)} 個百分點。`;
            }
            
            conclusion += '<br>';
            
            // 綜合建議
            if (product1.source === '淘寶' && product2.source === '廠商') {
                if (product1.margin_rate > product2.margin_rate) {
                    conclusion += '淘寶貨雖然銷量較少，但毛利率高出廠商貨 ' + (product1.margin_rate - product2.margin_rate).toFixed(1) + ' 個百分點，建議優先推廣高毛利產品。';
                } else {
                    conclusion += '廠商貨的毛利率較高，但淘寶貨銷量較大，建議根據市場策略選擇。';
                }
            } else {
                conclusion += '建議根據市場需求和利潤目標，平衡銷量與毛利率的關係。';
            }
            
            conclusion += '</div>';
            
            return conclusion;
        }
    </script>
@endsection

@extends('layouts.vertical', ['page_title' => '單本詳情'])

@section('css')
    <style>
        .number-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            gap: 8px;
        }

        .number-badge {
            padding: 8px;
            text-align: center;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .number-badge:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .number-badge.used {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }

        .number-badge.void {
            background: #fff3cd;
            color: #856404;
            border: 2px solid #ffeaa7;
        }

        .number-badge.unused {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }

        .stat-card {
            border-left: 4px solid;
        }

        .stat-card.success {
            border-left-color: #28a745;
        }

        .stat-card.warning {
            border-left-color: #ffc107;
        }

        .stat-card.danger {
            border-left-color: #dc3545;
        }

        .stat-card.info {
            border-left-color: #17a2b8;
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
                        <li class="breadcrumb-item"><a href="{{ route('receipt-books.index') }}">跳單管理</a></li>
                        <li class="breadcrumb-item active">單本詳情</li>
                    </ol>
                </div>
                <h4 class="page-title">單本詳情</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <!-- 成功訊息 -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 單本基本資訊 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="mb-3">
                                <span class="badge bg-primary" style="font-size: 1.2rem;">
                                    {{ $receiptBook->start_number }} ~ {{ $receiptBook->end_number }}
                                </span>
                            </h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong><i class="fe-user me-1"></i>保管人：</strong>
                                        {{ optional($receiptBook->holder)->name ?? '-' }}
                                    </p>
                                    <p class="mb-2">
                                        <strong><i class="fe-calendar me-1"></i>發放日期：</strong>
                                        {{ $receiptBook->issue_date ? $receiptBook->issue_date->format('Y-m-d') : '-' }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong><i class="fe-info me-1"></i>狀態：</strong>
                                        @if($receiptBook->status == 'unused')
                                            <span class="badge bg-info">未使用</span>
                                        @elseif($receiptBook->status == 'active')
                                            <span class="badge bg-success">使用中</span>
                                        @elseif($receiptBook->status == 'returned')
                                            <span class="badge bg-secondary">已繳回</span>
                                        @else
                                            <span class="badge bg-danger">已取消</span>
                                        @endif
                                    </p>
                                    @if($receiptBook->returned_at)
                                        <p class="mb-2">
                                            <strong><i class="fe-check-circle me-1"></i>繳回日期：</strong>
                                            {{ $receiptBook->returned_at->format('Y-m-d') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @if($receiptBook->note)
                                <p class="mb-0 mt-2">
                                    <strong><i class="fe-file-text me-1"></i>備註：</strong>
                                    {{ $receiptBook->note }}
                                </p>
                            @endif
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('receipt-books.edit', $receiptBook->id) }}" class="btn btn-primary me-2">
                                <i class="fe-edit me-1"></i>編輯
                            </a>
                            <a href="{{ route('receipt-books.index') }}" class="btn btn-secondary">
                                <i class="fe-arrow-left me-1"></i>返回列表
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- 跳號清單 -->
    @if(count($missingNumbers) > 0)
        <div class="row">
            <div class="col-12">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fe-alert-triangle me-2"></i>
                            跳號清單（共 {{ count($missingNumbers) }} 個）
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fe-info me-2"></i>
                            以下單號尚未使用且未作廢，請確認是否遺失或尚未繳回
                        </div>
                        <div class="number-grid">
                            @foreach($missingNumbers as $number)
                                <div class="number-badge unused">
                                    {{ $number }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div> <!-- container -->
@endsection

@section('script')
    <script>
        // 初始化 tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // 查看銷售單詳情
        function showSaleDetail(saleId) {
            // 可以導向銷售單詳情頁面
            window.location.href = `/sale/show/${saleId}`;
        }
    </script>
@endsection


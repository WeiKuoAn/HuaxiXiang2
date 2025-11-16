@extends('layouts.vertical', ['page_title' => '報修單詳情'])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">火化爐管理</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('crematorium.repairs.index') }}">報修單</a></li>
                        <li class="breadcrumb-item active">詳情</li>
                    </ol>
                </div>
                <h4 class="page-title">報修單詳情</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- 報修單基本資訊 -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">
                                <i class="mdi mdi-file-document me-2"></i>報修單資訊
                            </h5>
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td width="150"><strong>報修單號：</strong></td>
                                    <td><span class="text-primary fs-5">{{ $repair->repair_number }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>報修日期：</strong></td>
                                    <td>{{ $repair->report_date->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>報修人員：</strong></td>
                                    <td>{{ $repair->reporter->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>狀態：</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $repair->status_color }} fs-6">
                                            {{ $repair->status_text }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="mb-3">
                                <i class="mdi mdi-account-check me-2"></i>處理資訊
                            </h5>
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td width="150"><strong>處理人員：</strong></td>
                                    <td>{{ $repair->processor->name ?? '尚未處理' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>處理時間：</strong></td>
                                    <td>{{ $repair->processed_at ? $repair->processed_at->format('Y-m-d H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>建立時間：</strong></td>
                                    <td>{{ $repair->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>更新時間：</strong></td>
                                    <td>{{ $repair->updated_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- 問題描述 -->
                    <div class="mb-4">
                        <h6><strong>問題描述：</strong></h6>
                        <div class="alert alert-warning">
                            {{ $repair->problem_description }}
                        </div>
                    </div>

                    <!-- 報修設備明細 -->
                    <div class="mb-4">
                        <h5 class="mb-3">
                            <i class="mdi mdi-tools me-2"></i>報修設備明細
                        </h5>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>設備名稱</th>
                                        <th>類別</th>
                                        <th>位置</th>
                                        <th>具體問題</th>
                                        <th>處理方式</th>
                                        <th>更換資訊</th>
                                        <th>備註</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($repair->repairDetails as $index => $detail)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $detail->equipmentInstance->equipmentType->name }}</strong>
                                                @if($detail->equipmentInstance->equipmentType->exclude_from_inventory)
                                                    <br><span class="badge bg-secondary badge-sm">不計庫存</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ [
                                                        'furnace_1' => '一爐',
                                                        'furnace_2' => '二爐',
                                                        'ventilation' => '抽風',
                                                        'furnace_1_ventilation' => '一爐抽風',
                                                        'furnace_2_ventilation' => '二爐抽風',
                                                    ][$detail->equipmentInstance->category] ?? $detail->equipmentInstance->category }}
                                                </span>
                                            </td>
                                            <td>{{ $detail->equipmentInstance->full_location }}</td>
                                            <td>
                                                <div style="max-width: 200px;">
                                                    {{ $detail->problem_description ?? '-' }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($detail->action)
                                                    <span class="badge {{ $detail->action == 'repair' ? 'bg-primary' : 'bg-success' }}">
                                                        {{ $detail->action_text }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">尚未處理</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($detail->action == 'replace')
                                                    <span class="badge bg-warning text-dark">
                                                        {{ $detail->quantity }} 個
                                                    </span>
                                                    <span class="badge bg-secondary">
                                                        {{ $detail->replacement_type_text }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <div style="max-width: 150px;">
                                                    {{ $detail->notes ?? '-' }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 整體備註 -->
                    @if($repair->notes)
                        <div class="mb-4">
                            <h6><strong>整體處理備註：</strong></h6>
                            <div class="alert alert-info">
                                {{ $repair->notes }}
                            </div>
                        </div>
                    @endif

                    <!-- 動作按鈕 -->
                    <div class="text-end">
                        <a href="{{ route('crematorium.repairs.index') }}" class="btn btn-secondary me-2">
                            <i class="mdi mdi-arrow-left me-1"></i>返回列表
                        </a>
                        @if($repair->status == 'pending' || $repair->status == 'processing')
                            <a href="{{ route('crematorium.repairs.edit', $repair->id) }}" class="btn btn-primary">
                                <i class="mdi mdi-pencil me-1"></i>處理報修
                            </a>
                        @endif
                        <button type="button" class="btn btn-info" onclick="window.print()">
                            <i class="mdi mdi-printer me-1"></i>列印
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- container -->

<style>
    @media print {
        .page-title-box,
        .breadcrumb,
        .btn {
            display: none !important;
        }
        
        .card {
            border: none;
            box-shadow: none;
        }
    }
</style>
@endsection


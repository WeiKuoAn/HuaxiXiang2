@extends('layouts.vertical', ['page_title' => '檢查記錄詳情'])

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
                            <li class="breadcrumb-item"><a href="{{ route('crematorium.maintenance') }}">檢查記錄</a></li>
                            <li class="breadcrumb-item active">檢查記錄詳情</li>
                        </ol>
                    </div>
                    <h4 class="page-title">檢查記錄詳情</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- 基本資訊 -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="mb-3">基本資訊</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="120"><strong>設備名稱：</strong></td>
                                        <td>{{ $maintenance->equipment->name ?? '未知設備' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>擺放位置：</strong></td>
                                        <td>{{ $maintenance->equipment->location ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>設備類別：</strong></td>
                                        <td>{{ $maintenance->equipment->full_category_text ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>檢查日期：</strong></td>
                                        <td>{{ $maintenance->maintenance_date }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3">檢查人員</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="120"><strong>檢查人員：</strong></td>
                                        <td>{{ $maintenance->inspector }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>保養人員：</strong></td>
                                        <td>{{ $maintenance->maintainer ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>整體狀態：</strong></td>
                                        <td>
                                            @if($maintenance->overall_status == 'good')
                                                <span class="badge bg-success fs-6">正常</span>
                                            @elseif($maintenance->overall_status == 'warning')
                                                <span class="badge bg-warning fs-6">需注意</span>
                                            @else
                                                <span class="badge bg-danger fs-6">異常</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>建立時間：</strong></td>
                                        <td>{{ $maintenance->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- 檢查項目詳情 -->
                        <div class="mb-4">
                            <h5 class="mb-3">檢查項目詳情</h5>
                            <div class="row">
                                @php
                                    $checkItems = [
                                        'sensor_status' => '綠色感知器',
                                        'relay_status' => '繼電器',
                                        'transformer_status' => '變壓器',
                                        'ignition_rod_status' => '點火棒',
                                        'nozzle_status' => '噴油嘴',
                                        'gasket_status' => '固定墊片',
                                        'oil_pipe_status' => '油管',
                                        'oil_pump_status' => '油泵浦',
                                        'photosensor_status' => '光敏電阻（感光器）',
                                        'controller_status' => '控制器',
                                        'support_rod_status' => '支撐桿'
                                    ];
                                    
                                    $statusText = [
                                        'good' => '正常',
                                        'warning' => '需注意',
                                        'error' => '異常'
                                    ];
                                    
                                    $statusClass = [
                                        'good' => 'success',
                                        'warning' => 'warning',
                                        'error' => 'danger'
                                    ];
                                @endphp

                                @foreach($checkItems as $field => $label)
                                    @if($maintenance->$field)
                                        <div class="col-md-4 mb-3">
                                            <div class="card border-0 bg-light">
                                                <div class="card-body p-3">
                                                    <h6 class="card-title mb-2">{{ $label }}</h6>
                                                    <span class="badge bg-{{ $statusClass[$maintenance->$field] }}">
                                                        @if($maintenance->$field == 'good')
                                                            ✓ {{ $statusText[$maintenance->$field] }}
                                                        @elseif($maintenance->$field == 'warning')
                                                            ⚠ {{ $statusText[$maintenance->$field] }}
                                                        @else
                                                            ✗ {{ $statusText[$maintenance->$field] }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- 備註 -->
                        @if($maintenance->notes)
                            <div class="mb-4">
                                <h5 class="mb-3">備註</h5>
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <p class="mb-0">{{ $maintenance->notes }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- 統計資訊 -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border-0 bg-success bg-opacity-10">
                                    <div class="card-body text-center">
                                        <h3 class="text-success mb-1">
                                            {{ collect($checkItems)->filter(function($label, $field) use ($maintenance) { 
                                                return $maintenance->$field == 'good'; 
                                            })->count() }}
                                        </h3>
                                        <p class="text-success mb-0">正常項目</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-warning bg-opacity-10">
                                    <div class="card-body text-center">
                                        <h3 class="text-warning mb-1">
                                            {{ collect($checkItems)->filter(function($label, $field) use ($maintenance) { 
                                                return $maintenance->$field == 'warning'; 
                                            })->count() }}
                                        </h3>
                                        <p class="text-warning mb-0">需注意項目</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-danger bg-opacity-10">
                                    <div class="card-body text-center">
                                        <h3 class="text-danger mb-1">
                                            {{ collect($checkItems)->filter(function($label, $field) use ($maintenance) { 
                                                return $maintenance->$field == 'error'; 
                                            })->count() }}
                                        </h3>
                                        <p class="text-danger mb-0">異常項目</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('crematorium.maintenance') }}" class="btn btn-secondary me-2">返回列表</a>
                            <a href="#" class="btn btn-primary" onclick="editRecord({{ $maintenance->id }})">編輯記錄</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- container -->

    <script>
        function editRecord(id) {
            // 這裡可以實現編輯功能
            alert('編輯功能：記錄 ID ' + id);
        }
    </script>
@endsection

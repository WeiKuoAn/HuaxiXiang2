@extends('layouts.vertical', ["page_title"=> "設備類型詳情"])

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Huaxixiang</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">火化爐管理</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('crematorium.types.index') }}">設備類型管理</a></li>
                        <li class="breadcrumb-item active">設備詳情</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ $equipmentType->name }} - 詳情</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- 基本資訊 -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">基本資訊</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width: 30%;">設備名稱</th>
                            <td>{{ $equipmentType->name }}</td>
                        </tr>
                        <tr>
                            <th>類別</th>
                            <td>{{ $equipmentType->full_category_text }}</td>
                        </tr>
                        <tr>
                            <th>描述</th>
                            <td>{{ $equipmentType->description ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- 庫存資訊 -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">庫存資訊</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h3 class="text-primary">{{ $equipmentType->stock_new }}</h3>
                            <p class="text-muted mb-0">全新庫存</p>
                        </div>
                        <div class="col-4">
                            <h3 class="text-info">{{ $equipmentType->stock_usable }}</h3>
                            <p class="text-muted mb-0">堪用庫存</p>
                        </div>
                        <div class="col-4">
                            <h3 class="text-success">{{ $equipmentType->stock_total }}</h3>
                            <p class="text-muted mb-0">總庫存</p>
                        </div>
                    </div>
                    
                    @if($equipmentType->stock_total <= 5)
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="mdi mdi-alert me-2"></i>庫存不足，建議補貨
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 設備實例列表 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">設備實例列表（使用此設備的位置）</h5>
                        <span class="badge bg-primary">共 {{ $equipmentType->instances->count() }} 個</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($equipmentType->instances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>編號</th>
                                        <th>位置</th>
                                        <th>狀態</th>
                                        <th>安裝日期</th>
                                        <th>最後維護</th>
                                        <th>備註</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($equipmentType->instances as $instance)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $instance->location }}</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $instance->status_color }}">
                                                    {{ $instance->status_text }}
                                                </span>
                                            </td>
                                            <td>{{ $instance->installed_date ? $instance->installed_date->format('Y-m-d') : '—' }}</td>
                                            <td>{{ $instance->last_maintenance_date ? $instance->last_maintenance_date->format('Y-m-d') : '—' }}</td>
                                            <td>{{ $instance->notes ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-4">此設備類型尚未配置任何設備實例</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 text-end">
            <a href="{{ route('crematorium.types.index') }}" class="btn btn-secondary">返回列表</a>
            <a href="{{ route('crematorium.types.edit', $equipmentType->id) }}" class="btn btn-primary">編輯</a>
        </div>
    </div>
</div>
@endsection


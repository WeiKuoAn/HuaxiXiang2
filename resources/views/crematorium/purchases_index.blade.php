@extends('layouts.vertical', ["page_title"=> "設備進貨管理"])

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
                        <li class="breadcrumb-item"><a href="{{ route('crematorium.equipment.index') }}">火化爐管理</a></li>
                        <li class="breadcrumb-item active">設備進貨管理</li>
                    </ol>
                </div>
                <h4 class="page-title">設備進貨管理</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        {{ session('error') }}
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between mb-3">
                        <div class="col-auto">
                            <h5 class="card-title">進貨記錄列表</h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('crematorium.equipment.index') }}" class="btn btn-secondary me-2">
                                <i class="mdi mdi-arrow-left me-1"></i>返回設備管理
                            </a>
                            <a href="{{ route('crematorium.purchases.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus-circle me-1"></i>新增進貨記錄
                            </a>
                        </div>
                    </div>

                    <!-- 篩選區域 -->
                    <form method="GET" action="{{ route('crematorium.purchases.index') }}" class="mb-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">設備類型</label>
                                <select name="equipment_type_id" class="form-select form-select-sm">
                                    <option value="">全部設備</option>
                                    @foreach($equipmentTypes as $equipmentType)
                                    <option value="{{ $equipmentType->id }}" {{ request('equipment_type_id') == $equipmentType->id ? 'selected' : '' }}>
                                        {{ $equipmentType->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">狀態</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">全部狀態</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>待確認</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>已確認</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>已取消</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">開始日期</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">結束日期</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-success btn-sm me-1">
                                    <i class="mdi mdi-magnify"></i> 搜尋
                                </button>
                                <a href="{{ route('crematorium.purchases.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="mdi mdi-refresh"></i> 重置
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- 進貨記錄表格 -->
                    <div class="table-responsive">
                        <table class="table table-centered table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">單號</th>
                                    <th style="width: 100px;">進貨日期</th>
                                    <th>設備明細</th>
                                    <th style="width: 120px;" class="text-end">總金額</th>
                                    <th style="width: 100px;">進貨人員</th>
                                    <th style="width: 80px;" class="text-center">狀態</th>
                                    <th style="width: 100px;" class="text-center">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchases as $purchase)
                                <tr>
                                    <td>
                                        <small class="text-muted">{{ substr($purchase->purchase_number, -8) }}</small>
                                    </td>
                                    <td>{{ $purchase->purchase_date->format('Y-m-d') }}</td>
                                    <td>
                                        @if($purchase->items->count() > 0)
                                            @foreach($purchase->items as $item)
                                                <div class="mb-1">
                                                    <strong>{{ $item->equipmentType->name ?? '-' }}</strong>
                                                    <span class="badge bg-info ms-1">{{ $item->quantity }}</span>
                                                    @if($item->unit_price)
                                                        <small class="text-muted">@${{ number_format($item->unit_price, 0) }}</small>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-muted">無明細</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($purchase->total_price)
                                            <strong>${{ number_format($purchase->total_price, 0) }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $purchase->purchaser->name ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $purchase->status_color }}">
                                            {{ $purchase->status_text }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($purchase->status == 'pending')
                                        <a href="{{ route('crematorium.purchases.edit', $purchase->id) }}" 
                                           class="btn btn-sm btn-warning" title="編輯">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{ route('crematorium.purchases.destroy', $purchase->id) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirm('確定要刪除此進貨記錄嗎？');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="刪除">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="mdi mdi-information-outline me-1"></i>
                                        目前沒有進貨記錄
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($purchases->total() > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end"><strong>本頁統計：</strong></td>
                                    <td class="text-end">
                                        <strong>${{ number_format($purchases->sum('total_price'), 0) }}</strong>
                                    </td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                    <!-- 分頁 -->
                    <div class="mt-3">
                        {{ $purchases->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- container -->
@endsection


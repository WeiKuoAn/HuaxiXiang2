@extends('layouts.vertical', ['page_title' => '火化爐報修單'])

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
                        <li class="breadcrumb-item active">報修單</li>
                    </ol>
                </div>
                <h4 class="page-title">火化爐報修單</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between mb-3">
                        <div class="col-auto">
                            <h5 class="card-title">報修單列表</h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('crematorium.repairs.create') }}" class="btn btn-danger">
                                <i class="mdi mdi-alert-circle me-1"></i>線上報修
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- 篩選區域 -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('crematorium.repairs.index') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">狀態</label>
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="">全部狀態</option>
                                        <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>待處理</option>
                                        <option value="processing" {{ $request->status == 'processing' ? 'selected' : '' }}>處理中</option>
                                        <option value="completed" {{ $request->status == 'completed' ? 'selected' : '' }}>已完成</option>
                                        <option value="cancelled" {{ $request->status == 'cancelled' ? 'selected' : '' }}>已取消</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">開始日期</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ $request->start_date }}" onchange="this.form.submit()">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">結束日期</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ $request->end_date }}" onchange="this.form.submit()">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <a href="{{ route('crematorium.repairs.index') }}" class="btn btn-outline-secondary">
                                            <i class="mdi mdi-close"></i> 清除篩選
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>報修單號</th>
                                    <th>報修日期</th>
                                    <th>報修人員</th>
                                    <th>報修設備數</th>
                                    <th>問題描述</th>
                                    <th>狀態</th>
                                    <th>處理人員</th>
                                    <th>處理時間</th>
                                    <th>動作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($repairs as $repair)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $repair->repair_number }}</strong>
                                        </td>
                                        <td>{{ $repair->report_date->format('Y-m-d') }}</td>
                                        <td>{{ $repair->reporter->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $repair->repairDetails->count() }} 項</span>
                                        </td>
                                        <td>
                                            <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                {{ $repair->problem_description }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $repair->status_color }}">
                                                {{ $repair->status_text }}
                                            </span>
                                        </td>
                                        <td>{{ $repair->processor->name ?? '-' }}</td>
                                        <td>{{ $repair->processed_at ? $repair->processed_at->format('Y-m-d H:i') : '-' }}</td>
                                        <td>
                                            <div class="btn-group dropdown">
                                                <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary btn-sm waves-effect" data-bs-toggle="dropdown" aria-expanded="false">
                                                    動作 <i class="mdi mdi-arrow-down-drop-circle"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="{{ route('crematorium.repairs.show', $repair->id) }}">
                                                        <i class="mdi mdi-eye me-2 text-muted font-18 vertical-middle"></i>查看詳情
                                                    </a>
                                                    @if($repair->status == 'pending' || $repair->status == 'processing')
                                                        <a class="dropdown-item" href="{{ route('crematorium.repairs.edit', $repair->id) }}">
                                                            <i class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>處理報修
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="cancelRepair({{ $repair->id }})">
                                                            <i class="mdi mdi-close-circle me-2 text-muted font-18 vertical-middle"></i>取消報修
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">暫無報修記錄</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($repairs->hasPages())
                        <div class="mt-3">
                            {{ $repairs->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div> <!-- container -->

<script>
function cancelRepair(id) {
    if (confirm('確定要取消此報修單嗎？')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/crematorium/repairs/${id}/cancel`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection


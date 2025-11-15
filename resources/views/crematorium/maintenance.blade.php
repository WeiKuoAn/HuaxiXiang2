@extends('layouts.vertical', ['page_title' => '設備檢查記錄'])

@section('css')
<style>
    .filter-form {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }
    
    .filter-form .form-select,
    .filter-form .form-control {
        border-radius: 6px;
    }
    
    .filter-form .btn {
        border-radius: 6px;
        white-space: nowrap;
    }
    
    .filter-form .d-flex.gap-2 {
        gap: 0.5rem;
    }
    
    .filter-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        font-size: 0.9rem;
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">火化爐管理</a></li>
                            <li class="breadcrumb-item active">檢查記錄</li>
                        </ol>
                    </div>
                    <h4 class="page-title">設備檢查記錄</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between">
                            <div class="col-auto">
                                <h5 class="card-title">檢查記錄列表</h5>
                            </div>
                            <div class="col-auto">
                                @if($isManager ?? false)
                                    <a href="{{ route('crematorium.createMaintenance') }}" class="btn btn-primary">
                                        <i class="mdi mdi-plus-circle me-1"></i>指派檢查人員
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- 篩選區域 -->
                        <div class="filter-form">
                            <form method="GET" action="{{ route('crematorium.maintenance') }}" class="row g-3" id="filterForm">
                                    <div class="col-md-2">
                                        <div class="filter-label">狀態</div>
                                        <select name="status" class="form-select" onchange="this.form.submit()">
                                            <option value="">全部狀態</option>
                                            <option value="0" {{ $request->get('status') == '0' ? 'selected' : '' }}>未檢查</option>
                                            <option value="3" {{ $request->get('status') == '3' ? 'selected' : '' }}>送審</option>
                                            <option value="9" {{ $request->get('status') == '9' ? 'selected' : '' }}>已檢查</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="filter-label">開始日期</div>
                                        <input type="date" name="start_date" class="form-control" 
                                               value="{{ $request->get('start_date') }}" 
                                               onchange="this.form.submit()">
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="filter-label">結束日期</div>
                                        <input type="date" name="end_date" class="form-control" 
                                               value="{{ $request->get('end_date') }}" 
                                               onchange="this.form.submit()">
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="filter-label">檢查人員</div>
                                        <select name="inspector" class="form-select" onchange="this.form.submit()">
                                            <option value="">全部檢查人員</option>
                                            @if(isset($staff))
                                                @foreach($staff as $person)
                                                    <option value="{{ $person->id }}" {{ $request->get('inspector') == $person->id ? 'selected' : '' }}>
                                                        {{ $person->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="filter-label">保養人員</div>
                                        <select name="maintainer" class="form-select" onchange="this.form.submit()">
                                            <option value="">全部保養人員</option>
                                            @if(isset($staff))
                                                @foreach($staff as $person)
                                                    <option value="{{ $person->id }}" {{ $request->get('maintainer') == $person->id ? 'selected' : '' }}>
                                                        {{ $person->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="filter-label">&nbsp;</div>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-magnify me-1"></i>搜尋
                                            </button>
                                            <a href="{{ route('crematorium.maintenance') }}" class="btn btn-outline-secondary">
                                                <i class="mdi mdi-refresh me-1"></i>清除
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- 檢查記錄表格 -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="8%">編號</th>
                                        <th width="15%">檢查單號</th>
                                        <th width="12%">檢查日期</th>
                                        <th width="12%">檢查人員</th>
                                        <th width="12%">保養人員</th>
                                        <th width="10%">整體狀態</th>
                                        <th width="15%">異常項目</th>
                                        <th width="10%">建立日期</th>
                                        <th width="6%">動作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($maintenance as $key => $record)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <span class="fw-bold">{{ $record->maintenance_number }}</span>
                                            </td>
                                            <td>{{ $record->maintenance_date ? \Carbon\Carbon::parse($record->maintenance_date)->format('Y-m-d') : '-' }}</td>
                                            <td>{{ $record->inspectorUser->name ?? '未指派' }}</td>
                                            <td>{{ $record->maintainerUser->name ?? '未指派' }}</td>
                                            <td>
                                                @php
                                                    $statusClass = [
                                                        0 => 'warning',
                                                        3 => 'info', 
                                                        9 => 'success'
                                                    ][$record->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">
                                                    {{ $record->status_text }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $problemCount = $record->maintenanceDetails->where('status', 'problem')->count();
                                                @endphp
                                                @if($problemCount > 0)
                                                    <span class="text-danger fw-bold">{{ $problemCount }} 項異常</span>
                                                @else
                                                    <span class="text-success">正常</span>
                                                @endif
                                            </td>
                                            <td>{{ $record->created_at ? $record->created_at->format('Y-m-d') : '-' }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('crematorium.editMaintenance', $record->id) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="編輯">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    @if($record->status == 3)
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="submitForReview({{ $record->id }})" title="審核通過">
                                                            <i class="mdi mdi-check"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="mdi mdi-information-outline me-2"></i>
                                                    暫無檢查記錄
                                                </div>
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
    </div> <!-- container -->

    <script>

        function submitForReview(id) {
            if (confirm('確定要審核通過此檢查記錄嗎？')) {
                // 這裡可以添加 AJAX 請求來更新狀態
                fetch(`/crematorium/maintenance/${id}/submit-review`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({status: 3})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('審核失敗，請重試');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('審核失敗，請重試');
                });
            }
        }
    </script>
@endsection
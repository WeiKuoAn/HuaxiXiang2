@extends('layouts.vertical', ['page_title' => '編輯單本'])

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
                        <li class="breadcrumb-item active">編輯單本</li>
                    </ol>
                </div>
                <h4 class="page-title">編輯單本</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('receipt-books.update', $receiptBook->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- 號碼範圍（不可編輯） -->
                        <div class="mb-3">
                            <label class="form-label">號碼範圍</label>
                            <div class="alert alert-info">
                                <i class="fe-info me-2"></i>
                                <strong>{{ $receiptBook->start_number }} ~ {{ $receiptBook->end_number }}</strong>
                                （號碼範圍創建後不可修改）
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">保管人 <span class="text-danger">*</span></label>
                            <select class="form-select @error('holder_id') is-invalid @enderror" 
                                    name="holder_id" 
                                    required>
                                <option value="">請選擇保管人</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ (old('holder_id', $receiptBook->holder_id) == $user->id) ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('holder_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">發放日期 <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('issue_date') is-invalid @enderror" 
                                   name="issue_date" 
                                   value="{{ old('issue_date', $receiptBook->issue_date ? $receiptBook->issue_date->format('Y-m-d') : '') }}">
                            @error('issue_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">繳回日期</label>
                            <input type="date" 
                                   class="form-control @error('returned_at') is-invalid @enderror" 
                                   name="returned_at" 
                                   value="{{ old('returned_at', $receiptBook->returned_at ? $receiptBook->returned_at->format('Y-m-d') : '') }}">
                            @error('returned_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fe-info me-1"></i>
                                填寫繳回日期後，狀態會自動改為「已繳回」
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">狀態 <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    name="status" 
                                    required>
                                <option value="unused" {{ old('status', $receiptBook->status) == 'unused' ? 'selected' : '' }}>
                                    未使用
                                </option>
                                <option value="active" {{ old('status', $receiptBook->status) == 'active' ? 'selected' : '' }}>
                                    使用中
                                </option>
                                <option value="returned" {{ old('status', $receiptBook->status) == 'returned' ? 'selected' : '' }}>
                                    已繳回
                                </option>
                                <option value="cancelled" {{ old('status', $receiptBook->status) == 'cancelled' ? 'selected' : '' }}>
                                    已取消
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">備註</label>
                            <textarea class="form-control @error('note') is-invalid @enderror" 
                                      name="note" 
                                      rows="3" 
                                      placeholder="請輸入備註（選填）">{{ old('note', $receiptBook->note) }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <a href="{{ route('receipt-books.show', $receiptBook->id) }}" class="btn btn-secondary waves-effect waves-light me-1">
                                <i class="fe-x me-1"></i>取消
                            </a>
                            <button type="submit" class="btn btn-success waves-effect waves-light">
                                <i class="fe-check me-1"></i>更新
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 使用情況卡片 -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fe-bar-chart me-1"></i>使用情況
                    </h5>
                    @php
                        $stats = $receiptBook->getStatistics();
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>總數量</span>
                            <strong>{{ $stats['total'] }} 張</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-success">已使用</span>
                            <strong class="text-success">{{ $stats['used'] }} 張</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-danger">未使用</span>
                            <strong class="text-danger">{{ $stats['missing'] }} 張</strong>
                        </div>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $stats['usage_rate'] }}%" 
                             aria-valuenow="{{ $stats['usage_rate'] }}" 
                             aria-valuemin="0" aria-valuemax="100">
                            {{ $stats['usage_rate'] }}%
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('receipt-books.show', $receiptBook->id) }}" class="btn btn-info btn-sm w-100">
                            <i class="fe-eye me-1"></i>查看詳細使用情況
                        </a>
                    </div>
                </div>
            </div>

            <div class="card border-warning">
                <div class="card-body">
                    <h5 class="card-title mb-3 text-warning">
                        <i class="fe-alert-triangle me-1"></i>注意事項
                    </h5>
                    <ul class="mb-0">
                        <li class="mb-2">
                            <small>號碼範圍創建後無法修改</small>
                        </li>
                        <li class="mb-2">
                            <small>修改保管人不會影響已使用的單號記錄</small>
                        </li>
                        <li class="mb-2">
                            <small>標記為「已繳回」或「已取消」後建議不再修改</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div> <!-- container -->
@endsection


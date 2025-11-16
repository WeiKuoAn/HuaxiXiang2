@extends('layouts.vertical', ['page_title' => '新增單本'])

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
                        <li class="breadcrumb-item active">新增單本</li>
                    </ol>
                </div>
                <h4 class="page-title">新增單本</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('receipt-books.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">號碼範圍 <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label small">起始號碼</label>
                                    <input type="number" 
                                           class="form-control @error('start_number') is-invalid @enderror" 
                                           name="start_number" 
                                           value="{{ old('start_number') }}" 
                                           min="1"
                                           placeholder="例如：1"
                                           required>
                                    @error('start_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">結束號碼</label>
                                    <input type="number" 
                                           class="form-control @error('end_number') is-invalid @enderror" 
                                           name="end_number" 
                                           value="{{ old('end_number') }}" 
                                           min="2"
                                           placeholder="例如：50"
                                           required>
                                    @error('end_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fe-info me-1"></i>
                                系統會檢查號碼範圍是否與現有單本重疊
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">保管人 <span class="text-muted">(可留空，改由認領)</span></label>
                            <select class="form-select @error('holder_id') is-invalid @enderror" 
                                    name="holder_id">
                                <option value="">（暫不指定，交由專員認領）</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('holder_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('holder_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                建議：若未指定保管人，可於「<a href="{{ route('receipt-books.claimable') }}">認領清單</a>」由專員自行認領。
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">發放日期 <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('issue_date') is-invalid @enderror" 
                                   name="issue_date" 
                                   value="{{ old('issue_date') }}">
                            @error('issue_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">備註</label>
                            <textarea class="form-control @error('note') is-invalid @enderror" 
                                      name="note" 
                                      rows="3" 
                                      placeholder="請輸入備註（選填）">{{ old('note') }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <a href="{{ route('receipt-books.index') }}" class="btn btn-secondary waves-effect waves-light me-1">
                                <i class="fe-x me-1"></i>取消
                            </a>
                            <button type="submit" class="btn btn-success waves-effect waves-light">
                                <i class="fe-check me-1"></i>新增
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 說明卡片 -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fe-help-circle me-1"></i>使用說明
                    </h5>
                    <div class="mb-3">
                        <h6 class="text-primary">什麼是跳單管理？</h6>
                        <p class="text-muted small">
                            跳單管理用於追蹤紙本收據的使用情況，確保每張收據都有被正確使用或繳回。
                        </p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-primary">號碼範圍</h6>
                        <p class="text-muted small">
                            • 每本單子通常以 50 張為一本<br>
                            • 例如：1~50、51~100、101~150<br>
                            • 號碼範圍不可與現有單本重疊
                        </p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-primary">後續操作</h6>
                        <p class="text-muted small">
                            • 新增後可查看該單本的詳細使用情況<br>
                            • 系統會自動比對 sale_data 中的單號<br>
                            • 可快速檢查哪些單號尚未使用（跳號）
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div> <!-- container -->
@endsection


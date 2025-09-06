@extends('layouts.vertical', ['page_title' => '編輯紀念日'])

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
                            <li class="breadcrumb-item"><a href="{{ route('memorial.dates') }}">紀念日管理</a></li>
                            <li class="breadcrumb-item active">編輯紀念日</li>
                        </ol>
                    </div>
                    <h4 class="page-title">編輯紀念日</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

    <!-- 編輯表單 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">紀念日資訊</h5>
                </div>
                <div class="card-body">
                    <!-- 基本資訊顯示 -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">業務單號</label>
                            <p class="form-control-plaintext">{{ $memorialDate->sale->sale_on ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">客戶名稱</label>
                            <p class="form-control-plaintext">{{ $memorialDate->sale->cust_name->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">寶貝名稱</label>
                            <p class="form-control-plaintext">{{ $memorialDate->sale->pet_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">往生日期</label>
                            <p class="form-control-plaintext">
                                @if($memorialDate->sale->death_date)
                                    {{ \Carbon\Carbon::parse($memorialDate->sale->death_date)->format('Y年m月d日') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr>

                    <!-- 編輯表單 -->
                    <form method="POST" action="{{ route('memorial.dates.update', $memorialDate->id) }}">
                        @csrf
                        @method('POST')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seventh_day" class="form-label">頭七日期</label>
                                    <input type="date" class="form-control @error('seventh_day') is-invalid @enderror" 
                                           id="seventh_day" name="seventh_day" 
                                           value="{{ old('seventh_day', $memorialDate->seventh_day ? $memorialDate->seventh_day->format('Y-m-d') : '') }}">
                                    @error('seventh_day')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">浪浪方案無頭七，可留空</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="forty_ninth_day" class="form-label">四十九日日期 <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('forty_ninth_day') is-invalid @enderror" 
                                           id="forty_ninth_day" name="forty_ninth_day" 
                                           value="{{ old('forty_ninth_day', $memorialDate->forty_ninth_day->format('Y-m-d')) }}" required>
                                    @error('forty_ninth_day')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hundredth_day" class="form-label">百日日期 <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('hundredth_day') is-invalid @enderror" 
                                           id="hundredth_day" name="hundredth_day" 
                                           value="{{ old('hundredth_day', $memorialDate->hundredth_day->format('Y-m-d')) }}" required>
                                    @error('hundredth_day')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="anniversary_day" class="form-label">對年日期 <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('anniversary_day') is-invalid @enderror" 
                                           id="anniversary_day" name="anniversary_day" 
                                           value="{{ old('anniversary_day', $memorialDate->anniversary_day->format('Y-m-d')) }}" required>
                                    @error('anniversary_day')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">備註</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="請輸入備註資訊">{{ old('notes', $memorialDate->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- 按鈕區域 -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('memorial.dates') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left"></i> 返回列表
                                    </a>
                                    <div>
                                        <button type="button" class="btn btn-outline-warning me-2" onclick="resetForm()">
                                            <i class="mdi mdi-refresh"></i> 重置
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="mdi mdi-content-save"></i> 儲存變更
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 日期計算提示 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">日期計算說明</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="mdi mdi-calendar-week text-primary" style="font-size: 24px;"></i>
                                <h6 class="mt-2">頭七</h6>
                                <p class="text-muted small">往生後第7天</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="mdi mdi-calendar-month text-info" style="font-size: 24px;"></i>
                                <h6 class="mt-2">四十九日</h6>
                                <p class="text-muted small">往生後第49天</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="mdi mdi-calendar-range text-success" style="font-size: 24px;"></i>
                                <h6 class="mt-2">百日</h6>
                                <p class="text-muted small">往生後第100天</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="mdi mdi-calendar-star text-warning" style="font-size: 24px;"></i>
                                <h6 class="mt-2">對年</h6>
                                <p class="text-muted small">往生後滿一年</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div> <!-- container -->
@endsection

@push('scripts')
<script>
function resetForm() {
    if (confirm('確定要重置表單嗎？所有變更將會遺失。')) {
        // 重置為原始值
        document.getElementById('seventh_day').value = '{{ $memorialDate->seventh_day ? $memorialDate->seventh_day->format("Y-m-d") : "" }}';
        document.getElementById('forty_ninth_day').value = '{{ $memorialDate->forty_ninth_day->format("Y-m-d") }}';
        document.getElementById('hundredth_day').value = '{{ $memorialDate->hundredth_day->format("Y-m-d") }}';
        document.getElementById('anniversary_day').value = '{{ $memorialDate->anniversary_day->format("Y-m-d") }}';
        document.getElementById('notes').value = '{{ $memorialDate->notes }}';
    }
}

$(document).ready(function() {
    // 表單驗證
    $('form').on('submit', function(e) {
        var fortyNinthDay = $('#forty_ninth_day').val();
        var hundredthDay = $('#hundredth_day').val();
        var anniversaryDay = $('#anniversary_day').val();
        
        if (!fortyNinthDay || !hundredthDay || !anniversaryDay) {
            e.preventDefault();
            alert('請填寫所有必填的紀念日期');
            return false;
        }
        
        // 檢查日期邏輯
        if (fortyNinthDay && hundredthDay && fortyNinthDay >= hundredthDay) {
            e.preventDefault();
            alert('四十九日日期必須早於百日日期');
            return false;
        }
        
        if (hundredthDay && anniversaryDay && hundredthDay >= anniversaryDay) {
            e.preventDefault();
            alert('百日日期必須早於對年日期');
            return false;
        }
    });
});
</script>
@endpush

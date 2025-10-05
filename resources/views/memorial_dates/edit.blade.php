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
                                    @if ($memorialDate->sale->death_date)
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
                                @if($memorialDate->sale->plan_id != 4)
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="seventh_day" class="form-label">頭七日期</label>
                                        <input type="date"
                                            class="form-control @error('seventh_day') is-invalid @enderror" id="seventh_day"
                                            name="seventh_day"
                                            value="{{ old('seventh_day', $memorialDate->seventh_day ? $memorialDate->seventh_day->format('Y-m-d') : '') }}">
                                        @error('seventh_day')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1" id="seventh_reserved" name="seventh_reserved" {{ old('seventh_reserved', $memorialDate->seventh_reserved) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="seventh_reserved">預約日期</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 reservation-date-container fade-out" id="seventh_date_container">
                                            <input type="date" class="form-control" id="seventh_reserved_at" name="seventh_reserved_at" value="{{ old('seventh_reserved_at', $memorialDate->seventh_reserved_at ? $memorialDate->seventh_reserved_at->format('Y-m-d') : '') }}" placeholder="預約日期">
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="forty_ninth_day" class="form-label">四十九日日期 <span
                                                class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control @error('forty_ninth_day') is-invalid @enderror"
                                            id="forty_ninth_day" name="forty_ninth_day"
                                            value="{{ old('forty_ninth_day', $memorialDate->forty_ninth_day->format('Y-m-d')) }}"
                                            required>
                                        @error('forty_ninth_day')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1" id="forty_ninth_reserved" name="forty_ninth_reserved" {{ old('forty_ninth_reserved', $memorialDate->forty_ninth_reserved) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="forty_ninth_reserved">預約日期</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 reservation-date-container fade-out" id="forty_ninth_date_container">
                                            <input type="date" class="form-control" id="forty_ninth_reserved_at" name="forty_ninth_reserved_at" value="{{ old('forty_ninth_reserved_at', $memorialDate->forty_ninth_reserved_at ? $memorialDate->forty_ninth_reserved_at->format('Y-m-d') : '') }}" placeholder="預約日期">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="hundredth_day" class="form-label">百日日期 <span
                                                class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control @error('hundredth_day') is-invalid @enderror"
                                            id="hundredth_day" name="hundredth_day"
                                            value="{{ old('hundredth_day', $memorialDate->hundredth_day->format('Y-m-d')) }}"
                                            required>
                                        @error('hundredth_day')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1" id="hundredth_reserved" name="hundredth_reserved" {{ old('hundredth_reserved', $memorialDate->hundredth_reserved) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="hundredth_reserved">預約日期</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 reservation-date-container fade-out" id="hundredth_date_container">
                                            <input type="date" class="form-control" id="hundredth_reserved_at" name="hundredth_reserved_at" value="{{ old('hundredth_reserved_at', $memorialDate->hundredth_reserved_at ? $memorialDate->hundredth_reserved_at->format('Y-m-d') : '') }}" placeholder="預約日期">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="anniversary_day" class="form-label">對年日期</label>
                                        <input type="date"
                                            class="form-control @error('anniversary_day') is-invalid @enderror"
                                            id="anniversary_day" name="anniversary_day"
                                            value="{{ old('anniversary_day', $memorialDate->anniversary_day ? $memorialDate->anniversary_day->format('Y-m-d') : '') }}">
                                        @error('anniversary_day')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1" id="anniversary_reserved" name="anniversary_reserved" {{ old('anniversary_reserved', $memorialDate->anniversary_reserved) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="anniversary_reserved">預約日期</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 reservation-date-container fade-out" id="anniversary_date_container">
                                            <input type="date" class="form-control" id="anniversary_reserved_at" name="anniversary_reserved_at" value="{{ old('anniversary_reserved_at', $memorialDate->anniversary_reserved_at ? $memorialDate->anniversary_reserved_at->format('Y-m-d') : '') }}" placeholder="預約日期">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">總備註</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
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
                                            <button type="button" class="btn btn-outline-warning me-2"
                                                onclick="resetForm()">
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

        <style>
        .reservation-date-container {
            transition: all 1s ease-in-out;
            overflow: hidden;
        }
        .reservation-date-container.fade-in {
            opacity: 1;
            max-height: 100px;
        }
        .reservation-date-container.fade-out {
            opacity: 0;
            max-height: 0;
        }
        </style>

        <script>
        function resetForm() {
            if (confirm('確定要重置表單嗎？所有變更將會遺失。')) {
                // 重置為原始值
                @if($memorialDate->sale->plan_id != 4)
                document.getElementById('seventh_day').value =
                    '{{ $memorialDate->seventh_day ? $memorialDate->seventh_day->format('Y-m-d') : '' }}';
                document.getElementById('seventh_reserved').checked = {{ $memorialDate->seventh_reserved ? 'true' : 'false' }};
                document.getElementById('seventh_reserved_at').value = '{{ $memorialDate->seventh_reserved_at ? $memorialDate->seventh_reserved_at->format('Y-m-d') : '' }}';
                @endif
                document.getElementById('forty_ninth_day').value = '{{ $memorialDate->forty_ninth_day->format('Y-m-d') }}';
                document.getElementById('hundredth_day').value = '{{ $memorialDate->hundredth_day->format('Y-m-d') }}';
                document.getElementById('anniversary_day').value = '{{ $memorialDate->anniversary_day ? $memorialDate->anniversary_day->format('Y-m-d') : '' }}';
                document.getElementById('forty_ninth_reserved').checked = {{ $memorialDate->forty_ninth_reserved ? 'true' : 'false' }};
                document.getElementById('forty_ninth_reserved_at').value = '{{ $memorialDate->forty_ninth_reserved_at ? $memorialDate->forty_ninth_reserved_at->format('Y-m-d') : '' }}';
                document.getElementById('hundredth_reserved').checked = {{ $memorialDate->hundredth_reserved ? 'true' : 'false' }};
                document.getElementById('hundredth_reserved_at').value = '{{ $memorialDate->hundredth_reserved_at ? $memorialDate->hundredth_reserved_at->format('Y-m-d') : '' }}';
                document.getElementById('anniversary_reserved').checked = {{ $memorialDate->anniversary_reserved ? 'true' : 'false' }};
                document.getElementById('anniversary_reserved_at').value = '{{ $memorialDate->anniversary_reserved_at ? $memorialDate->anniversary_reserved_at->format('Y-m-d') : '' }}';
                document.getElementById('notes').value = '{{ $memorialDate->notes }}';
            }
        }

        // 使用原生 JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded 事件觸發');
            
            // 處理預約日期勾選顯示/隱藏
            function toggleReservationDate(checkboxId, containerId, inputId) {
                const checkbox = document.getElementById(checkboxId);
                const container = document.getElementById(containerId);
                const input = document.getElementById(inputId);
                
                console.log('尋找元素:', checkboxId, checkbox, containerId, container, inputId, input);
                
                if (checkbox && container && input) {
                    checkbox.addEventListener('change', function() {
                        console.log(checkboxId + ' 預約狀態改變:', this.checked);
                        if (this.checked) {
                            // 顯示動畫
                            container.classList.remove('fade-out');
                            container.classList.add('fade-in');
                            console.log('顯示 ' + checkboxId + ' 預約日期欄位');
                        } else {
                            // 隱藏動畫
                            container.classList.remove('fade-in');
                            container.classList.add('fade-out');
                            // 1秒後清空值
                            setTimeout(() => {
                                input.value = '';
                            }, 100);
                            console.log('隱藏 ' + checkboxId + ' 預約日期欄位');
                        }
                    });
                } else {
                    console.log('找不到元素:', checkboxId, containerId, inputId);
                }
            }
            
            // 初始化顯示狀態
            function initReservationDisplay(checkboxId, containerId) {
                const checkbox = document.getElementById(checkboxId);
                const container = document.getElementById(containerId);
                
                console.log('初始化檢查:', checkboxId, checkbox, containerId, container);
                
                if (checkbox && container && checkbox.checked) {
                    container.classList.remove('fade-out');
                    container.classList.add('fade-in');
                    console.log('初始化顯示 ' + checkboxId + ' 預約日期欄位');
                }
            }
            
            // 綁定事件
            toggleReservationDate('seventh_reserved', 'seventh_date_container', 'seventh_reserved_at');
            toggleReservationDate('forty_ninth_reserved', 'forty_ninth_date_container', 'forty_ninth_reserved_at');
            toggleReservationDate('hundredth_reserved', 'hundredth_date_container', 'hundredth_reserved_at');
            toggleReservationDate('anniversary_reserved', 'anniversary_date_container', 'anniversary_reserved_at');
            
            // 初始化顯示狀態
            console.log('開始初始化顯示狀態...');
            initReservationDisplay('seventh_reserved', 'seventh_date_container');
            initReservationDisplay('forty_ninth_reserved', 'forty_ninth_date_container');
            initReservationDisplay('hundredth_reserved', 'hundredth_date_container');
            initReservationDisplay('anniversary_reserved', 'anniversary_date_container');
            console.log('初始化完成');

            // 表單驗證
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const fortyNinthDay = document.getElementById('forty_ninth_day').value;
                    const hundredthDay = document.getElementById('hundredth_day').value;
                    const anniversaryDay = document.getElementById('anniversary_day').value;

                    // 檢查日期邏輯
                    if (fortyNinthDay && hundredthDay && fortyNinthDay >= hundredthDay) {
                        e.preventDefault();
                        alert('四十九日日期必須早於百日日期');
                        return false;
                    }

                    if (anniversaryDay && hundredthDay && hundredthDay >= anniversaryDay) {
                        e.preventDefault();
                        alert('百日日期必須早於對年日期');
                        return false;
                    }

                    // 若勾選已預約，則需填寫預約日期
                    const checks = [
                        { c: 'seventh_reserved', d: 'seventh_reserved_at', label: '頭七' },
                        { c: 'forty_ninth_reserved', d: 'forty_ninth_reserved_at', label: '四十九日' },
                        { c: 'hundredth_reserved', d: 'hundredth_reserved_at', label: '百日' },
                        { c: 'anniversary_reserved', d: 'anniversary_reserved_at', label: '對年' },
                    ];
                    
                    for (let i = 0; i < checks.length; i++) {
                        const row = checks[i];
                        const checkbox = document.getElementById(row.c);
                        const dateInput = document.getElementById(row.d);
                        
                        if (checkbox && dateInput && checkbox.checked && !dateInput.value) {
                            e.preventDefault();
                            alert(row.label + ' 已勾選預約日期，請填寫預約日期');
                            return false;
                        }
                    }
                });
            }
        });
        </script>

    @endsection

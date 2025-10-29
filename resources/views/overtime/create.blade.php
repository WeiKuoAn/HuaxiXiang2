@extends('layouts.vertical', ['page_title' => '新增加班'])

@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- third party css end -->
    <style>
        .overtime-section {
            border: 1px solid #e3eaef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
        }

        .overtime-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }

        .overtime-row {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
        }

        .remove-btn {
            color: #dc3545;
            cursor: pointer;
        }

        .add-btn {
            color: #28a745;
            cursor: pointer;
        }

        .calculation-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            margin-top: 10px;
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
                            <li class="breadcrumb-item"><a href="{{ route('overtime.index') }}">加班管理</a></li>
                            <li class="breadcrumb-item active">新增加班</li>
                        </ol>
                    </div>
                    <h4 class="page-title">新增加班</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('overtime.create.data') }}" method="POST" id="overtimeForm">
                            @csrf
                            
                            <!-- 基本資訊 -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">加班日期<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="overtime_date" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>

                            <!-- 加班人員區塊 -->
                            <div class="overtime-section">
                                <h5 class="overtime-title">
                                    <i class="fe-users me-2"></i>加班人員
                                </h5>
                                <div id="overtime-container">
                                    <div class="overtime-row" data-index="0">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">人員<span class="text-danger">*</span></label>
                                                    <select class="form-control" name="overtime[0][user_id]" data-toggle="select" required>
                                                        <option value="">請選擇人員</option>
                                                        @foreach ($users as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">加班分鐘<span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="overtime[0][minutes]" min="1" required onchange="calculateOvertimePay(0)" onkeydown="handleMinutesKeydown(event, 0)">
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="mb-3">
                                                    <label class="form-label">事由<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="overtime[0][reason]" placeholder="請輸入加班事由..." required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="calculation-box" id="calculation-0">
                                                    <small class="text-muted">請輸入加班分鐘數以計算加班費</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-overtime" onclick="removeOvertime(this)">
                                                    <i class="fe-trash-2 me-1"></i>移除
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="addOvertime()">
                                    <i class="fe-plus me-1"></i>新增加班人員
                                </button>
                            </div>

                            <!-- 提交按鈕 -->
                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success waves-effect waves-light m-1">
                                        <i class="fe-check-circle me-1"></i>新增加班
                                    </button>
                                    <button type="reset" class="btn btn-secondary waves-effect waves-light m-1" onclick="history.go(-1)">
                                        <i class="fe-x me-1"></i>回上一頁
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- container -->
@endsection

@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <!-- third party js ends -->

    <script>
        let overtimeIndex = 0;

        $(document).ready(function() {
            // 初始化 Select2
            $('[data-toggle="select"]').select2();
        });

        // 新增加班人員
        function addOvertime() {
            overtimeIndex++;
            const template = `
                <div class="overtime-row" data-index="${overtimeIndex}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">人員<span class="text-danger">*</span></label>
                                <select class="form-control" name="overtime[${overtimeIndex}][user_id]" data-toggle="select" required>
                                    <option value="">請選擇人員</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">加班分鐘<span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="overtime[${overtimeIndex}][minutes]" min="1" required onchange="calculateOvertimePay(${overtimeIndex})" onkeydown="handleMinutesKeydown(event, ${overtimeIndex})">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">事由<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="overtime[${overtimeIndex}][reason]" placeholder="請輸入加班事由..." required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="calculation-box" id="calculation-${overtimeIndex}">
                                <small class="text-muted">請輸入加班分鐘數以計算加班費</small>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-overtime" onclick="removeOvertime(this)">
                                <i class="fe-trash-2 me-1"></i>移除
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            $('#overtime-container').append(template);
            
            // 重新初始化 Select2
            $('[data-toggle="select"]').select2();
        }

        // 移除加班人員
        function removeOvertime(button) {
            const container = $('#overtime-container');
            const rows = container.find('.overtime-row');
            
            if (rows.length > 1) {
                $(button).closest('.overtime-row').remove();
                
                // 重新編號所有剩餘的加班人員行
                container.find('.overtime-row').each(function(index) {
                    $(this).attr('data-index', index);
                    
                    // 更新所有 input 和 select 的 name 屬性
                    $(this).find('select, input').each(function() {
                        const name = $(this).attr('name');
                        if (name) {
                            const newName = name.replace(/overtime\[\d+\]/, `overtime[${index}]`);
                            $(this).attr('name', newName);
                        }
                    });
                    
                    // 更新計算框的 id
                    const calculationBox = $(this).find('.calculation-box');
                    if (calculationBox.length) {
                        const oldId = calculationBox.attr('id');
                        const newId = `calculation-${index}`;
                        calculationBox.attr('id', newId);
                    }
                    
                    // 更新事件處理
                    $(this).find('input[name*="[minutes]"]').attr('onchange', `calculateOvertimePay(${index})`);
                    $(this).find('input[name*="[minutes]"]').attr('onkeydown', `handleMinutesKeydown(event, ${index})`);
                });
            } else {
                alert('至少需要保留一筆加班人員記錄');
            }
        }

        // 處理加班分鐘鍵盤輸入
        function handleMinutesKeydown(event, index) {
            const key = event.key;
            const input = event.target;
            const currentValue = input.value;
            
            // 允許的按鍵：數字、退格鍵、刪除鍵、方向鍵、Tab鍵、Enter鍵
            const allowedKeys = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Tab', 'Enter'];
            
            // 如果是允許的按鍵，直接通過
            if (allowedKeys.includes(key)) {
                return true;
            }
            
            // 如果是 Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X 等組合鍵，允許通過
            if (event.ctrlKey && ['a', 'c', 'v', 'x', 'z'].includes(key.toLowerCase())) {
                return true;
            }
            
            // 如果是 Meta+A, Meta+C, Meta+V, Meta+X 等組合鍵（Mac），允許通過
            if (event.metaKey && ['a', 'c', 'v', 'x', 'z'].includes(key.toLowerCase())) {
                return true;
            }
            
            // 其他按鍵都阻止
            event.preventDefault();
            return false;
        }

        // 計算加班費
        function calculateOvertimePay(index) {
            const minutes = parseInt($(`input[name="overtime[${index}][minutes]"]`).val()) || 0;
            const calculationBox = $(`#calculation-${index}`);
            
            if (minutes <= 0) {
                calculationBox.html('<small class="text-muted">請輸入加班分鐘數以計算加班費</small>');
                return;
            }

            const totalHours = minutes / 60;
            let firstTwoHours = 0;
            let remainingHours = 0;

            if (totalHours <= 2) {
                firstTwoHours = totalHours;
                remainingHours = 0;
            } else {
                firstTwoHours = 2;
                remainingHours = totalHours - 2;
            }

            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;
            const timeDisplay = hours > 0 ? `${hours}小時${mins > 0 ? mins + '分鐘' : ''}` : `${mins}分鐘`;

            calculationBox.html(`
                <div class="row">
                    <div class="col-md-6">
                        <small><strong>加班時數：</strong>${timeDisplay}</small><br>
                        <small><strong>前兩小時：</strong>${firstTwoHours.toFixed(2)}小時 (1.34倍)</small><br>
                        <small><strong>後兩小時：：</strong>${remainingHours.toFixed(2)}小時 (1.67倍)</small>
                    </div>
                    <div class="col-md-6">
                        <small><strong>計算方式：</strong></small><br>
                        <small>前兩小時：1.34倍</small><br>
                        <small>後兩小時：：1.67倍</small>
                    </div>
                </div>
            `);
        }

        // 表單驗證
        $('#overtimeForm').on('submit', function(e) {
            const overtimeRows = $('.overtime-row');
            let hasValidData = false;
            let hasError = false;

            overtimeRows.each(function() {
                const userId = $(this).find('select[name*="[user_id]"]').val();
                const minutes = $(this).find('input[name*="[minutes]"]').val();
                const reason = $(this).find('input[name*="[reason]"]').val();
                
                // 檢查是否有填寫任何欄位
                if (userId || minutes || reason) {
                    // 如果有填寫任何欄位，則檢查是否全部必填欄位都已填寫
                    if (!userId) {
                        e.preventDefault();
                        alert('請選擇加班人員！');
                        hasError = true;
                        return false;
                    }
                    if (!minutes || minutes <= 0) {
                        e.preventDefault();
                        alert('請填寫加班分鐘數！');
                        hasError = true;
                        return false;
                    }
                    if (!reason || reason.trim() === '') {
                        e.preventDefault();
                        alert('請填寫加班事由！');
                        hasError = true;
                        return false;
                    }
                    hasValidData = true;
                }
            });

            if (hasError) {
                return false;
            }

            if (!hasValidData) {
                e.preventDefault();
                alert('請至少填寫一筆有效的加班記錄！');
                return false;
            }
        });
    </script>
@endsection

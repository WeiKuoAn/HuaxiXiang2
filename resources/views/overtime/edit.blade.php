@extends('layouts.vertical', ['page_title' => '編輯加班'])

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
                            <li class="breadcrumb-item active">編輯加班</li>
                        </ol>
                    </div>
                    <h4 class="page-title">編輯加班</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('overtime.edit.data', $overtime->id) }}" method="POST" id="overtimeForm">
                            @csrf
                            @method('PUT')
                            
                            <!-- 基本資訊 -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">加班日期<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="overtime_date" value="{{ $overtime->overtime_date->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">狀態</label>
                                        <input type="text" class="form-control" value="{{ $overtime->status_name }}" readonly>
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
                                                            <option value="{{ $user->id }}" {{ $overtime->user_id == $user->id ? 'selected' : '' }}>
                                                                {{ $user->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">加班分鐘<span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="overtime[0][minutes]" min="1" value="{{ $overtime->minutes }}" required onchange="calculateOvertimePay(0)">
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="mb-3">
                                                    <label class="form-label">事由</label>
                                                    <input type="text" class="form-control" name="overtime[0][reason]" value="{{ $overtime->reason }}" placeholder="請輸入加班事由...">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="calculation-box" id="calculation-0">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <small><strong>加班時數：</strong>{{ $overtime->formatted_hours }}</small><br>
                                                            <small><strong>前兩小時：</strong>{{ $overtime->formatted_first_two_hours }} (1.34倍)</small><br>
                                                            <small><strong>剩餘時間：</strong>{{ $overtime->formatted_remaining_hours }} (1.67倍)</small>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <small><strong>計算方式：</strong></small><br>
                                                            <small>前兩小時：1.34倍</small><br>
                                                            <small>剩餘時間：1.67倍</small>
                                                        </div>
                                                    </div>
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

                            <!-- 審核資訊 -->
                            @if($overtime->status != 'pending')
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <h6>審核資訊</h6>
                                            <p><strong>核准者：</strong>{{ $overtime->approver->name ?? '未知' }}</p>
                                            <p><strong>核准時間：</strong>{{ $overtime->approved_at ? $overtime->approved_at->format('Y-m-d H:i:s') : '未知' }}</p>
                                            @if($overtime->status == 'rejected' && $overtime->reject_reason)
                                                <p><strong>拒絕原因：</strong>{{ $overtime->reject_reason }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- 提交按鈕 -->
                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    @if($overtime->canEdit())
                                        <button type="submit" class="btn btn-success waves-effect waves-light m-1">
                                            <i class="fe-check-circle me-1"></i>更新加班
                                        </button>
                                    @endif
                                    <a href="{{ route('overtime.index') }}" class="btn btn-secondary waves-effect waves-light m-1">
                                        <i class="fe-arrow-left me-1"></i>返回列表
                                    </a>
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
            
            // 初始化時計算一次加班費
            calculateOvertimePay(0);
        });

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
                        <small><strong>剩餘時間：</strong>${remainingHours.toFixed(2)}小時 (1.67倍)</small>
                    </div>
                    <div class="col-md-6">
                        <small><strong>計算方式：</strong></small><br>
                        <small>前兩小時：1.34倍</small><br>
                        <small>剩餘時間：1.67倍</small>
                    </div>
                </div>
            `);
        }

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
                                <input type="number" class="form-control" name="overtime[${overtimeIndex}][minutes]" min="1" required onchange="calculateOvertimePay(${overtimeIndex})">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">事由</label>
                                <input type="text" class="form-control" name="overtime[${overtimeIndex}][reason]" placeholder="請輸入加班事由...">
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
            $(button).closest('.overtime-row').remove();
        }

        // 表單驗證
        $('#overtimeForm').on('submit', function(e) {
            const overtimeRows = $('.overtime-row');
            let hasValidData = false;

            overtimeRows.each(function() {
                const userId = $(this).find('select[name*="[user_id]"]').val();
                const minutes = $(this).find('input[name*="[minutes]"]').val();
                
                if (userId && minutes && minutes > 0) {
                    hasValidData = true;
                }
            });

            if (!hasValidData) {
                e.preventDefault();
                alert('請至少填寫一筆有效的加班記錄！');
                return false;
            }
        });
    </script>
@endsection









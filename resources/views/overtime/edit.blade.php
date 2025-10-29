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
                        <!-- 錯誤訊息 -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <strong>發生錯誤：</strong>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                {{ session('success') }}
                            </div>
                        @endif

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
                                    <i class="fe-users me-2"></i>加班資料
                                </h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">人員<span class="text-danger">*</span></label>
                                            <select class="form-control" name="user_id" data-toggle="select" required>
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
                                            <input type="number" class="form-control" name="minutes" min="1" value="{{ $overtime->minutes }}" required onchange="calculateOvertimePay()">
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="mb-3">
                                            <label class="form-label">事由<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="reason" value="{{ $overtime->reason }}" placeholder="請輸入加班事由..." required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="calculation-box" id="calculation-box">
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
                            </div>

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
            calculateOvertimePay();
        });

        // 計算加班費
        function calculateOvertimePay() {
            const minutes = parseInt($('input[name="minutes"]').val()) || 0;
            const calculationBox = $('#calculation-box');
            
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

        // 表單驗證
        $('#overtimeForm').on('submit', function(e) {
            console.log('表單提交事件被觸發');
            
            const userId = $('select[name="user_id"]').val();
            const minutes = $('input[name="minutes"]').val();
            const reason = $('input[name="reason"]').val();
            
            console.log('表單資料:', { userId, minutes, reason });

            if (!userId) {
                e.preventDefault();
                alert('請選擇加班人員！');
                console.log('驗證失敗：缺少人員');
                return false;
            }

            if (!minutes || minutes <= 0) {
                e.preventDefault();
                alert('請填寫加班分鐘數！');
                console.log('驗證失敗：加班分鐘無效');
                return false;
            }

            if (!reason || reason.trim() === '') {
                e.preventDefault();
                alert('請填寫加班事由！');
                console.log('驗證失敗：缺少事由');
                return false;
            }
            
            // 驗證通過，允許表單提交
            console.log('表單驗證通過，開始提交');
            console.log('表單 action:', this.action);
            console.log('表單 method:', this.method);
            return true;
        });
    </script>
@endsection









@extends('layouts.vertical', ['page_title' => '刪除加班'])

@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
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
                            <li class="breadcrumb-item active">刪除加班</li>
                        </ol>
                    </div>
                    <h4 class="page-title">刪除加班記錄</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <h5><i class="fe-alert-triangle me-2"></i>警告</h5>
                            <p class="mb-0">您即將刪除以下加班記錄，此操作無法復原，請確認是否繼續？</p>
                        </div>

                        <!-- 基本資訊 -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">加班日期</label>
                                    <input type="text" class="form-control" value="{{ $overtime->overtime_date->format('Y-m-d') }}" readonly>
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
                            <div class="overtime-row">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">人員</label>
                                            <input type="text" class="form-control" value="{{ $overtime->user->name ?? '未指定' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">加班分鐘</label>
                                            <input type="text" class="form-control" value="{{ $overtime->minutes }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="mb-3">
                                            <label class="form-label">事由</label>
                                            <input type="text" class="form-control" value="{{ $overtime->reason ?: '無' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="calculation-box">
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

                        <!-- 建立資訊 -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="alert alert-secondary">
                                    <h6>建立資訊</h6>
                                    <p><strong>建立者：</strong>{{ $overtime->creator->name ?? '未知' }}</p>
                                    <p><strong>建立時間：</strong>{{ $overtime->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- 操作按鈕 -->
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                @if($overtime->canDelete())
                                    <form action="{{ route('overtime.del.data', $overtime->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger waves-effect waves-light m-1" onclick="return confirm('確定要刪除此加班記錄嗎？此操作無法復原！')">
                                            <i class="fe-trash-2 me-1"></i>確認刪除
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fe-alert-circle me-2"></i>此記錄無法刪除，可能已被核准或拒絕
                                    </div>
                                @endif
                                <a href="{{ route('overtime.index') }}" class="btn btn-secondary waves-effect waves-light m-1">
                                    <i class="fe-arrow-left me-1"></i>返回列表
                                </a>
                            </div>
                        </div>
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
    <!-- third party js ends -->
@endsection









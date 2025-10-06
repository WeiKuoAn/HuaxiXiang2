@extends('layouts.vertical', ['page_title' => '加班管理'])

@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-colreorder-bs5/css/colReorder.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- third party css end -->
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
                            <li class="breadcrumb-item active">加班管理</li>
                        </ol>
                    </div>
                    <h4 class="page-title">加班管理</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- 篩選表單 -->
                        <form action="{{ route('overtime.index') }}" method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="form-label">開始日期</label>
                                    <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">結束日期</label>
                                    <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">狀態</label>
                                    <select class="form-control" name="status">
                                        <option value="">全部狀態</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>待審核</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>已核准</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>已拒絕</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">人員</label>
                                    <select class="form-control" name="user_id">
                                        <option value="">全部人員</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fe-search me-1"></i>查詢
                                    </button>
                                    <a href="{{ route('overtime.index') }}" class="btn btn-secondary me-2">
                                        <i class="fe-refresh-cw me-1"></i>重置
                                    </a>
                                    <a href="{{ route('overtime.export') }}?{{ http_build_query(request()->all()) }}" class="btn btn-success">
                                        <i class="fe-download me-1"></i>匯出Excel
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- 操作按鈕 -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <a href="{{ route('overtime.create') }}" class="btn btn-primary">
                                    <i class="fe-plus me-1"></i>新增加班
                                </a>
                            </div>
                        </div>

                        <!-- 資料表格 -->
                        <div class="table-responsive">
                            <table class="table table-centered table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>加班日期</th>
                                        <th>人員</th>
                                        <th>加班時數</th>
                                        <th>事由</th>
                                        <th>加班費</th>
                                        <th>狀態</th>
                                        <th>建立者</th>

                                        <th>建立時間</th>
                                        <th>動作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($datas as $overtime)
                                        <tr>
                                            <td>{{ $overtime->overtime_date->format('Y-m-d') }}</td>
                                            <td>{{ $overtime->user->name ?? '未指定' }}</td>
                                            <td>{{ $overtime->formatted_hours }}</td>
                                            <td>{{ Str::limit($overtime->reason, 30) }}</td>
                                            <td>${{ number_format($overtime->overtime_pay, 0) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $overtime->status_badge }}">
                                                    {{ $overtime->status_name }}
                                                </span>
                                            </td>
                                            <td>{{ $overtime->creator->name ?? '未知' }}</td>

                                            <td>{{ $overtime->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <div class="btn-group dropdown">
                                                    <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect" data-bs-toggle="dropdown" aria-expanded="false">
                                                        動作 <i class="mdi mdi-arrow-down-drop-circle"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        @if($overtime->canEdit())
                                                            <a class="dropdown-item" href="{{ route('overtime.edit', $overtime->id) }}">
                                                                <i class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯
                                                            </a>
                                                        @endif
                                                        

                                                        
                                                        @if($overtime->canDelete())
                                                            <a class="dropdown-item" href="{{ route('overtime.del', $overtime->id) }}">
                                                                <i class="mdi mdi-delete me-2 text-muted font-18 vertical-middle"></i>刪除
                                                            </a>
                                                        @endif
                                                        

                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">暫無加班記錄</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- 分頁 -->
                        <div class="row">
                            <div class="col-12">
                                {{ $datas->appends(request()->query())->links() }}
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
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-colreorder/js/dataTables.colReorder.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-colreorder-bs5/js/colReorder.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <!-- third party js ends -->

    <script>
        $(document).ready(function() {
            // 初始化 Select2
            $('select[name="user_id"]').select2({
                placeholder: '請選擇人員',
                allowClear: true
            });

            $('select[name="status"]').select2({
                placeholder: '請選擇狀態',
                allowClear: true
            });
        });

        // 顯示拒絕 Modal
        function showRejectModal(overtimeId) {
            $('#rejectForm').attr('action', '{{ url("overtime/reject") }}/' + overtimeId);
            $('#rejectModal').modal('show');
        }

        // 顯示拒絕原因
        function showRejectReason(reason) {
            $('#rejectReasonText').text(reason);
            $('#rejectReasonModal').modal('show');
        }
    </script>
@endsection

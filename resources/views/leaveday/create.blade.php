@extends('layouts.vertical', ['page_title' => '請假申請'])

@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/spectrum-colorpicker2/spectrum-colorpicker2.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/clockpicker/clockpicker.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
        type="text/css" />
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">人事管理</a></li>
                            <li class="breadcrumb-item active">請假申請</li>
                        </ol>
                    </div>
                    <h4 class="page-title">請假申請</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('leave_day.create.data') }}" method="POST" enctype="multipart/form-data"
                            id="uploadForm">
                            @csrf
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">姓名<span class="text-danger">*</span></label>
                                            @if (Auth::user()->job_id == 2)
                                                <select class="form-control" data-toggle="select" data-width="100%"
                                                    name="auth_name" required>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ Auth::user()->name }}" readonly>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <label for="project-priority" class="form-label">假別<span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" data-toggle="select" data-width="100%" name="leave_day"
                                            required>
                                            <option value="" selected>請選擇</option>
                                            @foreach ($leaves as $leave)
                                                <option value="{{ $leave->id }}">{{ $leave->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label class="form-label">請假起始時間<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="start_date" placeholder="起始日期"
                                            required>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label class="form-label">&nbsp;<span class="text-danger"></span></label>
                                        <div class="input-group clockpicker" data-placement="top" data-align="top"
                                            data-autoclose="true">
                                            <input type="text" class="form-control" value="09:00" name="start_time">
                                            <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">請假結束時間<span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="end_date" placeholder="結束時間"
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">&nbsp;<span class="text-danger"></span></label>
                                            <div class="input-group clockpicker" data-placement="top" data-align="top"
                                                data-autoclose="true">
                                                <input type="text" class="form-control" value="18:00"
                                                    name="end_time">
                                                <span class="input-group-text"><i
                                                        class="mdi mdi-clock-outline"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <label for="project-priority" class="form-label">請假單位<span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" data-toggle="select" data-width="100%"
                                            name="unit" required>
                                            <option value="day">天</option>
                                            <option value="hour">小時</option>
                                            <option value="week">週</option>
                                            <option value="month">月</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">總請假數量<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="total" value=""
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">選擇附件 (PDF, JPG, PNG)<span
                                            class="text-danger">*</span></label>
                                    <input type="file" class="form-control" name="file1" id="fileInput"
                                        accept=".pdf,.jpg,.png">
                                </div>
                                <div class="mb-3" id="previewSection" style="display: none;">
                                    <label class="form-label">檔案預覽：</label>
                                    <a href="#" id="filePreview" target="_blank">點我預覽</a>
                                    <input type="hidden" class="form-control" id="filename" name="filename" value="">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">備註</label>
                                    <textarea class="form-control" rows="3" placeholder="" name="comment"></textarea>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success waves-effect waves-light m-1"><i
                                            class="fe-check-circle me-1"></i>新增</button>
                                    <button type="reset" class="btn btn-secondary waves-effect waves-light m-1"
                                        onclick="history.go(-1)"><i class="fe-x me-1"></i>回上一頁</button>
                                </div>
                            </div>
                        </form>
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div>



        <!-- end row-->

    </div> <!-- container -->
@endsection

@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/spectrum-colorpicker2/spectrum-colorpicker2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/clockpicker/clockpicker.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- third party js ends -->

    <!-- demo app -->
    <script src="{{ asset('assets/js/pages/form-pickers.init.js') }}"></script>
    <script>
        document.getElementById('fileInput').addEventListener('change', function() {
            const formData = new FormData();
            const fileInput = document.getElementById('fileInput');

            if (fileInput.files.length === 0) {
                alert('請選擇一個檔案！');
                return;
            }

            formData.append('file', fileInput.files[0]);

            fetch("{{ route('leave_day.upload_file') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 成功提示
                        alert('上傳成功！');

                        // 更新預覽連結
                        const previewSection = document.getElementById('previewSection');
                        const filePreview = document.getElementById('filePreview');
                        const filename = document.getElementById('filename');

                        filePreview.href = data.file_url; // 設定超連結的 href
                        filename.value = data.file_url; // 設定檔名
                        filePreview.textContent = "點我預覽"; // 設定顯示文字
                        previewSection.style.display = 'block';
                    } else {
                        alert(data.message || '上傳失敗，請重試！');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('上傳過程中發生錯誤，請重試！');
                });
        });
    </script>
    <!-- end demo js-->
@endsection

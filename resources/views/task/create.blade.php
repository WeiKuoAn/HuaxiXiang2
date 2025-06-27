@extends('layouts.vertical', ['page_title' => '新增待辦'])

@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">設定管理</a></li>
                            <li class="breadcrumb-item active">新增待辦</li>
                        </ol>
                    </div>
                    <h4 class="page-title">新增待辦</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('task.create.data') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label>待辦事項<span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                                <input type="hidden" name="start_date" class="form-control" value="{{ $now }}"
                                    required>
                                <input type="hidden" name="start_time" class="form-control" value="09:00" required>
                                <div class="col-md-12 mb-3">
                                    <label>預計結束日期<span class="text-danger">*</span></label>
                                    <div class="row g-2">
                                        <div class="col-md">
                                            <input type="date" name="end_date" class="form-control" value=""
                                                required>
                                        </div>
                                        <div class="col-md">
                                            <input type="time" name="end_time" class="form-control" value="18:00"
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label>待辦事項說明</label>
                                    <textarea name="description" class="form-control" rows="5"></textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label>指派給</label>
                                    <select name="assigned_to" class="form-select">
                                        <option value="">不指定</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label>狀態</label>
                                    <select name="status" class="form-select">
                                        <option value="0">待辦</option>
                                        <option value="1">已完成</option>
                                    </select>
                                </div>
                            </div>
                    </div>
                    <!-- end row -->


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

    <script src="{{ asset('assets/js/twzipcode-1.4.1-min.js') }}"></script>
    <script src="{{ asset('assets/js/twzipcode.js') }}"></script>
    <script src="{{ asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <!-- third party js ends -->

    <!-- demo app -->
    <script src="{{ asset('assets/js/pages/create-project.init.js') }}"></script>
    <!-- end demo js-->
@endsection

@extends('layouts.vertical', ['page_title' => '新增功德件'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">功德件管理</a></li>
                            <li class="breadcrumb-item active">新增功德件</li>
                        </ol>
                    </div>
                    <h4 class="page-title">新增功德件</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <form action="{{ route('merit.create.data') }}" method="POST">

            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            @csrf
                            <div class="row">
                                <div class="mb-3">
                                    <label class="form-label">日期<span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="date" value="" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">品種<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="variety" value="" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">公斤<span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="kg" value="" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">功德人姓名<span class="text-danger">*</span></label>
                                    <select class="form-control" name="user_id" required>
                                        <option value="">請選擇功德人</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> <!-- end col-->
                        </div>
                        <!-- end row -->
                        <div class="row mb-3">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-success waves-effect waves-light m-1"><i
                                        class="fe-check-circle me-1"></i>新增</button>
                                <button type="reset" class="btn btn-secondary waves-effect waves-light m-1"
                                    onclick="history.go(-1)"><i class="fe-x me-1"></i>回上一頁</button>
                            </div>
                        </div>
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col-->
            <!-- end row-->
        </form>
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

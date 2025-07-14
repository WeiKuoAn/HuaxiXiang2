@extends('layouts.vertical', ['page_title' => '新增業務報廢單'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">業務管理</a></li>
                            <li class="breadcrumb-item active">新增業務報廢單</li>
                        </ol>
                    </div>
                    <h4 class="page-title">新增業務報廢單</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <form action="{{ route('sale.scrapped.create.data') }}" method="POST">
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            @csrf
                            <div class="row">
                                <div class="mb-3">
                                    <label for="sale_date" class="form-label">日期<span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="sale_date" name="sale_date" value="{{ $date }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="sale_on" class="form-label">單號<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="sale_on" name="sale_on" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">報廢備註</label>
                                    <textarea class="form-control" rows="3" placeholder="" name="comm"></textarea>
                                </div>
                            </div> <!-- end col-->
                        </div>

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
        </form>
    </div>
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

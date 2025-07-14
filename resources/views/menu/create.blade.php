@extends('layouts.vertical', ['page_title' => '新增選單'])

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
                            <li class="breadcrumb-item active">新增選單</li>
                        </ol>
                    </div>
                    <h4 class="page-title">新增選單</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('menu.create.data') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="mb-3">
                                    <label class="form-label">選單名稱<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">選單唯一值<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="slug" value="" required>
                                </div>
                                <div class="mb-3">
                                    <label for="project-priority" class="form-label">類別<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" data-toggle="select" data-width="100%" name="type">
                                        <option value="main">主要</option>
                                        <option value="app" selected>一般</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="project-priority" class="form-label">父類別<span
                                            class="text-danger">*</span></label>
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="parent_id">
                                        <option value="" selected>請選擇...</option>
                                        @foreach ($datas as $data)
                                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">網址<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="url" value="" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">icon<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="icon" value="">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">排序<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="sort" value="0" required>
                                </div>
                                <div class="mb-3">
                                    <label for="project-priority" class="form-label">備註<span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control" name="comment" rows="3"></textarea>
                                </div>
                            </div> <!-- end col-->

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

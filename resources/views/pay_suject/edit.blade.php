@extends('layouts.vertical', ['page_title' => '編輯支出科目'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">支出管理</a></li>
                            <li class="breadcrumb-item active">編輯支出科目</li>
                        </ol>
                    </div>
                    <h4 class="page-title">編輯支出科目</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('pay.suject.edit.data', $data->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">科目名稱<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name"
                                                value="{{ $data->name }}" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="project-priority" class="form-label">會計所屬科目<span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" data-toggle="select" data-width="100%"
                                            name="suject_type">
                                            <option value="0" @if ($data->suject_type == '0') selected @endif>營業費用</option>
                                            <option value="1" @if ($data->suject_type == '1') selected @endif>營業成本</option>
                                            <option value="2" @if ($data->suject_type == '2') selected @endif>銀行轉帳-手續費</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">排序<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="seq"
                                                value="{{ $data->seq }}" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="project-priority" class="form-label">專員查看狀態<span
                                                class="text-danger">*</span></label>

                                        <select class="form-control" data-toggle="select" data-width="100%" name="view_status">
                                            <option value="0" @if ($data->view_status == '0') selected @endif>開啟</option>
                                            <option value="1" @if ($data->view_status == '1') selected @endif>關閉</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="project-priority" class="form-label">科目狀態<span
                                                class="text-danger">*</span></label>

                                        <select class="form-control" data-toggle="select" data-width="100%" name="status">
                                            <option value="up" @if ($data->status == 'up') selected @endif>上架
                                            </option>
                                            <option value="down" @if ($data->status == 'down') selected @endif>下架
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">備註</label>
                                        <textarea class="form-control" rows="3" placeholder="" name="comment">{{ $data->comment }}</textarea>
                                    </div>
                                </div> <!-- end col-->

                            </div>
                            <!-- end row -->


                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success waves-effect waves-light m-1"><i
                                            class="fe-check-circle me-1"></i>修改</button>
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

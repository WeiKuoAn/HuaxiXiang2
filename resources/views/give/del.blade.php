@extends('layouts.vertical', ['page_title' => '刪除贈送'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">贈送管理</a></li>
                            <li class="breadcrumb-item active">刪除贈送</li>
                        </ol>
                    </div>
                    <h4 class="page-title">刪除贈送</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <form action="{{ route('give.del.data', $data->id) }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="mb-2">
                                    <label for="project-priority" class="form-label">單號<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="sale_on" value="{{ $data->sale_on }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">贈送物<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="value" value="{{ $data->value }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">價格<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="price" value="{{ $data->price }}" required>
                                </div>
                                <div class="mb-2">
                                    <label for="project-priority" class="form-label">贈送人員<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" data-toggle="select" data-width="100%" name="user_id">
                                        <option value="">請選擇</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" @if ($data->user_id == $user->id) selected @endif>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-success waves-effect waves-light m-1"><i
                                        class="fe-check-circle me-1"></i>刪除</button>
                                <button type="reset" class="btn btn-secondary waves-effect waves-light m-1"
                                    onclick="history.go(-1)"><i class="fe-x me-1"></i>回上一頁</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

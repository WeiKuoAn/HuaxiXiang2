@extends('layouts.vertical', ['page_title' => '刪除報廢單'])

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
                            <li class="breadcrumb-item active">刪除報廢單</li>
                        </ol>
                    </div>
                    <h4 class="page-title">刪除報廢單</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <form action="{{ route('sale.scrapped.destroy', $scrapped->id) }}" method="POST">
            @method('DELETE')
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            @csrf
                            <div class="row">
                                <div class="mb-3">
                                    <label for="sale_date" class="form-label">日期</label>
                                    <input type="date" class="form-control" id="sale_date" value="{{ $scrapped->sale_date }}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="sale_on" class="form-label">單號</label>
                                    <div class="input-group">
                                        <span class="input-group-text">No.</span>
                                        <input type="text" class="form-control" id="sale_on" value="{{ str_replace('No.', '', $scrapped->sale_on) }}" readonly>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">報廢原因</label>
                                    <input type="text" class="form-control" value="{{ $scrapped->comm }}" readonly>
                                </div>
                            </div> <!-- end col-->
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-danger waves-effect waves-light m-1"><i
                                        class="fe-trash-2 me-1"></i>確認刪除</button>
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

    <script>
        $(document).ready(function() {
            // 刪除確認對話框
            $('form').on('submit', function(e) {
                if (!confirm('確定要刪除這筆報廢單嗎？此操作無法復原。')) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>

    <!-- demo app -->
    <script src="{{ asset('assets/js/pages/create-project.init.js') }}"></script>
    <!-- end demo js-->
@endsection

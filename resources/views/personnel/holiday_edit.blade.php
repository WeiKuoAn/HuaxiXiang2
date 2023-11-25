@extends('layouts.vertical', ["page_title"=> "編輯專員每月休假"])

@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item active">編輯專員每月休假</li>
                    </ol>
                </div>
                <h4 class="page-title">編輯專員每月休假</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('personnel.holidays.edit.data',[$user->id,$year,$month]) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="mb-3">
                                <div class="mb-3">
                                   <label class="form-label">年度<span class="text-danger">*</span></label>
                                   <input type="text" class="form-control" name="year" value="{{ $year }}" readonly required>
                               </div>
                           </div>
                            <div class="mb-3">
                                <label for="project-priority" class="form-label">月份<span class="text-danger">*</span></label>
                                <select class="form-control" data-toggle="select" data-width="100%" name="month">
                                    <option value="{{ $month }}" >{{ $month }}月</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <div class="mb-3">
                                    <label class="form-label">專員<span class="text-danger">*</span></label>
                                    <select class="form-control" data-toggle="select" data-width="100%" name="user_id">
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    </select>
                                </div>
                           </div>
                           <div class="mb-3">
                            <div class="mb-3">
                               <label class="form-label">休假天數<span class="text-danger">*</span></label>
                               <input type="text" class="form-control" name="holiday" value="{{ $data->holiday }}"  required>
                           </div>
                       </div>
                        </div> <!-- end col-->
                        
                    </div>
                    <!-- end row -->


                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-success waves-effect waves-light m-1"><i class="fe-check-circle me-1"></i>編輯</button>
                            <button type="reset" class="btn btn-secondary waves-effect waves-light m-1" onclick="history.go(-1)"><i class="fe-x me-1"></i>回上一頁</button>
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
<script src="{{asset('assets/libs/dropzone/dropzone.min.js')}}"></script>
<script src="{{asset('assets/libs/select2/select2.min.js')}}"></script>
<script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>
<!-- third party js ends -->

<!-- demo app -->
<script src="{{asset('assets/js/pages/create-project.init.js')}}"></script>
<!-- end demo js-->
@endsection
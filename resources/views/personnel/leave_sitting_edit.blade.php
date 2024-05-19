@extends('layouts.vertical', ["page_title"=> "編輯假別天數"])

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
                        <li class="breadcrumb-item active">編輯假別天數</li>
                    </ol>
                </div>
                <h4 class="page-title">編輯假別天數</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <form action="{{ route('personnel.leavesitting.edit.data',$data->id) }}" method="POST">
        @csrf
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">年度/月份</h5>
                    
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="mb-3">
                                <div class="mb-3">
                                   <label class="form-label">年度<span class="text-danger">*</span></label>
                                   <input type="text" class="form-control" name="year" value="{{ $data->year }}" readonly required>
                               </div>
                            </div>
                            <div class="mb-3">
                                <label for="project-priority" class="form-label">假別名稱<span class="text-danger">*</span></label>
                                <select class="form-control" data-toggle="select" data-width="100%" name="leave_id">
                                    @foreach($leaves as $leave)
                                        <option value="{{ $leave->id }}" @if($data->leave_id == $leave->id) selected @endif>{{ $leave->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="project-priority" class="form-label">假別核定天數<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="approved_days" value="{{ $data->approved_days }}" required>
                            </div>
                        </div> <!-- end col-->
                    </div>
                    <!-- end row -->
                </div> <!-- end card-body -->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>

    <!-- end row-->
    <div class="row mt-3">
        <div class="col-6 text-center">
            <button type="submit" class="btn btn-success waves-effect waves-light m-1"><i class="fe-check-circle me-1"></i>編輯</button>
            <button type="reset" class="btn btn-secondary waves-effect waves-light m-1" onclick="history.go(-1)"><i class="fe-x me-1"></i>回上一頁</button>
        </div>
    </div>
</div> <!-- container -->
</form>

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
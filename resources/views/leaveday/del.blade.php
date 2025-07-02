@extends('layouts.vertical', ["page_title"=> "請假刪除"])

@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/spectrum-colorpicker2/spectrum-colorpicker2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/clockpicker/clockpicker.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css')}}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">個人管理</a></li>
                        <li class="breadcrumb-item active">請假刪除</li>
                    </ol>
                </div>
                <h4 class="page-title">請假刪除</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('leave_day.del.data',$data->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="mb-3">
                                <div class="mb-3">
                                   <label class="form-label">姓名<span class="text-danger">*</span></label>
                                   <input type="text" class="form-control" name="name" value="{{ $data->user_name->name }}" readonly>
                                   <input type="hidden" class="form-control" name="user_id" value="{{ $data->user_id }}" readonly>
                               </div>
                           </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="mb-3">
                                <label for="project-priority" class="form-label">假別<span class="text-danger">*</span></label>
                                <select class="form-control" data-toggle="select" data-width="100%" name="leave_day">
                                    @foreach($leaves as $leave)
                                        <option value="{{ $leave->id }}" @if($data->leave_day ==  $leave->id) selected @endif>{{ $leave->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="mb-3">
                                <label class="form-label">請假起始時間<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" placeholder="起始日期" value="{{ date('Y-m-d', strtotime($data->start_datetime)) }}">
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;<span class="text-danger"></span></label>
                                <div class="input-group clockpicker" data-placement="top" data-align="top" data-autoclose="true">
                                    <input type="text" class="form-control" name="start_time" value="{{ date('H:i', strtotime($data->start_datetime)) }}">
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
                                   <input type="date" class="form-control"  name="end_date" placeholder="結束時間"  value="{{ date('Y-m-d', strtotime($data->end_datetime)) }}">
                               </div>
                           </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="mb-3">
                                <div class="mb-3">
                                   <label class="form-label">&nbsp;<span class="text-danger"></span></label>
                                   <div class="input-group clockpicker" data-placement="top" data-align="top" data-autoclose="true">
                                    <input type="text" class="form-control" name="end_time" value="{{ date('H:i', strtotime($data->end_datetime)) }}">
                                    <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                </div>
                               </div>
                           </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="mb-3">
                                <label for="project-priority" class="form-label">請假單位<span class="text-danger">*</span></label>
                                <select class="form-control" data-toggle="select" data-width="100%" name="unit">
                                    <option value="day" @if($data->unit == 'day') selected @endif>天</option>
                                    <option value="hour" @if($data->unit == 'hour') selected @endif>小時</option>
                                    <option value="week" @if($data->unit == 'week') selected @endif>週</option>
                                    <option value="month" @if($data->unit == 'month') selected @endif>月</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <div class="mb-3">
                                   <label class="form-label">總請假數量<span class="text-danger">*</span></label>
                                   <input type="text" class="form-control" name="total" value="{{ $data->total }}" >
                               </div>
                           </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">備註</label>
                            <textarea class="form-control" rows="3" placeholder="" name="comment">{{ $data->comment }}</textarea>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-success waves-effect waves-light m-1"><i class="fe-check-circle me-1"></i>刪除</button>
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
<script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('assets/libs/spectrum-colorpicker2/spectrum-colorpicker2.min.js')}}"></script>
<script src="{{asset('assets/libs/clockpicker/clockpicker.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
<!-- third party js ends -->

<!-- demo app -->
<script src="{{asset('assets/js/pages/form-pickers.init.js')}}"></script>
<!-- end demo js-->
@endsection
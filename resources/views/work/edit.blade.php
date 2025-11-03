@extends('layouts.vertical', ["page_title"=> "編輯出勤紀錄"])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">用戶管理</a></li>
                        <li class="breadcrumb-item active">編輯出勤</li>
                    </ol>
                </div>
                <h4 class="page-title">編輯出勤</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('user.work.edit.data',$work->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="mb-3">
                                <div class="mb-3">
                                   <label class="form-label">上班時間</label>
                                   <input type="datetime" class="form-control" name="worktime" value="{{ date('H:i:s', strtotime($work->worktime)) == '00:00:01' ? '' : $work->worktime }}">
                                   @if(date('H:i:s', strtotime($work->worktime)) == '00:00:01')
                                       <small class="text-muted">此時間未打卡</small>
                                   @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="mb-3">
                                <div class="mb-3">
                                   <label class="form-label">下班時間</label>
                                   <input type="datetime" class="form-control" name="dutytime" value="{{ date('H:i:s', strtotime($work->dutytime)) == '23:59:59' ? '' : $work->dutytime }}">
                                   @if(date('H:i:s', strtotime($work->dutytime)) == '23:59:59')
                                       <small class="text-muted">此時間未打卡</small>
                                   @endif
                               </div>
                           </div>
                        </div> <!-- end col-->
                        <div class="col-xl-12">
                            <div class="mb-3">
                                <div class="mb-3">
                                   <label class="form-label">總時數<span class="text-danger">*</span></label>
                                   <input type="text" class="form-control" name="total" value="{{ $work->total }}" required>
                               </div>
                           </div>
                        </div> <!-- end col-->
                        <div class="mb-3">
                                <label for="project-priority" class="form-label">狀態<span class="text-danger">*</span></label>

                                <select class="form-control" data-toggle="select" data-width="100%" name="status">
                                    <option value="0" @if($work->status == '0') selected @endif>值班</option>
                                    <option value="1" @if($work->status == '1') selected @endif>補簽</option>
                                </select>
                            </div>
                        </div> <!-- end col-->
                        <div>
                            <label class="form-label">備註</label>
                            <textarea class="form-control" rows="3" placeholder="" name="remark">{{ $work->remark }}</textarea>
                        </div>
                    </div>
                    <!-- end row -->


                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-success waves-effect waves-light m-1"><i class="fe-check-circle me-1"></i>修改</button>
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
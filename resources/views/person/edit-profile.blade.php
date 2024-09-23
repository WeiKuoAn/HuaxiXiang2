@extends('layouts.vertical', ["page_title"=> "Add & Edit Products"])

@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/quill/quill.min.css')}}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item active">編輯個資</li>
                    </ol>
                </div>
                <h4 class="page-title">編輯個資</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->


    <div class="row">
        <div class="col-lg-12">
            @if ($hint == '1')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    恭喜你修改個人資料成功！@if(Auth::user()->level == 2)若之後修改人事資料請聯繫人資主管！@endif
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
                    
            @if(Auth::user()->level == 2 &&  $user->state == 0)
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    恭喜你修改個人資料成功！@if(Auth::user()->level == 2)若之後修改人事資料請聯繫人資主管！@endif
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        <div class="col-lg-6">
            <div class="card">
                
                <div class="card-body">
                    
                    <form action="{{ route('user-profile.data',$user->id) }}" method="POST">
                    @csrf
                    <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">個人資訊</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">入職時間<span class="text-danger">*</span></label>
                        <input type="date" class="form-control" data-toggle="flatpicker" name="entry_date" value="{{ $user->entry_date }}" readonly>
                    </div>

                    {{-- <div class="mb-3">
                        <label class="form-label">特休天數<span class="text-danger"></span></label>
                        <input type="text" class="form-control" name="name" value="0">
                    </div> --}}

                    <div class="mb-3">
                        <label for="project-priority" class="form-label">分館<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="job_id" @if(isset($user->branch_data))   value="{{ $user->branch_data->name }}" @endif readonly>
                    </div>

                    <div class="mb-3">
                        <label for="project-priority" class="form-label">職稱<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="job_id" @if(isset($user->job_data))   value="{{ $user->job_data->name }}" @endif readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">姓名<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ $user->name }}">
                    </div>

                    <div class="mb-3">
                        <label for="project-priority" class="form-label">生理性別<span class="text-danger">*</span></label>

                        <select class="form-control" data-toggle="select" data-width="100%" name="sex">
                            <option value="" selected >請選擇</option>
                            <option value="男生" @if($user->sex == '男生') selected @endif>男生</option>
                            <option value="女生" @if($user->sex == '女生') selected @endif>女生</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">生日<span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="birthday" value="{{ $user->birthday }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">身份證<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="ic_card" value="{{ $user->ic_card }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">聯絡電話<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="mobile" value="{{ $user->mobile }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">信箱E-mail<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="email" value="{{ $user->email }}">
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <label class="form-label">就讀學校/科系<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="education_school" value="{{ $user->education_school }}">
                        </div>
                        <div class="col-lg-3">
                            <label class="form-label">學歷<span class="text-danger">*</span></label>
                            <select class="form-control" data-toggle="select" data-width="100%" name="education_level">
                                <option value="請選擇" @if($user->education_level == '請選擇') selected @endif>請選擇</option>
                                <option value="國中" @if($user->education_level == '國中') selected @endif>國中</option>
                                <option value="高中" @if($user->education_level == '高中') selected @endif>高中</option>
                                <option value="大學" @if($user->education_level == '大學') selected @endif>大學</option>
                                <option value="碩士" @if($user->education_level == '碩士') selected @endif>碩士</option>
                                <option value="博士" @if($user->education_level == '博士') selected @endif>博士</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label class="form-label">狀態<span class="text-danger">*</span></label>
                            <select class="form-control" data-toggle="select" data-width="100%" name="is_graduated">
                                <option value="請選擇" @if($user->is_graduated == '請選擇') selected @endif>請選擇</option>
                                <option value="畢業" @if($user->is_graduated == '畢業') selected @endif>畢業</option>
                                <option value="肄業" @if($user->is_graduated == '肄業') selected @endif>肄業</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">婚姻狀況<span class="text-danger">*</span></label>
                        <select class="form-control" data-toggle="select" data-width="100%" name="marriage">
                            <option value="請選擇" @if($user->marriage == '請選擇') selected @endif>請選擇</option>
                            <option value="結婚" @if($user->marriage == '結婚') selected @endif>結婚</option>
                            <option value="未婚" @if($user->marriage == '未婚') selected @endif>未婚</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">通訊地址<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="address" value="{{ $user->address }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">戶籍地址<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="census_address" value="{{ $user->census_address }}">
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <!-- Date View -->
                            <div class="mb-3">
                                <label class="form-label">薪資帳戶<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{ $user->bank_id }}" placeholder="銀行代碼" name="bank_id">
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <!-- Date View -->
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <input type="text" class="form-control"name="bank_number" value="{{ $user->bank_number }}" placeholder="帳戶號碼">
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end card -->
        </div> <!-- end col -->

        <div class="col-lg-6">

            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">緊急聯絡人</h5>

                    <div class="mb-3">
                        <label class="form-label">緊急聯絡人姓名<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="urgent_name" value="{{ $user->urgent_name }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">緊急聯絡人關係<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="urgent_relation" value="{{ $user->urgent_relation }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">緊急聯絡人電話<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="urgent_mobile" value="{{ $user->urgent_mobile }}">
                    </div>
                </div>
            </div> <!-- end card -->

        </div> <!-- end col-->
    </div>
    <!-- end row -->

    @if($user->state == 1 || Auth::user()->level == 0 || Auth::user()->level == 1)
    <div class="row">
        <div class="col-12">
            <div class="text-center mb-3">
                <button type="submit" class="btn w-sm btn-success waves-effect waves-light">修改</button>
                <button type="button" class="btn w-sm btn-secondary waves-effect" onclick="history.go(-1)">回上一頁</button>
                {{-- <button type="submit" class="btn w-sm btn-danger waves-effect waves-light">Delete</button> --}}
            </div>
        </div> <!-- end col -->
    </div>
    @endif
    </form>
    <!-- end row -->


    <!-- file preview template -->
    <div class="d-none" id="uploadPreviewTemplate">
        <div class="card mt-1 mb-0 shadow-none border">
            <div class="p-2">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <img data-dz-thumbnail src="#" class="avatar-sm rounded bg-light" alt="">
                    </div>
                    <div class="col ps-0">
                        <a href="javascript:void(0);" class="text-muted fw-bold" data-dz-name></a>
                        <p class="mb-0" data-dz-size></p>
                    </div>
                    <div class="col-auto">
                        <!-- Button -->
                        <a href="" class="btn btn-link btn-lg text-muted" data-dz-remove>
                            <i class="dripicons-cross"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div> <!-- container -->
@endsection

@section('script')
<!-- third party js -->
<script src="{{asset('assets/libs/select2/select2.min.js')}}"></script>
<script src="{{asset('assets/libs/dropzone/dropzone.min.js')}}"></script>
<script src="{{asset('assets/libs/quill/quill.min.js')}}"></script>
<!-- third party js ends -->

<!-- demo app -->
<script src="{{asset('assets/js/pages/form-fileuploads.init.js')}}"></script>
<script src="{{asset('assets/js/pages/add-product.init.js')}}"></script>
<!-- end demo js-->
@endsection
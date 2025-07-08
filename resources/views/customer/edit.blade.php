@extends('layouts.vertical', ['page_title' => 'Create Project'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">客戶管理</a></li>
                            <li class="breadcrumb-item active">編輯客戶</li>
                        </ol>
                    </div>
                    <h4 class="page-title">編輯客戶</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('customer.edit.data', $customer->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <label for="project-priority" class="form-label">群組<span
                                                class="text-danger">*</span></label>

                                        <select class="form-control" data-toggle="select" data-width="100%" name="group_id">
                                            @foreach ($groups as $group)
                                                <option value="{{ $group->id }}"
                                                    @if ($customer->group_id == $group->id) selected @endif>{{ $group->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">姓名<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name"
                                                value="{{ $customer->name }}" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">電話<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="mobile"
                                                value="{{ $customer->mobile }}" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="form-label">地址<span class="text-danger">*</span></label>
                                        <div id="twzipcode">
                                            <div data-role="county" data-value="{{ $customer->county }}"></div>
                                        </div>
                                        <div class="mb-3 mt-1">
                                            <input type="text" class="form-control" name="address" placeholder="輸入地址"
                                                value="{{ $customer->address }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="form-label">舊地址<span class="text-danger">*</span></label>
                                        <div class="mb-3 mt-1">
                                            <input type="text" class="form-control" name="old-address" placeholder="輸入地址"
                                                value="{{ $customer->address }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="form-label">備註</label>
                                        <div class="mb-3 mt-1">
                                            <textarea class="form-control" rows="3" placeholder="" name="comment">{{ $customer->comment }}</textarea>
                                        </div>
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
    <script>
        $(document).ready(function() {
            $("#twzipcode").twzipcode({
                zipcodeIntoDistrict: true,
                css: [" form-control", "mt-1 form-control", "mt-1 form-control"], // 自訂 "城市"、"地區" class 名稱 
                countyName: "county", // 自訂城市 select 標籤的 name 值
                districtName: "district", // 自訂地區 select 標籤的 name 值
                countySel: '{{ $customer->county }}',
                districtSel: '{{ $customer->district }}',
            });
        });
    </script>
    <!-- third party js ends -->

    <!-- demo app -->
    <script src="{{ asset('assets/js/pages/create-project.init.js') }}"></script>
    <!-- end demo js-->
@endsection

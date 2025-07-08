@extends('layouts.vertical', ['page_title' => '編輯除戶記錄'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">除戶管理</a></li>
                            <li class="breadcrumb-item active">編輯除戶記錄</li>
                        </ol>
                    </div>
                    <h4 class="page-title">編輯除戶記錄</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('deregistration.edit.data', $data->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">客戶名稱</label>
                                    <select class="form-control" data-toggle="select2" data-width="100%" name="customer_id"
                                        id="customer_id">
                                        <option value="">請選擇...</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                @if ($data->customer_id == $customer->id) selected @endif>No.{{ $customer->id }}
                                                {{ $customer->name }}（{{ $customer->mobile }}）</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">晶片號碼<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="chipNumberInput" name="number"
                                                value="{{ $data->number }}" required maxlength="15"
                                                title="晶片號碼長度必須為 10, 12, 或 15 個字元">
                                            <small class="form-text text-muted"
                                                id="chipNumberCount">目前字數：{{ strlen($data->number) }}</small><br>
                                            <small class="text-danger">※晶片號碼長度必須為 10, 12, 或 15 個字元</small>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="project-priority" class="form-label">登記飼主<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="registrant"
                                            value="{{ $data->registrant }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="project-priority" class="form-label">身分證<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="ic_card"
                                            value="{{ $data->ic_card }}" required maxlength="10" pattern="[A-Za-z]\d{9}"
                                            title="身分證號碼格式為：1個英文字母 + 9個數字">
                                        <small class="text-danger">※身分證號碼格式為：1個英文字母 + 9個數字</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="project-priority" class="form-label">寶貝名</label>
                                        <input type="text" class="form-control" name="pet_name"
                                            value="{{ $data->pet_name }}">
                                    </div>
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">品種</label>
                                            <input type="text" class="form-control" name="variety"
                                                value="{{ $data->variety }}">
                                        </div>
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
                                            class="fe-check-circle me-1"></i>編輯</button>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 晶片號碼即時字數統計
            const numberInput = document.getElementById('chipNumberInput');
            const numberCount = document.getElementById('chipNumberCount');

            if (numberInput && numberCount) {
                numberInput.addEventListener('input', function() {
                    const currentLength = numberInput.value.length;
                    numberCount.textContent = `目前字數：${currentLength}`;
                });
            }

            const form = document.querySelector(
                'form[action="{{ route('deregistration.edit.data', $data->id) }}"]');
            if (form) {
                form.addEventListener('submit', function(event) {
                    // 晶片號碼驗證
                    const numberInput = form.querySelector('input[name="number"]');
                    const numberValue = numberInput.value;
                    const numberLength = numberValue.length;
                    if (numberLength !== 10 && numberLength !== 12 && numberLength !== 15) {
                        alert('晶片號碼長度必須為 10, 12, 或 15 個字元。');
                        event.preventDefault(); // 阻止表單提交
                        numberInput.focus();
                        return;
                    }

                    // 身分證驗證
                    const icCardInput = form.querySelector('input[name="ic_card"]');
                    const icCardValue = icCardInput.value;
                    const icCardPattern = /^[A-Za-z]\d{9}$/;
                    if (!icCardPattern.test(icCardValue)) {
                        alert('身分證號碼格式不正確，應為 1 個英文字母加上 9 個數字。');
                        event.preventDefault(); // 阻止表單提交
                        icCardInput.focus();
                        return;
                    }
                });
            }
        });
    </script>
@endsection

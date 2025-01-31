@extends('layouts.vertical', ['page_title' => '刪除達標'])

@section('css')
    <link href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Huaxixiang</a></li>
                            <li class="breadcrumb-item"><a href="#">達標管理</a></li>
                            <li class="breadcrumb-item active">刪除達標</li>
                        </ol>
                    </div>
                    <h4 class="page-title">刪除達標</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('target.del.data', $data->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <!-- 達標類別 -->
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <label class="form-label">達標類別<span class="text-danger">*</span></label>
                                        <select class="form-control" name="category_id" required>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $data->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- 設定達標的職稱 -->
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <label class="form-label">設定達標的職稱<span class="text-danger">*</span></label>
                                        @foreach ($jobs as $job)
                                            @php
                                                $jobIds = is_string($data->job_id)
                                                    ? json_decode($data->job_id, true)
                                                    : $data->job_id;
                                                $jobIds = is_array($jobIds) ? $jobIds : []; // 確保它是陣列
                                            @endphp
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="job_ids[]"
                                                    id="job-{{ $job->id }}" value="{{ $job->id }}"
                                                    {{ in_array($job->id, $jobIds) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="job-{{ $job->id }}">
                                                    {{ $job->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- 達標頻率 -->
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <label class="form-label">達標頻率<span class="text-danger">*</span></label>
                                        <select class="form-control" name="frequency" required>
                                            <option value="月" {{ $data->frequency == '月' ? 'selected' : '' }}>每月
                                            </option>
                                            <option value="季" {{ $data->frequency == '季' ? 'selected' : '' }}>每季
                                            </option>
                                            <option value="半年" {{ $data->frequency == '半年' ? 'selected' : '' }}>每半年
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- 達標條件 (金額、數量或兩者) -->
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <label class="form-label">達標條件<span class="text-danger">*</span></label>
                                        <select class="form-control" name="target_condition" id="target_condition" required>
                                            <option value="金額" {{ $data->target_condition == '金額' ? 'selected' : '' }}>
                                                總金額</option>
                                            <option value="數量" {{ $data->target_condition == '數量' ? 'selected' : '' }}>
                                                賣出數量</option>
                                            <option value="金額+數量"
                                                {{ $data->target_condition == '金額+數量' ? 'selected' : '' }}>金額 + 數量</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- 設定達標金額 -->
                                <div class="col-xl-12" id="target_amount_div">
                                    <div class="mb-3">
                                        <label class="form-label">設定達標金額<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="target_amount" min="0"
                                            value="{{ $data->target_amount }}">
                                    </div>
                                </div>

                                <!-- 設定賣出數量 -->
                                <div class="col-xl-12" id="target_quantity_div">
                                    <div class="mb-3">
                                        <label class="form-label">設定賣出數量<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="target_quantity" min="0"
                                            value="{{ $data->target_quantity }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success waves-effect waves-light m-1">
                                        <i class="fe-check-circle me-1"></i>刪除
                                    </button>
                                    <button type="button" class="btn btn-secondary waves-effect waves-light m-1"
                                        onclick="history.go(-1)">
                                        <i class="fe-x me-1"></i>回上一頁
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let conditionSelect = document.getElementById("target_condition");
            let amountDiv = document.getElementById("target_amount_div");
            let quantityDiv = document.getElementById("target_quantity_div");

            function toggleFields() {
                let selected = conditionSelect.value;
                amountDiv.style.display = (selected === "金額" || selected === "金額+數量") ? "block" : "none";
                quantityDiv.style.display = (selected === "數量" || selected === "金額+數量") ? "block" : "none";
            }

            conditionSelect.addEventListener("change", toggleFields);
            toggleFields(); // 初始化時執行一次
        });
    </script>
@endsection

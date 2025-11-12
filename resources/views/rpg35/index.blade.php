@extends('layouts.vertical', ['page_title' => '每月美容/美化報表'])

@section('css')
    <style>
        .product-card {
            transition: all 0.3s ease;
            background-color: #ffffff;
            border: 2px solid #e9ecef;
        }

        .product-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            transform: translateY(-3px);
            border-color: #007bff;
        }

        .product-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: #212529;
        }

        .product-quantity {
            font-size: 1.5rem;
            font-weight: 700;
            color: #007bff;
        }

        .variant-section {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #dee2e6;
        }

        .variant-title {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .variant-item {
            padding: 0.5rem;
            background-color: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .category-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 1.25rem 1.5rem;
        }

        .category-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #212529;
        }

        .category-count {
            font-size: 1.25rem;
            font-weight: 600;
            color: #28a745;
        }
    </style>
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">報表管理</a></li>
                            <li class="breadcrumb-item active">每月美容/美化報表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">每月美容/美化報表</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between">
                            <div class="col-auto">
                                <form class="d-flex flex-wrap align-items-center" id="myForm"
                                    action="{{ route('rpg35') }}" method="GET">
                                    <label for="status-select" class="me-2">年度</label>
                                    <div class="me-sm-3">
                                        <select class="form-select my-1 my-lg-0" id="status-select" name="year"
                                            onchange="this.form.submit()">
                                            @foreach ($years as $year)
                                                <option value="{{ $year }}"
                                                    @if ($request->year == $year) selected @endif>{{ $year }}年
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label for="status-select" class="me-2">月份</label>
                                    <div class="me-sm-3">
                                        <select class="form-select my-1 my-lg-0" id="status-select" name="month"
                                            onchange="this.form.submit()">
                                            <option value="" selected>請選擇</option>
                                            <option value="01" @if ($request->month == '01') selected @endif>一月
                                            </option>
                                            <option value="02" @if ($request->month == '02') selected @endif>二月
                                            </option>
                                            <option value="03" @if ($request->month == '03') selected @endif>三月
                                            </option>
                                            <option value="04" @if ($request->month == '04') selected @endif>四月
                                            </option>
                                            <option value="05" @if ($request->month == '05') selected @endif>五月
                                            </option>
                                            <option value="06" @if ($request->month == '06') selected @endif>六月
                                            </option>
                                            <option value="07" @if ($request->month == '07') selected @endif>七月
                                            </option>
                                            <option value="08" @if ($request->month == '08') selected @endif>八月
                                            </option>
                                            <option value="09" @if ($request->month == '09') selected @endif>九月
                                            </option>
                                            <option value="10" @if ($request->month == '10') selected @endif>十月
                                            </option>
                                            <option value="11" @if ($request->month == '11') selected @endif>十一月
                                            </option>
                                            <option value="12" @if ($request->month == '12') selected @endif>十二月
                                            </option>
                                        </select>
                                    </div>
                                    <div class="me-3">
                                        <button type="submit" onclick="CheckSearch(event)"
                                            class="btn btn-success waves-effect waves-light me-1"><i
                                                class="fe-search me-1"></i>搜尋</button>
                                    </div>
                                </form>
                            </div>
                        </div> <!-- end row -->
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col-->
        </div>

        @if (isset($datas) && count($datas) > 0)
            <!-- 總計 -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info d-flex justify-content-between align-items-center mb-0">
                        <h3 class="mb-0">總銷售數量</h3>
                        <h2 class="mb-0"><strong>{{ number_format(array_sum(array_column($datas, 'total'))) }}</strong> 件</h2>
                    </div>
                </div>
            </div>

            <!-- 按分類顯示 -->
            @foreach ($datas as $prom_id => $data)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header category-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="category-title mb-0">{{ $data['name'] }}</h4>
                                    <span class="category-count">{{ number_format($data['total']) }} 件</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @foreach ($data['items'] as $item_total => $item_count)
                                        @if ($item_count > 0)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="product-card rounded p-3 h-100">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div class="product-name">{{ number_format($item_total) }}</div>
                                                        <div class="product-quantity">{{ number_format($item_count) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <h4 class="text-muted">請選擇年度和月份進行查詢</h4>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div> <!-- container -->

    <script>
        // 搜尋驗證
        function CheckSearch(event) {
            const year = document.querySelector('select[name="year"]').value;
            const month = document.querySelector('select[name="month"]').value;

            if (!month) {
                event.preventDefault();
                alert('請選擇月份');
                return false;
            }

            return true;
        }

        // 初始化 Bootstrap tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection

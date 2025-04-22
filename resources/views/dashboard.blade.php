@extends('layouts.vertical', ['page_title' => 'Dashboard 2'])

@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/admin-resources/admin-resources.min.css') }}" rel="stylesheet" type="text/css" />
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
                            <li class="breadcrumb-item active">當月總表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">當月總表</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-md-6 col-xl-4">
                <div class="widget-rounded-circle card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                                    <i class="fe-dollar-sign font-22 avatar-title text-primary"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-end">
                                    <h3 class="text-dark mt-1">$<span
                                            data-plugin="counterup">{{ number_format($total_today_incomes) }}</span>元</h3>
                                    <p class="text-muted mb-1 text-truncate">今日營收</p>
                                </div>
                            </div>
                        </div> <!-- end row-->
                    </div>
                </div> <!-- end widget-rounded-circle-->
            </div> <!-- end col-->

            <div class="col-md-6 col-xl-4">
                <div class="widget-rounded-circle card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                                    <i class="fe-clipboard font-22 avatar-title text-success"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-end">
                                    <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $sale_today }}</span></h3>
                                    <p class="text-muted mb-1 text-truncate">今日業務單量</p>
                                </div>
                            </div>
                        </div> <!-- end row-->
                    </div>
                </div> <!-- end widget-rounded-circle-->
            </div> <!-- end col-->

            <div class="col-md-6 col-xl-4">
                <div class="widget-rounded-circle card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-lg rounded-circle bg-soft-info border-info border">
                                    <i class="fe-edit font-22 avatar-title text-info"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-end">
                                    <h3 class="text-dark mt-1"><span
                                            data-plugin="counterup">{{ number_format($check_sale) }}</span>單</h3>
                                    <p class="text-muted mb-1 text-truncate">待對帳單量</p>
                                </div>
                            </div>
                        </div> <!-- end row-->
                    </div>
                </div> <!-- end widget-rounded-circle-->
            </div> <!-- end col-->

            <div class="col-md-6 col-xl-4">
                <div class="widget-rounded-circle card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-lg rounded-circle bg-soft-warning border-warning border">
                                    <i class="fe-eye font-22 avatar-title text-warning"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-end">
                                    <h3 class="text-dark mt-1"><span
                                            data-plugin="counterup">{{ number_format($gdpaper_month) }}</span>元</h3>
                                    <p class="text-muted mb-1 text-truncate">金紙營收</p>
                                </div>
                            </div>
                        </div> <!-- end row-->
                    </div>
                </div> <!-- end widget-rounded-circle-->
            </div> <!-- end col-->

            <div class="col-md-6 col-xl-4">
                <div class="widget-rounded-circle card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                                    <i class="fe-bar-chart-2 font-22 avatar-title text-primary"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-end">
                                    <h3 class="text-dark mt-1">$<span
                                            data-plugin="counterup">{{ number_format($price_month) }}</span>元</h3>
                                    <p class="text-muted mb-1 text-truncate">當月營收</p>
                                </div>
                            </div>
                        </div> <!-- end row-->
                    </div>
                </div> <!-- end widget-rounded-circle-->
            </div> <!-- end col-->

            <div class="col-md-6 col-xl-4">
                <div class="widget-rounded-circle card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                                    <i class="fe-trending-down font-22 avatar-title text-success"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-end">
                                    <h3 class="text-dark mt-1"><span
                                            data-plugin="counterup">{{ number_format($pay_month) }}</span>元</h3>
                                    <p class="text-muted mb-1 text-truncate">當月支出</p>
                                </div>
                            </div>
                        </div> <!-- end row-->
                    </div>
                </div> <!-- end widget-rounded-circle-->
            </div> <!-- end col-->
            <div class="col-md-6 col-xl-4">
                <div class="widget-rounded-circle card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-lg rounded-circle bg-soft-info border-info border">
                                    <i class="fe-bar-chart-line- font-22 avatar-title text-info"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-end">
                                    <h3 class="text-dark mt-1" @if ($net_income < 0) style="color: red;" @endif>
                                        <span data-plugin="counterup">{{ number_format($net_income) }}</span>元
                                    </h3>
                                    <p class="text-muted mb-1 text-truncate">營業淨利</p>
                                </div>
                            </div>
                        </div> <!-- end row-->
                    </div>
                </div> <!-- end widget-rounded-circle-->
            </div> <!-- end col-->

            <div class="col-md-6 col-xl-4">
                <div class="widget-rounded-circle card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-lg rounded-circle bg-soft-warning border-warning border">
                                    <i class="fe-users font-22 avatar-title text-warning"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-end">
                                    <h3 class="text-dark mt-1"><span
                                            data-plugin="counterup">{{ number_format($cust_nums) }}</span>人</h3>
                                    <p class="text-muted mb-1 text-truncate">累積客戶數量</p>
                                </div>
                            </div>
                        </div> <!-- end row-->
                    </div>
                </div> <!-- end widget-rounded-circle-->
            </div> <!-- end col-->
        </div>

        <!-- start page title -->
        {{-- <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">達標進度</h4>
                    <div class="col-md-6 col-xl-4">
                        <div class="widget-rounded-circle card">
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($targetDatas as $targetData)
                                        <div class="col-6">
                                            <div class="avatar-sm bg-blue rounded">
                                                <i class="fe-aperture avatar-title font-22 text-white"></i>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <h3 class="text-dark my-1">$<span data-plugin="counterup">{{ number_format($targetData->manual_achieved) }}</span>
                                                </h3>
                                                <p class="text-muted mb-1 text-truncate">{{ $targetData->category_name->name }}（{{ $targetData->frequency }}）</p>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <h6 class="text-uppercase">達標目標: {{ number_format($targetData->target_amount) }}<span class="float-end">達標進度{{ $targetData->percent }}%</span></h6>
                                            <div class="progress progress-sm m-0">
                                                <div class="progress-bar bg-blue" role="progressbar" aria-valuenow="{{ $targetData->percent }}"
                                                    aria-valuemin="0" aria-valuemax="100" style="width: {{ $targetData->percent }}%">
                                                    <span class="visually-hidden">{{ $targetData->percent }}"% Complete</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div> <!-- end widget-rounded-circle-->
                        </div> <!-- end col-->
                    </div>
                </div>
            </div>

        </div> <!-- container --> --}}
    @endsection

    @section('script')
        <!-- third party js -->
        <script src="{{ asset('assets/libs/jquery-sparkline/jquery-sparkline.min.js') }}"></script>
        <script src="{{ asset('assets/libs/admin-resources/admin-resources.min.js') }}"></script>
        <!-- third party js ends -->

        <!-- demo app -->
        <script src="{{ asset('assets/js/pages/dashboard-2.init.js') }}"></script>
        <!-- end demo js-->
    @endsection

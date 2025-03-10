@extends('layouts.vertical', ['page_title' => 'Dashboard', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/selectize/selectize.min.css') }}" rel="stylesheet" type="text/css" />
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
                    </div>
                    <h4 class="page-title">線上打卡</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="widget-rounded-circle card">
                    <div class="card-body">
                        <div class="row">
                            <form action="{{ route('index.worktime') }}" method="POST">
                                <div class="alert alert-primary" role="alert">
                                    目前時間為 <b>{{ $now }}</b>
                                </div>
                                @csrf
                                @if (!isset($work->worktime))
                                    <button type="Submit" class="btn btn-primary" name="work_time"
                                        value="0">上班</button>
                                    <button type="button" class="btn btn-success" name="overtime" value="1"
                                        id="overtime">補簽</button>
                                    <div id="overtimecontent">
                                        <br>
                                        <div class="mb-3">
                                            <label for="exampleFormControlTextarea1" class="form-label">上班時間</label>
                                            <input type="datetime-local" class="form-control" id="name" name="worktime"
                                                value="">
                                        </div>
                                        <div class="mb-3">
                                            <label for="exampleFormControlTextarea1" class="form-label">下班時間</label>
                                            <input type="datetime-local" class="form-control" id="name" name="dutytime"
                                                value="">
                                        </div>
                                        <div class="mb-3">
                                            <label for="exampleFormControlTextarea1" class="form-label">補簽原因</label>
                                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="remark"></textarea><br>
                                            <button type="Submit" class="btn btn-danger" name="overtime"
                                                value="1">送出</button>
                                        </div>
                                    </div>
                                @elseif($work->dutytime != null)
                                    <button type="Submit" class="btn btn-primary" name="work_time"
                                        value="0">上班</button>
                                    <button type="button" class="btn btn-success" value="1" id="overtime">補簽</button>
                                    <div id="overtimecontent">
                                        <br>
                                        <div class="mb-3">
                                            <label for="exampleFormControlTextarea1" class="form-label">上班時間</label>
                                            <input type="datetime-local" class="form-control" id="name" name="worktime"
                                                value="">
                                        </div>
                                        <div class="mb-3">
                                            <label for="exampleFormControlTextarea1" class="form-label">下班時間</label>
                                            <input type="datetime-local" class="form-control" id="name" name="dutytime"
                                                value="">
                                        </div>
                                        <div class="mb-3">
                                            <label for="exampleFormControlTextarea1" class="form-label">補簽原因</label>
                                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="remark"></textarea><br>
                                            <button type="Submit" class="btn btn-danger" name="overtime"
                                                value="1">送出</button>
                                        </div>
                                    </div>
                                @elseif($work->dutytime == null)
                                    <button type="Submit" class="btn btn-danger" name="dutytime" value="2">下班</button>
                                @endif
                        </div>
                        </form>
                    </div> <!-- end row-->
                </div>
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->
    </div> <!-- container -->
    <div class="row p-2">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">待提醒合約清單（兩個月內）</h4>

                    <div class="table-responsive">
                        <table class="table table-borderless table-hover table-nowrap table-centered m-0">

                            <thead class="table-light">
                                <tr>
                                    <th>編號</th>
                                    <th>合約類別</th>
                                    <th>顧客名稱</th>
                                    <th>電話</th>
                                    <th>寶貝名稱</th>
                                    <th>目前簽約年份</th>
                                    <th>開始日期</th>
                                    <th>結束日期</th>
                                    <th>金額</th>
                                    <th>續約</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contract_datas as $key => $contract_data)
                                    <tr>
                                        <td>{{ $contract_data->number }}</td>
                                        <td>
                                            <span
                                                @if ($contract_data->type == '1') class=" bg-soft-success text-success p-1" 
                                        @elseif($contract_data->type == '2') class=" bg-soft-danger text-danger p-1"
                                        @elseif($contract_data->type == '4') class=" bg-soft-warning text-warning p-1"
                                        @else class=" bg-soft-blue text-blue p-1" @endif>
                                                @if (isset($contract_data->type_data))
                                                    {{ $contract_data->type_data->name }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>{{ $contract_data->cust_name->name }}</td>
                                        <td>{{ $contract_data->mobile }}</td>
                                        <td>{{ $contract_data->pet_name }}</td>
                                        <td>第{{ $contract_data->year }}年</td>
                                        <td>{{ $contract_data->getRocStartDateAttribute() }}</td>
                                        <td>{{ $contract_data->getRocEndDateAttribute() }}</td>
                                        <td>{{ number_format($contract_data->price) }}</td>
                                        <td>
                                            @if ($contract_data->renew == '1')
                                                是（{{ $contract_data->renew_year }}年）
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

    <div class="row p-2">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">待提醒平安燈清單（兩個月內）</h4>

                    <div class="table-responsive">
                        <table class="table table-borderless table-hover table-nowrap table-centered m-0">

                            <thead class="table-light">
                                <tr>
                                    <th>編號</th>
                                    <th>合約類別</th>
                                    <th>顧客名稱</th>
                                    <th>電話</th>
                                    <th>寶貝名稱</th>
                                    <th>目前簽約年份</th>
                                    <th>開始日期</th>
                                    <th>結束日期</th>
                                    <th>金額</th>
                                    <th>續約</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lamp_datas as $key => $lamp_data)
                                    <tr>
                                        <td>{{ $lamp_data->number }}</td>
                                        <td>
                                            <span
                                                @if ($lamp_data->type == '1') class=" bg-soft-success text-success p-1" 
                                        @elseif($lamp_data->type == '2') class=" bg-soft-danger text-danger p-1"
                                        @elseif($lamp_data->type == '4') class=" bg-soft-warning text-warning p-1"
                                        @else class=" bg-soft-blue text-blue p-1" @endif>
                                                @if (isset($lamp_data->type_data))
                                                    {{ $lamp_data->type_data->name }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>{{ $lamp_data->cust_name->name }}</td>
                                        <td>{{ $lamp_data->mobile }}</td>
                                        <td>{{ $lamp_data->pet_name }}</td>
                                        <td>第{{ $lamp_data->year }}年</td>
                                        <td>{{ $lamp_data->getRocStartDateAttribute() }}</td>
                                        <td>{{ $lamp_data->getRocEndDateAttribute() }}</td>
                                        <td>{{ number_format($lamp_data->price) }}</td>
                                        <td>
                                            @if ($lamp_data->renew == '1')
                                                是（{{ $lamp_data->renew_year }}年）
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection

@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/js/overtime.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
    <!-- third party js ends -->

    <!-- demo app -->
    <script src="{{ asset('assets/js/pages/dashboard-1.init.js') }}"></script>
    <!-- end demo js-->
@endsection

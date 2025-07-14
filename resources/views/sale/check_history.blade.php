@extends('layouts.vertical', ['page_title' => '查看對帳明細'])

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
                            <li class="breadcrumb-item active">查看對帳明細</li>
                        </ol>
                    </div>
                    <h4 class="page-title">查看對帳明細</h4>
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
                                    action="{{ route('sales.checkHistory') }}" method="GET">
                                    <label for="status-select" class="me-2">日期區間</label>
                                    <div class="me-2">
                                        <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="after_date"
                                            @if (!isset($request->after_date)) value="{{ $firstDay->format('Y-m-d') }}" @endif
                                            value="{{ $request->after_date }}">
                                    </div>
                                    <label for="status-select" class="me-2">至</label>
                                    <div class="me-3">
                                        <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="before_date"
                                            @if (!isset($request->before_date)) value="{{ $lastDay->format('Y-m-d') }}" @endif
                                            value="{{ $request->before_date }}">
                                    </div>
                                    <label for="status-select" class="me-2">對帳人員</label>
                                    <div class="me-4">
                                        <select id="inputState" class="form-select" name="check_user_id"
                                            onchange="this.form.submit()">
                                            <option value="null" @if (isset($request->check_user_id) || $request->check_user_id == '') selected @endif>請選擇
                                            </option>
                                            @foreach ($check_users as $check_user)
                                                <option value="{{ $check_user->id }}"
                                                    @if ($request->check_user_id == $check_user->id) selected @endif>
                                                    {{ $check_user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="me-3">
                                        <button type="submit" onclick="CheckSearch(event)"
                                            class="btn btn-success waves-effect waves-light me-1"><i
                                                class="fe-search me-1"></i>搜尋</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-auto">
                                <div class="text-lg-end my-1 my-lg-0">
                                    <h3><span
                                            class="text-danger">總共：{{ number_format($sums['count']) }}單，總計：{{ number_format($sums['price']) }}元</span>
                                    </h3>
                                </div>
                            </div><!-- end col-->
                        </div> <!-- end row -->
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col-->
        </div>

        <div class="row">
            <div class="col-12">
                @foreach ($datas as $date => $data)
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"> {{$data['name']}}</h5>
                            <div class="table-responsive ">
                                <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                    <thead class="table-light">
                                        <tr align="center">
                                            <th>No</th>
                                            <th>日期</th>
                                            <th>單號</th>
                                            <th width="15%">客戶名稱</th>
                                            <th>寶貝名</th>
                                            <th>方案</th>
                                            <th>業務價格</th>
                                            <th>對帳人員</th>
                                            <th>業務詳情</th>
                                        </tr>
                                    </thead>
                                    @foreach ($data['items'] as $key => $item)
                                        <tbody>
                                            <tr>
                                                <td align="center">{{ $key + 1 }}</td>
                                                <td align="center">{{ $item->sale_date }}</td>
                                                <td align="center">{{ $item->sale_on }}</td>
                                                <td align="center">
                                                    @if (isset($item->cust_name))
                                                        {{ $item->cust_name->name }}
                                                    @endif
                                                </td>
                                                <td align="center">{{ $item->pet_name }}</td>
                                                <td align="center">
                                                    @if (isset($item->plan_name))
                                                        {{ $item->plan_name->name }}
                                                    @elseif($item->pay_id == 'D')
                                                        尾款
                                                    @elseif($item->pay_id == 'E')
                                                        追加
                                                    @endif
                                                </td>
                                                <td align="center">{{ number_format($item->pay_price) }}</td>
                                                <td align="center">{{ $item->check_user_name->name }}</td>
                                                <td align="center"><a href="{{ route('sale.check', $item->id) }}"><i
                                                            class="mdi mdi-eye me-2 text-muted font-18 vertical-middle"></i></a>
                                                </td>
                                            </tr>
                                    @endforeach
                                    </tbody>
                                    <tr class="mb-3">
                                        <td colspan="5"></td>
                                        <td align="center"><b>共計：{{ number_format($data['count']) }}單</b></td>
                                        <td align="center"><b>現金：{{ number_format($data['cash_total']) }}元</b></td>
                                        <td align="center"><b>匯款：{{ number_format($data['transfer_total']) }}元</b></td>
                                        <td align="center"><b>小計：{{ number_format($data['price']) }}元</b></td>
                                        <td></td>
                                    </tr>
                                </table><br>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div> <!-- container -->
@endsection

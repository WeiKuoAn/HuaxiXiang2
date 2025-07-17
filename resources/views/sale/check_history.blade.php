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
                                            class="text-danger">業務共{{ number_format($sums['count']) }}單，支出共{{ number_format($sums['pay_count']) }}單，總計：{{ number_format($sums['actual_price']) }}元</span>
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
                            <h5 class="card-title"> {{ $data['name'] }}</h5>
                            @if (isset($data['items']))
                                <div class="table-responsive ">
                                    <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                        <thead class="table-light">
                                            <tr align="center">
                                                <th width="5%">No</th>
                                                <th width="10%">日期</th>
                                                <th width="10%">單號</th>
                                                <th width="15%">客戶名稱</th>
                                                <th width="10%">寶貝名</th>
                                                <th width="10%">方案</th>
                                                <th width="10%">業務價格</th>
                                                <th width="10%">對帳人員</th>
                                                <th width="10%">業務詳情</th>
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
                                        </tr>
                                    </table><br>
                                </div>
                            @endif

                            @if (isset($data['pay_datas']))
                                <div class="table-responsive ">
                                    <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                        <thead class="table-light">
                                            <tr align="center">
                                                <th width="5%">No</th>
                                                <th width="10%">日期</th>
                                                <th width="10%">單號</th>
                                                <th width="15%">支出日期</th>
                                                <th width="10%">支出科目</th>
                                                <th width="10%">發票號碼</th>
                                                <th width="10%">支出總價格</th>
                                                <th width="10%">備註</th>
                                                <th width="10%">支出詳情</th>
                                            </tr>
                                        </thead>
                                        @foreach ($data['pay_datas'] as $pay_key => $pay_data)
                                            <tbody>
                                                <tr>
                                                    <td align="center">{{ $pay_key + 1 }}</td>
                                                    <td align="center">{{ $pay_data->pay_date }}</td>
                                                    <td align="center">{{ $pay_data->pay_on }}</td>
                                                    <td align="center">
                                                        @foreach ($pay_data->pay_items as $pay_item)
                                                            {{ $pay_item->pay_date }}<br>
                                                        @endforeach
                                                    </td>
                                                    <td align="center">
                                                        @foreach ($pay_data->pay_items as $pay_item)
                                                            {{ $pay_item->pay_name->name }}<br>
                                                        @endforeach
                                                    </td>
                                                    <td align="center">
                                                        @foreach ($pay_data->pay_items as $pay_item)
                                                            <b>{{ $pay_item->invoice_number }}</b> -
                                                            ${{ number_format($pay_item->price) }}<br>
                                                        @endforeach
                                                    </td>
                                                    <td align="center">
                                                        {{ number_format($pay_data->price) }}
                                                    </td>
                                                    <td align="center">
                                                        {{ $pay_data->comment }}
                                                    </td>
                                                    <td align="center">
                                                        <a href="{{ route('pay.check', $pay_data->id) }}">
                                                            <i
                                                                class="mdi mdi-eye me-2 text-muted font-18 vertical-middle"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                        @endforeach
                                        </tbody>
                                        <tr class="mb-3">
                                            <td colspan="5"></td>
                                            <td align="center"><b>共計：{{ number_format($data['pay_count']) }}單</b></td>
                                            <td align="center"></td>
                                            <td align="center"></td>
                                            <td align="center"><b>小計：{{ number_format($data['pay_price']) }}元</b></td>
                                        </tr>
                                    </table><br>
                                </div>
                            @endif
                            <div class="row">
                                <div class="card mb-0">
                                    <div class="card-body">
                                        <div class="col-12 text-end">
                                            <h4 class="card-title text-danger">實收：{{ number_format($data['actual_price'] ?? 0) }}元</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div> <!-- container -->
@endsection

@section('script')
    <script>
        function CheckSearch(event) {
            // 檢查日期是否有效
            var afterDate = document.querySelector('input[name="after_date"]').value;
            var beforeDate = document.querySelector('input[name="before_date"]').value;

            if (afterDate && beforeDate && afterDate > beforeDate) {
                event.preventDefault();
                alert('開始日期不能大於結束日期');
                return false;
            }

            return true;
        }
    </script>
@endsection

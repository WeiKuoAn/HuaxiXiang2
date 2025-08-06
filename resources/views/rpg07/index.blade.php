@extends('layouts.vertical', ['page_title' => '團火查詢'])

@section('content')
    <style>
.table-nowrap th, .table-nowrap td {
    white-space: nowrap;
}

.table-responsive td.wrap-text {
    white-space: normal;
    min-width: 200px;
}
    </style>
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
                            <li class="breadcrumb-item active">團火查詢</li>
                        </ol>
                    </div>
                    <h4 class="page-title">團火查詢</h4>
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
                                    action="{{ route('rpg07') }}" method="GET">
                                    <label for="status-select" class="me-2">日期區間</label>
                                    <div class="me-2">
                                        <input type="date" class="form-control my-1 my-lg-0" id="after_date"
                                            name="after_date" value="{{ $request->after_date }}">
                                    </div>
                                    <label for="status-select" class="me-2">至</label>
                                    <div class="me-3">
                                        <input type="date" class="form-control my-1 my-lg-0" id="before_date"
                                            name="before_date" value="{{ $request->before_date }}">
                                    </div>
                                    <div class="me-3">
                                        <button type="submit" onclick="CheckSearch(event)"
                                            class="btn btn-success waves-effect waves-light me-1"><i
                                                class="fe-search me-1"></i>搜尋</button>
                                    </div>
                                    <div class="me-3">
                                        <a href="{{ route('rpg07.export', request()->input()) }}" onclick="CheckForm(event)"
                                            class="btn btn-danger waves-effect waves-light">匯出</a>
                                    </div>
                                </form>
                            </div>
                            @if (Auth::user()->level != 2)
                                <div class="col-auto">
                                    <div class="text-lg-end my-1 my-lg-0">
                                        <h3><span class="text-danger">共{{ number_format($kgs,2) }}公斤，共計{{ number_format($total_price) }}元，功德{{ $total_merits }}筆</span></h3>
                                    </div>
                                </div><!-- end col-->
                            @else
                                <div class="col-auto">
                                    <div class="text-lg-end my-1 my-lg-0">
                                        <h3><span class="text-danger">共{{ number_format($kgs,2) }}公斤，功德{{ $total_merits }}筆</span></h3>
                                    </div>
                                </div><!-- end col-->
                            @endif
                        </div> <!-- end row -->
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col-->
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead class="table-light">
                                    <tr align="center">
                                        <th width="5%">日期</th>
                                        <th>客戶</th>
                                        <th>寶貝名</th>
                                        <th>公斤數</th>
                                        <th>品種</th>
                                        <th>火化費</th>
                                        <th>類別</th>
                                        <th>方案</th>
                                        <th>金紙</th>
                                        <th>後續處理A</th>
                                        <th>後續處理B</th>
                                        <th>付款方式</th>
                                        @if (Auth::user()->level == 0)
                                            <th>實收價格</th>
                                        @endif
                                        <th class="wrap-text" >備註</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $item)
                                        @if($item['type'] === 'sale')
                                            <tr>
                                                <td align="center">{{ $item['data']->sale_date }}</td>
                                                <td align="center">
                                                    @if (isset($item['data']->customer_id))
                                                        @if (isset($item['data']->cust_name))
                                                            {{ $item['data']->cust_name->name }}
                                                        @else
                                                            {{ $item['data']->customer_id }}
                                                        @endif
                                                    @endif
                                                </td>
                                                <td align="center">
                                                    @if (isset($item['data']->pet_name))
                                                        {{ $item['data']->pet_name }}
                                                    @endif
                                                </td>
                                                <td align="center">
                                                    {{ $item['data']->kg }}
                                                </td>
                                                <td align="center">
                                                    {{ $item['data']->variety }}
                                                </td>
                                                <td align="center">
                                                    @if ($item['data']->pay_id == 'E')
                                                        {{ number_format($item['data']->pay_price) }}
                                                    @else
                                                        {{ number_format($item['data']->plan_price) }}
                                                    @endif
                                                </td>
                                                <td align="center">
                                                    @if (isset($item['data']->type))
                                                        {{ $item['data']->source_type->name }}
                                                    @endif
                                                </td>
                                                <td align="center">
                                                    @if (isset($item['data']->plan_id))
                                                        @if (isset($item['data']->plan_name))
                                                            {{ $item['data']->plan_name->name }}
                                                        @else
                                                            {{ $item['data']->plan_id }}
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @foreach ($item['data']->gdpapers as $gdpaper)
                                                        @if (isset($gdpaper->gdpaper_id))
                                                            @if ($item['data']->plan_id != '4')
                                                                {{ $gdpaper->gdpaper_name->name }}-{{ $gdpaper->gdpaper_num }}份<br>
                                                            @else
                                                                {{ $gdpaper->gdpaper_name->name }}{{ number_format($gdpaper->gdpaper_num) }}份<br>
                                                            @endif
                                                        @else
                                                            無
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @if (isset($item['data']->before_prom_id))
                                                        @if (isset($item['data']->PromA_name))
                                                            {{ $item['data']->PromA_name->name }}-{{ number_format($item['data']->before_prom_price) }}
                                                        @else
                                                            {{ $item['data']->before_prom_id }}
                                                        @endif
                                                    @endif
                                                    @foreach ($item['data']->proms as $prom)
                                                        @if ($prom->prom_type == 'A')
                                                            @if (isset($prom->prom_id))
                                                                {{ $prom->prom_name->name }}-{{ number_format($prom->prom_total) }}<br>
                                                            @else
                                                                無
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td align="center">
                                                    @foreach ($item['data']->proms as $prom)
                                                        @if ($prom->prom_type == 'B')
                                                            @if (isset($prom->prom_id))
                                                                {{ $prom->prom_name->name }}<br>
                                                            @else
                                                                無
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td align="center">
                                                    @if (isset($item['data']->pay_id))
                                                        {{ $item['data']->pay_type() }}
                                                    @endif
                                                </td>
                                                @if (Auth::user()->level == 0)
                                                    <td align="center">{{ number_format($item['data']->pay_price) }}</td>
                                                @endif
                                                <td class="wrap-text">
                                                    {{ $item['data']->comm }}
                                                </td>
                                            </tr>
                                        @elseif($item['type'] === 'merit_only')
                                            @php
                                                $merit = $item['merits']->first();
                                                $user_name = $merit && $merit->user_data ? $merit->user_data->name : '功德記錄';
                                                // Debug: 輸出功德記錄資訊
                                                \Log::info('功德記錄顯示:', [
                                                    'date' => $item['date'],
                                                    'merit_exists' => $merit ? 'yes' : 'no',
                                                    'user_data_exists' => $merit && $merit->user_data ? 'yes' : 'no',
                                                    'user_name' => $user_name,
                                                    'kg' => $merit ? $merit->kg : 'N/A',
                                                    'variety' => $merit ? $merit->variety : 'N/A'
                                                ]);
                                            @endphp
                                            <tr class="table-secondary">
                                                <td align="center">{{ $item['date'] }}</td>
                                                <td align="center">{{ $user_name }}</td>
                                                <td align="center">-</td>
                                                <td align="center">{{ $merit ? ($merit->kg ?? 0) : 0 }}</td>
                                                <td align="center">{{ $merit ? ($merit->variety ?? '功德') : '功德' }}</td>
                                                <td align="center">-</td>
                                                <td align="center">功德</td>
                                                <td align="center">-</td>
                                                <td align="center">-</td>
                                                <td align="center">-</td>
                                                <td align="center">-</td>
                                                <td align="center">-</td>
                                                @if (Auth::user()->level == 0)
                                                    <td align="center">-</td>
                                                @endif
                                                <td class="wrap-text">
                                                    <span class="text-muted">功德件</span>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- container -->
@endsection

{{-- <script>
    function CheckSearch(event) {
    //   event.preventDefault(); // 防止超連結的默認行為
    
      // 檢查欄位是否填寫
      var before_date = $("#before_date").val();
      var after_date = $("#after_date").val();
    
      if (after_date === "" || before_date === "") {
        alert("請填寫日期區間");
      } else {
        $("#myForm").submit();
      }
    }
</script> --}}

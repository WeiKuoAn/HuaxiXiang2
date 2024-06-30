@extends('layouts.vertical', ["page_title"=> "團火查詢"])

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
                            <form class="d-flex flex-wrap align-items-center" id="myForm" action="{{ route('rpg07') }}" method="GET">
                                <label for="status-select" class="me-2">日期區間</label>
                                <div class="me-2">
                                    <input type="date" class="form-control my-1 my-lg-0" id="after_date" name="after_date" value="{{ $request->after_date }}">
                                </div>
                                <label for="status-select" class="me-2">至</label>
                                <div class="me-3">
                                    <input type="date" class="form-control my-1 my-lg-0" id="before_date" name="before_date" value="{{ $request->before_date }}">
                                </div>
                                <div class="me-3">
                                    <button type="submit" onclick="CheckSearch(event)" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                </div>
                                <div class="me-3">
                                    <a href="{{ route('rpg07.export',request()->input()) }}" onclick="CheckForm(event)" class="btn btn-danger waves-effect waves-light">匯出</a>
                                </div>
                            </form>
                        </div>
                        @if(Auth::user()->level != 2)
                            <div class="col-auto">
                                <div class="text-lg-end my-1 my-lg-0">
                                    <h3><span class="text-danger">共計{{ number_format($total_price) }}元</span></h3>
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
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                            <thead class="table-light">
                                <tr align="center">
                                    <th >日期</th>
                                    <th >客戶</th>
                                    <th >寶貝名</th>
                                    <th >公斤數</th>
                                    <th >火化費</th>
                                    <th >類別</th>
                                    <th >方案</th>
                                    <th >金紙</th>
                                    <th >後續處理A</th>
                                    <th >後續處理B</th>
                                    <th >付款方式</th>
                                    @if(Auth::user()->level == 0)
                                        <th >實收價格</th>
                                    @endif
                                    <th >備註</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datas as $data)
                                    <tr>
                                        {{-- <td>{{ $data->sale_on }}</td>
                                        <td>{{ $data->user_name->name }}</td> --}}
                                        <td align="center">{{ $data->sale_date }}</td>
                                        <td align="center">
                                            @if (isset($data->customer_id))
                                                @if(isset($data->cust_name))
                                                    {{ $data->cust_name->name }}
                                                @else
                                                {{ $data->customer_id }}
                                                @endif
                                            @endif
                                        </td>
                                        <td align="center">
                                            @if (isset($data->pet_name))
                                                {{ $data->pet_name }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $data->kg }}
                                        </td>
                                        <td align="center">
                                            {{ number_format($data->plan_price) }}
                                        </td>
                                        <td align="center">
                                            @if (isset($data->type))
                                                {{ $data->source_type->name }}
                                            @endif
                                        </td>
                                        <td align="center">
                                            @if (isset($data->plan_id))
                                                @if(isset($data->plan_name))
                                                {{ $data->plan_name->name }}
                                                @else
                                                {{ $data->plan_id }}
                                                @endif
                                            @endif
                                            {{-- {{ $data->plan_id }} --}}
                                        </td>
                                        <td>
                                            @foreach ($data->gdpapers as $gdpaper)
                                                @if (isset($gdpaper->gdpaper_id))
                                                    @if ($data->plan_id != '4')
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
                                            @if(isset($data->before_prom_id))
                                                @if(isset($data->PromA_name))
                                                {{ $data->PromA_name->name }}-{{ number_format($data->before_prom_price) }}
                                                @else
                                                {{$data->before_prom_id}}
                                                @endif
                                            @endif
                                            @foreach ($data->proms as $prom)
                                                @if ($prom->prom_type == 'A')
                                                    @if(isset($prom->prom_id))
                                                        {{ $prom->prom_name->name }}-{{ number_format($prom->prom_total) }}<br>
                                                    @else
                                                        無
                                                    @endif
                                                @endif
                                            @endforeach     
                                        </td>
                                            <td align="center">
                                                @foreach ($data->proms as $prom)
                                                    @if ($prom->prom_type == 'B')
                                                        @if(isset($prom->prom_id))
                                                            {{ $prom->prom_name->name }}<br>
                                                        @else
                                                            無
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </td>
                                        <td align="center">
                                            @if (isset($data->pay_id))
                                                {{ $data->pay_type() }}
                                            @endif
                                        </td>
                                        @if(Auth::user()->level == 0)
                                        <td align="center">{{ number_format($data->pay_price) }}</td>
                                        @endif
                                        <td>
                                            {{ $data->comm }}
                                        </td>
                                    </tr>
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
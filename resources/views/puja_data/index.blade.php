@extends('layouts.vertical', ["page_title"=> "法會報名列表"])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">法會報名管理</a></li>
                        <li class="breadcrumb-item active">法會報名列表</li>
                    </ol>
                </div>
                <h4 class="page-title">法會報名列表</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('puja_datas') }}" method="GET">
                                <div class="me-3">
                                    <label for="after_date" class="form-label">報名日期</label>
                                    <input type="date" class="form-control" id="after_date" name="after_date" value="{{ $request->after_date }}">
                                </div>
                                <div class="me-3">
                                    <label for="before_date" class="form-label">&nbsp;</label>
                                    <input type="date" class="form-control" id="before_date" name="before_date" value="{{ $request->before_date }}">
                                </div>
                                <div class="me-3">
                                    <label for="before_date" class="form-label">顧客姓名</label>
                                    <input type="search" class="form-control my-1 my-lg-0" id="inputPassword2" name="cust_name">
                                </div>
                                <div class="me-3">
                                    <label for="before_date" class="form-label">寶貝名</label>
                                    <input type="search" class="form-control my-1 my-lg-0" id="pet_name" name="pet_name">
                                </div>
                                <div class="me-sm-3">
                                    <label class="form-label">年份</label>
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="year" onchange="this.form.submit()">
                                        <option value="null" selected>請選擇...</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" @if($request->year == $year) selected @endif>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="me-sm-3">
                                    <label class="form-label">法會名稱</label>
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="puja_id" onchange="this.form.submit()">
                                        <option value="null" selected>請選擇...</option>
                                        @foreach($pujas as $puja)
                                            <option value="{{ $puja->id }}" @if($puja->id == $request->puja_id) selected @endif>{{ $puja->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="me-3 mt-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                </div>
                                <div class="me-3 mt-3">
                                    <a href="{{ route('puja_datas.export',request()->input()) }}">
                                         <button type="button" class="btn btn-primary waves-effect waves-light me-1"><i class="fe-download me-1"></i>匯出</button>
                                     </a>
                                </div>
                            </form>
                        </div>
                        <div class="col-auto"  style="margin-top: 25px;">
                            <div class="text-lg-end my-1 my-lg-0">
                                {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                                <a href="{{ route('puja_data.create') }}">
                                    <button type="button" class="btn btn-danger waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#custom-modal"><i class="mdi mdi-plus-circle me-1"></i>法會報名</button>
                                </a>
                            </div>
                        </div><!-- end col-->
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
                                    <tr>
                                        <tr>
                                            <th>編號</th>
                                            <th>報名類別</th>
                                            <th>報名日期</th>
                                            <th>法會名稱</th>
                                            <th>顧客名稱</th>
                                            <th>寶貝名稱</th>
                                            <th>附加商品</th>
                                            <th>付款方式</th>
                                            <th>支付金額</th>
                                            <th>備註</th>
                                            <th>動作</th>
                                        </tr>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $key=>$data)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $data->type() }}</td>
                                            <td>{{ $data->date }}</td>
                                            <td>{{ $data->puja_name->name }}</td>
                                            <td>
                                                @if (isset($data->customer_id))
                                                    @if(isset($data->cust_name))
                                                        {{ $data->cust_name->name }}
                                                    @else
                                                        {{ $data->customer_id }}<b style="color: red;">（客戶姓名須重新登入）</b>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                {{ $data->pet_name }}<br>
                                            </td>
                                            <td>
                                                @if(isset($data->products))
                                                    @foreach($data->products as $data->product)
                                                        {{ $product_name[$data->product->product_id] }}-{{$data->product->product_num  }}份<br>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($data->pay_id))
                                                    {{$data->pay_type()}}
                                                @endif
                                            </td>
                                            <td>{{ number_format($data->pay_price) }}</td>
                                            <td>{{ $data->comment }}</td>
                                            <td>
                                                <div class="btn-group dropdown">
                                                    <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect" data-bs-toggle="dropdown" aria-expanded="false">動作 <i class="mdi mdi-arrow-down-drop-circle"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="{{ route('puja_data.edit',$data->id) }}"><i class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯</a>
                                                        @if (Auth::user()->level != 2)
                                                        <a class="dropdown-item" href="{{ route('puja_data.del',$data->id) }}"><i class="mdi mdi-delete me-2 font-18 text-muted vertical-middle"></i>刪除</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <br>
                            <ul class="pagination pagination-rounded justify-content-end mb-0">
                                {{ $datas->appends($condition)->links('vendor.pagination.bootstrap-4') }}
                            </ul>
                        </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>

                    

</div> <!-- container -->
@endsection
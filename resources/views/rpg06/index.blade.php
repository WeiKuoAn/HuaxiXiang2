@extends('layouts.vertical', ["page_title"=> "套組法會查詢"])

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
                        <li class="breadcrumb-item active">套組法會查詢</li>
                    </ol>
                </div>
                <h4 class="page-title">套組法會查詢</h4>
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
                            <form class="d-flex flex-wrap align-items-center" id="myForm" action="{{ route('rpg06') }}" method="GET">
                                <label for="status-select" class="me-2">日期區間</label>
                                <div class="me-2">
                                    <input type="date" class="form-control my-1 my-lg-0" id="after_date" name="after_date" value="{{ $request->after_date }}">
                                </div>
                                <label for="status-select" class="me-2">至</label>
                                <div class="me-3">
                                    <input type="date" class="form-control my-1 my-lg-0" id="before_date" name="before_date" value="{{ $request->before_date }}">
                                </div>
                                <div class="me-3">
                                    <button type="submit"class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                </div>
                                <div class="me-3">
                                    <a href="{{ route('rpg06.export',request()->input()) }}">
                                         <button type="button" class="btn btn-primary waves-effect waves-light me-1"><i class="fe-download me-1"></i>匯出</button>
                                     </a>
                                </div>
                            </form>
                        </div>

                        {{-- <div class="col-auto">
                            <div class="text-lg-end my-1 my-lg-0">
                                <a href="{{ route('rpg07.export',request()->input()) }}" class="btn btn-danger waves-effect waves-light">匯出</a>
                            </div>
                        </div><!-- end col--> --}}
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
                                    <th scope="col">報名日期</th>
                                    <th scope="col">客戶姓名</th>
                                    <th scope="col">寶貝名稱</th>
                                    <th scope="col">客戶電話</th>
                                    <th scope="col">法會費用</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $key=>$data)
                                    <tr align="center">
                                        <td>{{ date('Y-m-d',strtotime($data->created_at)) }}</td>
                                        <td>{{ $data->sale_data->cust_name->name }}</td>
                                        <td>{{ $data->sale_data->pet_name }}</td>
                                        <td>{{ $data->sale_data->cust_name->mobile }}</td>
                                        <td>{{  number_format($data->prom_total) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table><br>
                        <ul class="pagination pagination-rounded justify-content-end mb-0">
                            {{ $datas->appends($condition)->links('vendor.pagination.bootstrap-4') }}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- container -->
@endsection

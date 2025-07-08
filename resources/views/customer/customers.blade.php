@extends('layouts.vertical', ["page_title"=> "CRM Customers"])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">客戶管理</a></li>
                        <li class="breadcrumb-item active">客戶資料</li>
                    </ol>
                </div>
                <h4 class="page-title">客戶資料</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('customer') }}" method="GET">
                                <div class="me-3">
                                    <input type="search" class="form-control my-1 my-lg-0" id="inputPassword2" name="name" placeholder="姓名" value="{{ $request->name }}">
                                </div>
                                <div class="me-3">
                                    <input type="search" class="form-control my-1 my-lg-0" id="inputPassword2" name="mobile" placeholder="電話" value="{{ $request->mobile }}">
                                </div>
                                <div class="me-3">
                                    <input type="search" class="form-control my-1 my-lg-0" id="inputPassword2" name="pet_name" placeholder="寶貝名" value="{{ $request->pet_name }}">
                                </div>
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="county" onchange="this.form.submit()">
                                        <option value="null" selected>選擇地區</option>
                                        @foreach($countys as $county)
                                            <option value="{{ $county }}" @if($county == $request->county) selected @endif >{{ $county }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="district" name="district" onchange="this.form.submit()">
                                        <option value="null" selected>選擇地區</option>
                                        @foreach($districts as $district)
                                            <option value="{{ $district }}" @if($district == $request->district) selected @endif >{{ $district }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="me-3">
                                    <input type="search" class="form-control my-1 my-lg-0" id="inputPassword2" name="address" placeholder="地址" value="{{ $request->address }}">
                                </div>
                                {{-- <label for="status-select" class="me-2">Sort By</label> --}}
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="group_id" onchange="this.form.submit()">
                                        <option value="" selected>不限</option>
                                        @foreach($customer_groups as $customer_group)
                                            <option value="{{ $customer_group->id }}" @if($customer_group->id == $request->group_id) selected @endif>{{ $customer_group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="me-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                </div>
                                @if(Auth::user()->job_id != 5)
                                <div class="me-3">
                                    <a href="{{ route('customer.export',request()->input()) }}">
                                        <button type="button" class="btn btn-primary waves-effect waves-light me-1"><i class="fe-download me-1"></i>匯出</button>
                                    </a>
                                </div>
                                @endif
                            </form>
                        </div>
                        <div class="col-auto">
                            <div class="text-lg-end my-1 my-lg-0">
                                {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                                <a href="{{ route('customer.create') }}" class="btn btn-danger waves-effect waves-light"><i class="mdi mdi-plus-circle me-1"></i>新增客戶</a>
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
                                        <th>編號</th>
                                        <th>姓名</th>
                                        <th>電話</th>
                                        <th>寶貝名稱</th>
                                        <th>地址</th>
                                        <th>群組</th>
                                        <th>備註</th>
                                        <th>動作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($customers as $key=>$customer)
                                    <tr>
                                        <td>{{ $customer->id }}</td>
                                        <td class="table-user"><img src="{{asset('assets/images/users/user-4.jpg')}}" alt="table-user" class="me-2 rounded-circle">{{ $customer->name }}</td>
                                        <td>{{ $customer->mobile }}</td>
                                        <td>
                                            @if(isset($customer->sale_datas))
                                                @foreach ($customer->sale_datas as $sale_data)
                                                    {{ $sale_data->pet_name }}<br>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($customer->county))
                                                {{ $customer->county }}{{ $customer->district }}{{ $customer->address }}
                                            @elseif(isset($customer->address))
                                                {{ $customer->address }}
                                            @else
                                                無
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($customer->group))
                                            {{ $customer->group->name }}
                                            @endif
                                        </td>
                                        <td>{{ $customer->comment }}</td>
                                        <td>
                                            <div class="btn-group dropdown">
                                                <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect" data-bs-toggle="dropdown" aria-expanded="false">動作 <i class="mdi mdi-arrow-down-drop-circle"></i></a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="{{ route('customer.detail',$customer->id) }}"><i class="mdi mdi-eye me-2 text-muted font-18 vertical-middle"></i>查看</a>
                                                    <a class="dropdown-item" href="{{ route('customer.sales',$customer->id) }}"><i class="mdi mdi-clipboard-text-search me-2 font-18 text-muted vertical-middle"></i>業務紀錄</a>
                                                    <a class="dropdown-item" href="{{ route('customer.edit',$customer->id) }}"><i class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯</a>
                                                    @if (Auth::user()->level != 2 || Auth::user()->job_id == 3)
                                                    <a class="dropdown-item" href="{{ route('customer.del',$customer->id) }}"><i class="mdi mdi-delete me-2 text-muted font-18 vertical-middle"></i>刪除</a>
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
                                {{ $customers->appends($condition)->links('vendor.pagination.bootstrap-4') }}
                            </ul>
                        </div>
                    </div>
                </div>
                </div>
            </div>

                    

</div> <!-- container -->
@endsection
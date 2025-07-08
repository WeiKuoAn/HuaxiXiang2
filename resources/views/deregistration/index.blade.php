@extends('layouts.vertical', ['page_title' => '除戶列表'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">除戶管理</a></li>
                            <li class="breadcrumb-item active">除戶列表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">除戶列表</h4>
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
                                <form class="d-flex flex-wrap align-items-center"
                                    action="{{ route('deregistration.index') }}" method="GET">
                                    <div class="me-1">
                                        <input type="search" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="registrant" placeholder="登記飼主" value="{{ $request->name }}">
                                    </div>
                                    <div class="me-1">
                                        <input type="search" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="cust_name" placeholder="客戶姓名" value="{{ $request->name }}">
                                    </div>
                                    <div class="me-1">
                                        <input type="search" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="ic_card" placeholder="身分證" value="{{ $request->mobile }}">
                                    </div>
                                    <div class="me-2">
                                        <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i
                                                class="fe-search me-1"></i>搜尋</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-auto">
                                <div class="text-lg-end my-1 my-lg-0">
                                    {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                                    <a href="{{ route('deregistration.create') }}"
                                        class="btn btn-danger waves-effect waves-light"><i
                                            class="mdi mdi-plus-circle me-1"></i>新增除戶記錄</a>
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
                        {{-- <div class="col-12 col-md-12">
                                    <h2 class="card-title" style="font-size: 1.6em;text-align:right;">總支出：<b
                                            style="color:red;">{{ number_format($sum_pay) }}</b>元</h2>
                                </div> --}}
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>晶片號碼</th>
                                        <th>登記飼主</th>
                                        <th>客戶姓名</th>
                                        <th>身分證</th>
                                        <th>寶貝名</th>
                                        <th>品種</th>
                                        <th>備註</th>
                                        <th width="10%">動作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $key => $data)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $data->number }}</td>
                                            <td>{{ $data->registrant }}</td>
                                            <td>{{ $data->customer->name }}</td>
                                            <td>{{ $data->ic_card }}</td>
                                            <td>{{ $data->pet_name }}</td>
                                            <td>{{ $data->variety }}</td>
                                            <td>{{ $data->comment }}</td>
                                            <td>
                                                <a href="{{ route('deregistration.edit', $data->id) }}"
                                                    class="action-icon"> <i class="mdi mdi-square-edit-outline"></i></a>
                                                <a href="{{ route('deregistration.del', $data->id) }}" class="action-icon">
                                                    <i class="mdi mdi-trash-can-outline"></i></a>
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

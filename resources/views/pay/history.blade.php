@extends('layouts.vertical', ['page_title' => 'CRM Customers'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">支出管理</a></li>
                            <li class="breadcrumb-item active">單號【{{ $pay_data->pay_on }}】支出軌跡</li>
                        </ol>
                    </div>
                    <h4 class="page-title">單號【{{ $pay_data->pay_on }}】支出軌跡</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>動作</th>
                                        <th>時間</th>
                                        <th>人員</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $key => $data)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                @if ($data->state == 'create')
                                                    新增支出單
                                                @elseif($data->state == 'update')
                                                    更新支出單
                                                @elseif($data->state == 'return')
                                                    退回支出單
                                                @elseif($data->state == 'other_user_update')
                                                    管理員更新支出單
                                                @elseif($data->state == 'check')
                                                    確認支出單
                                                @endif
                                            </td>
                                            <td>{{ $data->created_at }}</td>
                                            <td>{{ $data->user_name->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="text-center mb-3">
                            <button type="button" class="btn w-sm btn-light waves-effect"
                                onclick="history.go(-1)">回上一頁</button>
                        </div>
                    </div> <!-- end col -->
                </div>
            </div>
        </div>



    </div> <!-- container -->
@endsection

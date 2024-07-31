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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">業務管理</a></li>
                        <li class="breadcrumb-item active">單號【{{ $sale->sale_on }}】業務軌跡</li>
                    </ol>
                </div>
                <h4 class="page-title">單號【{{ $sale->sale_on }}】業務軌跡</h4>
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
                                @foreach ($datas as $key=>$data)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>
                                            @if($data->state == 'create')
                                                新增業務單
                                            @elseif($data->state == 'update')
                                                更新業務單
                                            @elseif($data->state == 'usercheck')
                                                專員送出業務單
                                            @elseif($data->state == 'check')
                                                確認業務單
                                            @elseif($data->state == 'not_check')
                                                撤回對帳單
                                            @elseif($data->state == 'reset')
                                                已對帳還原未對帳單
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
                            <button type="button" class="btn w-sm btn-light waves-effect" onclick="history.go(-1)">回上一頁</button>
                        </div>
                    </div> <!-- end col -->
                </div>
                </div>
            </div>

                    

</div> <!-- container -->
@endsection
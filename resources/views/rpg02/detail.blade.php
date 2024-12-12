@extends('layouts.vertical', ['page_title' => '支出報表'])

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
                            <li class="breadcrumb-item active">支出報表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ '【' . $after_date . '~' . $before_date . '】' . '會計科目：' . $pay_data->name }}</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead>
                                    <tr>
                                        <td width="15%" align="center">No</td>
                                        <td width="15%" align="center">會計科目</td>
                                        <td width="15%" align="center">日期</td>
                                        <td width="25%" align="center">金額</td>
                                        <td width="45%">備註</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td align="center">
                                            <b style="color: red;">共 {{ number_format($total) }} 元</b>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @foreach ($datas as $key => $data)
                                        <tr>
                                            <td align="center">{{ $key + 1 }}</td>
                                            <td align="center">{{ $pay_data->name }}</td>
                                            <td align="center">{{ $data->pay_date }}</td>
                                            <td align="center">{{ number_format($data->price) }}</td>
                                            <td>{{ $data->comment }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <br>
                            <div class="text-center mb-3">
                                <button type="reset" class="btn btn-secondary waves-effect waves-light m-1"
                                    onclick="history.go(-1)">回上一頁</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    </div> <!-- container -->
@endsection

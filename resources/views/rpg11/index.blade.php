@extends('layouts.vertical', ["page_title"=> "年度營收報表"])

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
                        <li class="breadcrumb-item active">年度營收報表</li>
                    </ol>
                </div>
                <h4 class="page-title">年度營收報表</h4>
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
                                    <tr align="center">
                                        <th scope="col">年度</th>
                                        <th scope="col">業務單量</th>
                                        <th scope="col">業務營收</th>
                                        <th scope="col">法會單量</th>
                                        <th scope="col">法會營收</th>
                                        <th scope="col">其他收入</th>
                                        <th scope="col">總營收</th>
                                        <th scope="col">總支出<br>(未含股東分紅)</th>
                                        <th scope="col">淨利</th>
                                        <th scope="col">股東分紅</th>
                                    </tr>
                                </thead>
                                <tbody align="center">
                                    @foreach ($datas as $key=>$data)
                                        <tr>
                                            <td>{{ $data['name'] }}</td>
                                            <td>{{ number_format($data['slae_count']) }}</td>
                                            <td>{{ number_format($data['slae_price']) }}</td>
                                            <td>{{ number_format($data['puja_count']) }}</td>
                                            <td>{{ number_format($data['puja_price']) }}</td>
                                            <td>{{ number_format($data['income_price']) }}</td>
                                            <td>{{ number_format($data['total_income']) }}</td>
                                            <td>{{ number_format($data['pay_price']) }}</td>
                                            <td>{{ number_format($data['total']) }}</td>
                                            <td>
                                                @if(isset($data['percent']))
                                                    {{ $data['percent'] }}%
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tr align="center">
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col" class="text-danger">{{ number_format($net_income) }}</th>
                                    <th scope="col"></th>
                                </tr>
                        </table><br>
                    </div>
                </div>
            </div>
        </div>
    </div>

                    

</div> <!-- container -->
@endsection
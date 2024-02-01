@extends('layouts.vertical', ["page_title"=> "專員紀念品銷售統計"])

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
                        <li class="breadcrumb-item active">專員【{{$prom->name}}】銷售統計</li>
                    </ol>
                </div>
                <h4 class="page-title">專員【{{$prom->name}}】銷售統計</h4>
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
                                    <th>No</th>
                                    <th>日期</th>
                                    <th>方案</th>
                                    <th>寶貝名稱</th>
                                    <th>銷售金額</th>
                                </tr>
                            </thead>
                            <thead >
                                <tr style="color:red;" align="center">
                                    <th colspan="3"></th>
                                    <th>總共：{{ $sums['count'] }}筆</th>
                                    <th>總計：{{ number_format($sums['total']) }}元</th>
                                </tr>
                            </thead>
                            @foreach($datas as $user_id=>$data)
                                <tbody>
                                    <tr align="center">
                                        <td>{{ $data['name'] }}</td>
                                        <td colspan="4"></td>
                                    </tr>

                                    @foreach($data['prom_datas'] as $key=>$da)
                                    <tr>
                                        <td align="center">{{ $key+1 }}</td>
                                        <td align="center">{{ $da->sale_date }}</td>
                                        <td align="center">
                                            @if($da->pay_id == 'D')
                                            尾款
                                            @elseif($da->pay_id == 'E')
                                            追加
                                            @else
                                            {{ $da->plan_name }}
                                            @endif
                                        </td>
                                        <td align="center">{{ $da->pet_name }}</td>
                                        <td align="center">{{ number_format($da->prom_total) }}</td>
                                    </tr>
                                   @endforeach
                                   <tr align="center">
                                       <td colspan="3"></td>
                                       <td>共：{{ number_format($data['total_count']) }}筆</td>
                                       <td>小計：{{ number_format($data['prom_total']) }}元</td>
                                   </tr>
                                </tbody>
                            @endforeach
                        </table><br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- container -->
@endsection
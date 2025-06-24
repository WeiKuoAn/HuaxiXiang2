@extends('layouts.vertical', ['page_title' => '每月來源報表'])

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
                            <li class="breadcrumb-item active">每月來源報表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ $date . '【' . $source_name[$source_code] . '】來源' }}</h4>
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
                                <thead>
                                    <tr align="center">
                                        <td>No</td>
                                        <td>客戶名稱</td>
                                        <td>寵物名稱</td>
                                        <td>後續處理A</td>
                                        <td>後續處理B</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $key => $data)
                                        <tr align="center">
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                @if (isset($data->customer_id))
                                                    @if (isset($data->cust_name))
                                                        {{ $data->cust_name->name }}
                                                    @else
                                                        {{ $data->customer_id }}
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{ $data->pet_name }}</td>
                                            <td>
                                                @if (isset($data->before_prom_id))
                                                    @if (isset($data->PromA_name))
                                                        {{ $data->PromA_name->name }}
                                                    @endif
                                                @endif
                                                @foreach ($data->proms as $prom)
                                                    @if ($prom->prom_type == 'A')
                                                        @if (isset($prom->prom_id))
                                                            {{ $prom->prom_name->name }}<br>
                                                        @else
                                                            無
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($data->proms as $prom)
                                                    @if ($prom->prom_type == 'B')
                                                        @if (isset($prom->prom_id))
                                                            {{ $prom->prom_name->name }}<br>
                                                        @else
                                                            無
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <br>
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center mb-3">
                                        <button type="reset" class="btn btn-secondary waves-effect waves-light m-1"
                                            onclick="history.go(-1)">回上一頁</button>
                                    </div>
                                </div> <!-- end col -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

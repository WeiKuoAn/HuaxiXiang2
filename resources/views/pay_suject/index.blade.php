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
                            <li class="breadcrumb-item active">支出科目</li>
                        </ol>
                    </div>
                    <h4 class="page-title">支出科目</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-sm-8">
                                {{-- <div class="mt-2 mt-sm-0">
                                <button type="button" class="btn btn-success mb-2 me-1"><i class="fe-search me-1"></i>搜尋</button>
                            </div> --}}
                            </div><!-- end col-->
                            <div class="col-sm-4 text-sm-end">
                                <a href="{{ route('pay.suject.create') }}">
                                    <button type="button" class="btn btn-danger waves-effect waves-light"
                                        data-bs-toggle="modal" data-bs-target="#custom-modal"><i
                                            class="mdi mdi-plus-circle me-1"></i>新增支出科目</button>
                                </a>
                            </div>
                        </div>
                        <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                            <thead class="table-light">
                                <tr>
                                <tr>
                                    <th>編號</th>
                                    <th>科目名稱</th>
                                    <th>所屬會計科目</th>
                                    <th>專員查看狀態</th>
                                    <th>排序</th>
                                    <th>狀態</th>
                                    <th>列入報表計算</th>
                                    <th>備註</th>
                                    <th>動作</th>
                                </tr>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datas as $key => $data)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>
                                            @if ($data->suject_type == '0')
                                                營業費用
                                            @elseif($data->suject_type == '1')
                                                營業成本
                                            @elseif($data->suject_type == '2')
                                                其他費用
                                            @endif
                                        </td>
                                        <td>
                                            @if ($data->view_status == '0')
                                                開啟
                                            @else
                                                <b style="color:red;">關閉</b>
                                            @endif
                                        </td>
                                        <td>{{ $data->seq }}</td>
                                        <td>
                                            @if ($data->status == 'up')
                                                啟用
                                            @else
                                                <b style="color:red;">停用</b>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($data->calculate == '0')
                                                是
                                            @else
                                                <b style="color:red;">否</b>
                                            @endif
                                        </td>
                                        <td>{{ $data->comment }}</td>
                                        <td>
                                            <a href="{{ route('pay.suject.edit', $data->id) }}" class="action-icon"> <i
                                                    class="mdi mdi-square-edit-outline"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <br>
                        <ul class="pagination pagination-rounded justify-content-end mb-0">
                            {{ $datas->links('vendor.pagination.bootstrap-4') }}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>



    </div> <!-- container -->
@endsection

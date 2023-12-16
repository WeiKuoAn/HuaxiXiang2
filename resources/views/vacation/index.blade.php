@extends('layouts.vertical', ["page_title"=> "每月總休假設定"])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">人事管理</a></li>
                        <li class="breadcrumb-item active">每月總休假設定</li>
                    </ol>
                </div>
                <h4 class="page-title">每月總休假設定</h4>
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
                            <a href="{{ route('vacation.create') }}">
                                <button type="button" class="btn btn-danger waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#custom-modal"><i class="mdi mdi-plus-circle me-1"></i>新增休假天數</button>
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr align="center">
                                    <th>年份</th>
                                    <th>一月</th>
                                    <th>二月</th>
                                    <th>三月</th>
                                    <th>四月</th>
                                    <th>五月</th>
                                    <th>六月</th>
                                    <th>七月</th>
                                    <th>八月</th>
                                    <th>九月</th>
                                    <th>十月</th>
                                    <th>十一月</th>
                                    <th>十二月</th>
                                    <th>總休假天數</th>
                                    <th>動作</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($datas as $key=>$data)
                                <tr align="center">
                                    <td>{{ $data['year'] }}</td>
                                    @foreach($data['months'] as $month)
                                        <td>{{ $month->day }}天</td>
                                    @endforeach
                                    <td>{{ $data['total'] }}天</td>
                                    <td>
                                        <a href="{{ route('vacation.edit',$data['year']) }}" class="action-icon"> <i class="mdi mdi-square-edit-outline"></i></a>
                                        {{-- <a href="javascript:void(0);" class="action-icon"> <i class="mdi mdi-delete"></i></a> --}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>
    <!-- end row -->

</div> <!-- container -->
@endsection
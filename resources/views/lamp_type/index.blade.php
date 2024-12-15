@extends('layouts.vertical', ["page_title"=> "平安燈類別列表"])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">平安燈管理</a></li>
                        <li class="breadcrumb-item active">平安燈類別列表</li>
                    </ol>
                </div>
                <h4 class="page-title">平安燈類別列表</h4>
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
                            <a href="{{ route('lampType.create') }}">
                                <button type="button" class="btn btn-danger waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#custom-modal"><i class="mdi mdi-plus-circle me-1"></i>新增平安燈類別</button>
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>編號</th>
                                    <th>平安燈類別</th>
                                    <th>狀態</th>
                                    <th>動作</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($datas as $key=>$data)
                                <tr>
                                    <td>{{ $data->id }}</td>
                                    <td>{{ $data->name }}</td>
                                    <td>
                                        @if($data->status == "up") 啟用
                                        @else <b style="color:red;">停用</b>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('contractType.edit',$data->id) }}" class="action-icon"> <i class="mdi mdi-square-edit-outline"></i></a>
                                        {{-- <a href="javascript:void(0);" class="action-icon"> <i class="mdi mdi-delete"></i></a> --}}
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

                    

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>
    <!-- end row -->

</div> <!-- container -->
@endsection
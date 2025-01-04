@extends('layouts.vertical', ["page_title"=> "後續處理列表"])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">設定管理</a></li>
                        <li class="breadcrumb-item active">後續處理列表</li>
                    </ol>
                </div>
                <h4 class="page-title">後續處理列表</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('proms') }}" method="GET">
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="type"  onchange="this.form.submit()">
                                        <option value="" selected>不限</option>
                                        @foreach($prom_types as $prom_type)
                                            <option value="{{ $prom_type->code }}" @if($request->type == $prom_type->code) selected @endif>{{ $prom_type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="me-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-auto">
                            <div class="text-lg-end my-1 my-lg-0">
                                {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                                <a href="{{ route('prom.create') }}" class="btn btn-danger waves-effect waves-light"><i class="mdi mdi-plus-circle me-1"></i>新增後續處理</a>
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
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>編號</th>
                                    <th>類別</th>
                                    <th>名稱</th>
                                    <th>排序</th>
                                    <th>狀態</th>
                                    <th>動作</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($datas as $key=>$data)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>
                                        @if($data->type == 'A')
                                            安葬方式
                                        @elseif($data->type == 'B')
                                            後續處理
                                        @else
                                            其他處理
                                        @endif
                                    </td>
                                    <td>{{ $data->name }}</td>
                                    <td>{{ $data->seq }}</td>
                                    <td>
                                        @if($data->status == "up") 啟用
                                        @else <b style="color:red;">停用</b>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('prom.edit',$data->id) }}" class="action-icon"> <i class="mdi mdi-square-edit-outline"></i></a>
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
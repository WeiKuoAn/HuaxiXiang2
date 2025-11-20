@extends('layouts.vertical', ["page_title"=> "拜訪紀錄"])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">拜訪管理</a></li>
                        <li class="breadcrumb-item active">拜訪列表</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    @if($customer->group_id == '2')
                        醫院拜訪紀錄
                    @elseif($customer->group_id == '5')
                        禮儀社拜訪紀錄
                    @elseif($customer->group_id == '4')
                        繁殖場拜訪紀錄
                    @elseif($customer->group_id == '3')
                        狗園拜訪紀錄
                    @elseif($customer->group_id == '7')
                        其他業者拜訪紀錄
                    @else
                        美容院拜訪紀錄
                    @endif
                </h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('visits',$customer->id) }}" method="GET">
                                <div class="me-3">
                                    <label for="status-select" class="me-2">拜訪日期</label>
                                    <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2" name="after_date" value="{{ $request->after_date }}" placeholder="日期">
                                </div>
                                <div class="me-3">
                                    <label for="status-select" class="me-2">&nbsp;</label>
                                    <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2" name="before_date" value="{{ $request->before_date }}" placeholder="日期">
                                </div>
                                <div class="me-3">
                                    <label for="status-select" class="me-2">拜訪內容</label>
                                    <input type="search" class="form-control my-1 my-lg-0" id="inputPassword2" name="comment" value="{{ $request->comment }}">
                                </div>
                                <div class="me-3 mt-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-auto" style="margin-top: 25px;">
                            <div class="text-lg-end my-1 my-lg-0" >
                                {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                                <a href="{{ route('visit.create',$customer->id) }}">
                                    <button type="button" class="btn btn-danger waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#custom-modal"><i class="fe-search me-1"></i>新增拜訪紀錄</button>
                                </a>
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
                                    <th>拜訪日期</th>
                                    <th>拜訪類別</th>
                                    <th>拜訪內容</th>
                                    <th>拜訪人</th>
                                    <th>動作</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($datas as $key=>$data)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $data->date }}</td>
                                    <td>
                                        @if($data->visit_type === 'supply')
                                            補給
                                        @else
                                            拜訪
                                        @endif
                                    </td>
                                    <td>
                                        <div id="content">{{ $data->comment }}</div>
                                    </td>
                                    <td>
                                        @if($data->visit_type === 'supply')
                                            {{ $data->supplement_by_user->name ?? '-' }}
                                        @else
                                            {{ $data->user_name->name ?? '-' }}
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group dropdown">
                                            <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect" data-bs-toggle="dropdown" aria-expanded="false">動作 <i class="mdi mdi-arrow-down-drop-circle"></i></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="{{ route('visit.edit',[$customer->id,$data->id]) }}"><i class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯</a>
                                                <a class="dropdown-item" href="{{ route('visit.del',[$customer->id,$data->id]) }}"><i class="mdi mdi-delete me-2 font-18 text-muted vertical-middle"></i>刪除</a>
                                            </div>
                                        </div>
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
<style>
    div#content {
        white-space: pre;
    }
</style>
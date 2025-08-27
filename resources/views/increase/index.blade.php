@extends('layouts.vertical', ["page_title"=> "加成列表"])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">加成管理</a></li>
                        <li class="breadcrumb-item active">加成列表</li>
                    </ol>
                </div>
                <h4 class="page-title">加成列表</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('increase.index') }}" method="GET">
                                <div class="me-3">
                                    <label for="start_date" class="form-label">開始日期</label>
                                    <input type="date" class="form-control my-1 my-lg-0" name="start_date" value="{{ $request->start_date ?? '' }}">
                                </div>
                                <div class="me-3">
                                    <label for="end_date" class="form-label">結束日期</label>
                                    <input type="date" class="form-control my-1 my-lg-0" name="end_date" value="{{ $request->end_date ?? '' }}">
                                </div>
                                <div class="me-3 mt-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                    <a href="{{ route('increase.index') }}" class="btn btn-secondary waves-effect waves-light me-1"><i class="fe-refresh-cw me-1"></i>重置</a>
                                </div>
                            </form>
                        </div>
                        <div class="col mt-3">
                            <div class="text-lg-end my-1 my-lg-0 mt-5">
                                <a href="{{ route('increase.create') }}" class="btn btn-danger waves-effect waves-light me-1"><i class="mdi mdi-plus-circle me-1"></i>新增加成</a>
                                <a href="{{ route('increase.export') }}?start_date={{ $request->start_date ?? '' }}&end_date={{ $request->end_date ?? '' }}" class="btn btn-info waves-effect waves-light"><i class="mdi mdi-download me-1"></i>匯出Excel</a>
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
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead class="table-light">
                                    <tr>
                                        <th>加成日期</th>
                                        <th>人員</th>
                                        <th>類型</th>
                                        <th>夜間加成</th>
                                        <th>晚間加成</th>
                                        <th>颱風加成</th>
                                        <th>總金額</th>
                                        <th>動作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($datas as $key=>$data)
                                    @php
                                        $itemCount = 0;
                                        foreach ($data->items as $item) {
                                            if ($item->phone_person_id) $itemCount++;
                                            if ($item->receive_person_id) $itemCount++;
                                        }
                                    @endphp
                                    
                                    @foreach ($data->items as $index => $item)
                                        @if($item->phone_person_id)
                                            <tr>
                                                @if($index == 0)
                                                    <td rowspan="{{ $itemCount }}" class="align-middle">
                                                        <strong>{{ $data->increase_date->format('Y-m-d') }}</strong>
                                                    </td>
                                                @endif
                                                <td>{{ $item->phonePerson->name ?? '未指定' }}</td>
                                                <td><span class="badge bg-primary">接電話</span></td>
                                                <td>${{ number_format($item->night_phone_amount, 0) }}</td>
                                                <td>${{ number_format($item->evening_phone_amount, 0) }}</td>
                                                <td>${{ number_format($item->typhoon_phone_amount, 0) }}</td>
                                                <td>${{ number_format($item->total_phone_amount, 0) }}</td>
                                                <td>
                                                    <div class="btn-group dropdown">
                                                        <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect" data-bs-toggle="dropdown" aria-expanded="false">動作 <i class="mdi mdi-arrow-down-drop-circle"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" href="{{ route('increase.edit',$data->id) }}"><i class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯</a>
                                                            <a class="dropdown-item" href="{{ route('increase.del',$data->id) }}"><i class="mdi mdi-delete me-2 text-muted font-18 vertical-middle"></i>刪除</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        @if($item->receive_person_id)
                                            <tr>
                                                @if($index == 0 && !$item->phone_person_id)
                                                    <td rowspan="{{ $itemCount }}" class="align-middle">
                                                        <strong>{{ $data->increase_date->format('Y-m-d') }}</strong>
                                                    </td>
                                                @endif
                                                <td>{{ $item->receivePerson->name ?? '未指定' }}</td>
                                                <td><span class="badge bg-success">接件</span></td>
                                                <td>${{ number_format($item->night_receive_amount, 0) }}</td>
                                                <td>${{ number_format($item->evening_receive_amount, 0) }}</td>
                                                <td>${{ number_format($item->typhoon_receive_amount, 0) }}</td>
                                                <td>${{ number_format($item->total_receive_amount, 0) }}</td>
                                                <td>
                                                    <div class="btn-group dropdown">
                                                        <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect" data-bs-toggle="dropdown" aria-expanded="false">動作 <i class="mdi mdi-arrow-down-drop-circle"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" href="{{ route('increase.edit',$data->id) }}"><i class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯</a>
                                                            <a class="dropdown-item" href="{{ route('increase.del',$data->id) }}"><i class="mdi mdi-delete me-2 text-muted font-18 vertical-middle"></i>刪除</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
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
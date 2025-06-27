@extends('layouts.vertical', ['page_title' => '待辦管理'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">待辦管理</a></li>
                            <li class="breadcrumb-item active">待辦管理</li>
                        </ol>
                    </div>
                    <h4 class="page-title">待辦管理</h4>
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
                                <form class="d-flex flex-wrap align-items-center" action="{{ route('task') }}"
                                    method="GET">
                                    <div class="me-3">
                                        <label for="title" class="form-label">待辦事項</label>
                                        <input type="text" name="title" class="form-control" placeholder="請輸入待辦事項"
                                            value="{{ request('title') }}">
                                    </div>
                                    <div class="me-3">
                                        <label for="start_date" class="form-label">待辦開始日期</label>
                                        <input type="date" name="start_date" class="form-control"
                                            value="{{ request('start_date') }}">
                                    </div>
                                    <div class="me-3">
                                        <label for="end_date" class="form-label">預計結束日期</label>
                                        <input type="date" name="end_date" class="form-control"
                                            value="{{ request('end_date') }}">
                                    </div>
                                    <div class="me-3">
                                        <label for="status" class="form-label">狀態</label>
                                        <select name="status" class="form-select" onchange="this.form.submit()">
                                            <option value="" selected>全部</option>
                                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>已完成
                                            </option>
                                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>未完成
                                            </option>
                                        </select>
                                    </div>
                                    <div class="me-3">
                                        <label for="assigned_to" class="form-label">指派給</label>
                                        <select name="assigned_to" class="form-select" onchange="this.form.submit()">
                                            <option value="">全部</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="me-3 mt-4">
                                        <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i
                                                class="fe-search me-1"></i>搜尋</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-auto ">
                                <div class="text-lg-end my-1 my-lg-0">
                                    {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                                    <a href="{{ route('task.create') }}" class="btn btn-danger waves-effect waves-light"><i
                                            class="mdi mdi-plus-circle me-1"></i>新增待辦</a>
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
                                        <th>立案人</th>
                                        <th>待辦事項</th>
                                        {{-- <th>待辦開始日期</th> --}}
                                        <th>指派給</th>
                                        <th>預計結束日期</th>
                                        <th>待辦事項說明</th>
                                        <th>狀態</th>
                                        <th>結案人/結案日</th>
                                        <th>動作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $key => $data)
                                        <tr>
                                            <td>{{ $data->created_users->name ?? '' }}</td>
                                            <td>{{ $data->title }}</td>
                                            <td>{{ $data->assigned_users->name ?? '' }}</td>
                                            <td>{{ substr($data->end_date, 0, 16) }}</td>
                                            <td>{{ $data->description }}</td>
                                            <td>
                                                @if ($data->status == '1')
                                                    已完成
                                                @else
                                                    <b style="color:red;">未完成</b>
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($data->close_users))
                                                    {{ $data->close_users->name }} /
                                                    {{ substr($data->updated_at, 0, 16) }}
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('task.edit', $data->id) }}" class="action-icon"> <i
                                                        class="mdi mdi-square-edit-outline"></i></a>
                                                <a href="{{ route('task.del', $data->id) }}" class="action-icon"> <i
                                                        class="mdi mdi-delete"></i></a>
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

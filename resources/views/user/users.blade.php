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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">用戶管理</a></li>
                            <li class="breadcrumb-item active">用戶列表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">用戶列表</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('users') }}" method="GET">
                            <div class="row mb-2">

                                <div class="col-sm-2">
                                    <label for="name" class="form-label">用戶姓名</label>
                                    <input type="text" class="form-control" id="name" name="name" value="">
                                </div><!-- end col-->
                                <div class="col-sm-1">
                                    <label for="project-priority" class="form-label">權限<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" data-toggle="select" data-width="100%" name="status"
                                        onchange="this.form.submit()">
                                        <option value="0" @if ($request->status == '0') selected @endif>開通</option>
                                        <option value="1" @if ($request->status == '1') selected @endif>關閉</option>
                                    </select>
                                </div><!-- end col-->
                                <div class="col-sm-2">
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-success mb-2 me-1"><i
                                                class="fe-search me-1"></i>搜尋</button>
                                    </div>
                                </div><!-- end col-->
                        </form>
                        <div class="col-sm-7 text-sm-end mt-4">
                            <a href="{{ route('user.create') }}">
                                <button type="button" class="btn btn-danger waves-effect waves-light"
                                    data-bs-toggle="modal" data-bs-target="#custom-modal"><i
                                        class="mdi mdi-plus-circle me-1"></i>新增用戶</button>
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>姓名</th>
                                    <th>電話</th>
                                    <th>職稱</th>
                                    <th>入職時間</th>
                                    @if($request->status == 1)
                                        <th>離職時間</th>
                                    @endif
                                    <th>等級</th>
                                    <th>權限</th>
                                    <th>排序</th>
                                    <th>動作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="table-user"><img src="{{ asset('assets/images/users/user-4.jpg') }}"
                                                alt="table-user" class="me-2 rounded-circle">{{ $user->name }}</td>
                                        <td>{{ $user->mobile }}</td>
                                        <td>
                                            @if (isset($user->job_data))
                                                {{ $user->job_data->name }}
                                            @endif
                                        </td>
                                        <td>{{ $user->entry_date }}</td>
                                        @if($request->status == 1)
                                            <td>{{ $user->resign_date }}</td>
                                        @endif 
                                        <td>
                                            @if ($user->level == '0')
                                                <span style="color:red;"> {{ $user->level_state() }}</span>
                                            @else
                                                {{ $user->level_state() }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($user->status == '0')
                                                開通
                                            @else
                                                關閉
                                            @endif
                                        </td>
                                        <td>{{ $user->seq }}</td>
                                        <td>
                                            <div class="btn-group dropdown">
                                                @if ($user->level != '0')
                                                    <a href="javascript: void(0);"
                                                        class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect"
                                                        data-bs-toggle="dropdown" aria-expanded="false">動作 <i
                                                            class="mdi mdi-arrow-down-drop-circle"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item"
                                                            href="{{ route('user.edit', $user->id) }}"><i
                                                                class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯</a>
                                                        {{-- <a class="dropdown-item" href="#"><i class="mdi mdi-delete me-2 text-muted font-18 vertical-middle"></i>刪除</a> --}}
                                                        <a class="dropdown-item"
                                                            href="{{ route('user.sale', $user->id) }}"><i
                                                                class="mdi mdi-clipboard-text-search me-2 font-18 text-muted vertical-middle"></i>查看業務單</a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('user.work.index', $user->id) }}"><i
                                                                class="mdi mdi-clock me-2 font-18 text-muted vertical-middle"></i>出勤紀錄</a>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $users->links() }}

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>
    <!-- end row -->

    </div> <!-- container -->
@endsection

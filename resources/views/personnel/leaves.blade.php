@extends('layouts.vertical', ['page_title' => '假別管理'])

@section('content')
    <style>
        div#content {
            white-space: pre-line;
        }
    </style>
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
                            <li class="breadcrumb-item active">假別管理</li>
                        </ol>
                    </div>
                    <h4 class="page-title">假別管理</h4>
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

                            </div>
                            <div class="col-auto">
                                <div class="text-lg-end my-1 my-lg-0">
                                    {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                                    <a href="{{ route('personnel.leaves.create') }}"
                                        class="btn btn-danger waves-effect waves-light"><i
                                            class="mdi mdi-plus-circle me-1"></i>新增假別</a>
                                    <a href="{{ route('personnel.leavesitting.create') }}"
                                        class="btn btn-primary waves-effect waves-light"><i
                                            class="mdi mdi-plus-circle me-1"></i>新增年度假別天數</a>
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
                                    <tr align="center">
                                        <th>假別</th>
                                        <th>年份/天數</th>
                                        <th>排序</th>
                                        <th>固定天數</th>
                                        <th>狀態</th>
                                        <th>備註</th>
                                        <th>動作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $data)
                                        <tr align="center">
                                            <td width="15%">{{ $data->name }}</td>
                                            <td width="15%">
                                                @foreach ($data->settings as $setting)
                                                    {{ $setting->year }} - {{ $setting->approved_days }}天<a
                                                        href="{{ route('personnel.leavesitting.edit', $setting->id) }}"
                                                        class="action-icon"> <i class="mdi mdi-pencil"></i></a><br>
                                                @endforeach
                                            </td>
                                            <td width="10%">{{ $data->seq }}</td>
                                            <td width="10%">
                                                @if ($data->fixed == 0)
                                                    有
                                                @else
                                                    沒有
                                                @endif
                                            </td>
                                            <td width="10%">
                                                @if ($data->status == 0)
                                                    啟用
                                                @else
                                                    停用
                                                @endif
                                            </td>
                                            <td width="35%" align="left">
                                                <div id="content">{{ $data->comment }}</div>
                                            </td>
                                            <td><a href="{{ route('personnel.leaves.edit', $data->id) }}"
                                                    class="action-icon"> <i class="mdi mdi-square-edit-outline"></i></a>
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
    </div>

    </div> <!-- container -->
@endsection

@extends('layouts.vertical', ['page_title' => '其他業者列表'])

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
                            <li class="breadcrumb-item active">其他業者列表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">其他業者列表</h4>
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
                                <form class="d-flex flex-wrap align-items-center" action="{{ route('others') }}"
                                    method="GET">
                                    <div class="me-3">
                                        <input type="search" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="name" placeholder="姓名" value="{{ $request->name }}">
                                    </div>
                                    <div class="me-3">
                                        <input type="search" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="mobile" placeholder="電話" value="{{ $request->mobile }}">
                                    </div>
                                    <div class="me-3">
                                        <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i
                                                class="fe-search me-1"></i>搜尋</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-auto">
                                <div class="text-lg-end my-1 my-lg-0">
                                    {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                                    <a href="{{ route('visit.company.create') }}"
                                        class="btn btn-danger waves-effect waves-light"><i
                                            class="mdi mdi-plus-circle me-1"></i>新增其他業者</a>
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
                                        <th>編號</th>
                                        <th>姓名</th>
                                        <th>電話</th>
                                        <th>匯款帳號</th>
                                        <th>新增時間</th>
                                        <th>佣金</th>
                                        <th>拜訪</th>
                                        <th>拜訪次數</th>
                                        <th>叫件次數</th>
                                        <th>最近叫件日期</th>
                                        <th>拜訪紀錄</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $key => $data)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $data->name }}<br>
                                            </td>
                                            <td>{{ $data->mobile }}</td>
                                            <td>
                                                @if (isset($data->bank))
                                                    銀行：{{ $data->bank_name }}（{{ $data->bank }}）<br>
                                                    分行：{{ $data->branch_name }}（{{ $data->branch }}）<br>
                                                    帳號：{{ $data->bank_number }}<br>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ date('Y-m-d', strtotime($data->created_at)) }}</td>
                                            <td>
                                                @if ($data->commission == 1)
                                                    有
                                                @else
                                                    無
                                                @endif
                                            </td>
                                            <td>
                                                @if ($data->visit_status == 1)
                                                    有
                                                @else
                                                    無
                                                @endif
                                            </td>
                                            <td>{{ $data->visit_count }}次</td>
                                            <td>{{ $data->sale_count }}次</td>
                                            <td>
                                                @if (isset($data->recently_date))
                                                    {{ $data->recently_date }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group dropdown">
                                                    <a href="javascript: void(0);"
                                                        class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect"
                                                        data-bs-toggle="dropdown" aria-expanded="false">動作 <i
                                                            class="mdi mdi-arrow-down-drop-circle"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item"
                                                            href="{{ route('visit.company.edit', $data->id) }}"><i
                                                                class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯</a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('visit.source.sale', $data->id) }}"><i
                                                                class="mdi mdi-clipboard-text-search me-2 font-18 text-muted vertical-middle"></i>叫件紀錄</a>
                                                        <a class="dropdown-item" href="{{ route('visits', $data->id) }}"><i
                                                                class="mdi mdi-file-document me-2 font-18 text-muted vertical-middle"></i>查看拜訪</a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('visit.create', $data->id) }}"><i
                                                                class="mdi mdi-text-box-plus-outline me-2 text-muted font-18 vertical-middle"></i>新增拜訪</a>
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
                    </div>
                </div>
            </div>
        </div>



    </div> <!-- container -->
@endsection

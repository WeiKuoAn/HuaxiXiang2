@extends('layouts.vertical', ['page_title' => '贈送管理'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">贈送管理</a></li>
                            <li class="breadcrumb-item active">贈送列表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">贈送列表</h4>
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
                                <form class="d-flex flex-wrap align-items-center" action="{{ route('incomes') }}"
                                    method="GET">
                                    <div class="me-3">
                                        <label for="sale_on" class="form-label">單號</label>
                                        <input type="text" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="sale_on" value="{{ $request->sale_on }}">
                                    </div>
                                    <div class="me-sm-3">
                                        <label for="before_date" class="form-label">業務</label>
                                        <select id="inputState" class="form-select" name="user"
                                            onchange="this.form.submit()">
                                            <option value="null" @if (isset($request->user) || $request->user == '') selected @endif>請選擇
                                            </option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    @if ($request->user == $user->id) selected @endif>
                                                    {{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="me-3 mt-4">
                                        <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i
                                                class="fe-search me-1"></i>搜尋</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col mt-3">
                                <div class="text-lg-end my-1 my-lg-0 mt-5">
                                    {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                                    <a href="{{ route('give.create') }}" class="btn btn-danger waves-effect waves-light"><i
                                            class="mdi mdi-plus-circle me-1"></i>新增贈送</a>
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
                                        <th>單號</th>
                                        <th>贈送物</th>
                                        <th>價格</th>
                                        <th>贈送人員</th>
                                        <th>動作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $key => $data)
                                        <tr>
                                            <td>
                                                {{ $data->sale_on }}</td>
                                            <td>{{ $data->value }}</td>
                                            <td>{{ number_format($data->price) }}</td>
                                            <td>{{ $data->user->name }}</td>
                                                                                         <td>
                                                 <a href="{{ route('give.edit', $data->id) }}" class="action-icon me-2">
                                                     <i class="mdi mdi-pencil text-muted font-18"></i>
                                                 </a>
                                                 <a href="{{ route('give.del', $data->id) }}" class="action-icon me-2">
                                                     <i class="mdi mdi-delete text-muted font-18"></i>
                                                 </a>
                                                 <a href="{{ route('sale.sale_on_show', $data->sale_on) }}" target="_blank" class="action-icon">
                                                     <i class="mdi mdi-eye text-muted font-18"></i>
                                                 </a>
                                             </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div> <!-- container -->
@endsection

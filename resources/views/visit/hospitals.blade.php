@extends('layouts.vertical', ['page_title' => '拜訪紀錄'])

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
                            <li class="breadcrumb-item active">醫院列表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">醫院列表</h4>
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
                                <form class="d-flex flex-wrap align-items-center" action="{{ route('hospitals') }}"
                                    method="GET">
                                    <div class="me-1">
                                        <input type="search" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="name" placeholder="姓名" value="{{ $request->name }}">
                                    </div>
                                    <div class="me-1">
                                        <input type="search" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="mobile" placeholder="電話" value="{{ $request->mobile }}">
                                    </div>
                                    <div class="me-sm-1">
                                        <select class="form-select my-1 my-lg-0" id="status-select" name="county"
                                            onchange="this.form.submit()">
                                            <option value="null" selected>選擇地區</option>
                                            @foreach ($countys as $county)
                                                <option value="{{ $county }}"
                                                    @if ($county == $request->county) selected @endif>{{ $county }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="me-sm-2">
                                        <select class="form-select my-1 my-lg-0" id="district" name="district"
                                            onchange="this.form.submit()">
                                            <option value="null" selected>選擇地區</option>
                                            @foreach ($districts as $district)
                                                <option value="{{ $district }}"
                                                    @if ($district == $request->district) selected @endif>{{ $district }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="me-sm-1">
                                        <select class="form-select my-1 my-lg-0" id="status-select" name="commission"
                                            onchange="this.form.submit()">
                                            <option value="null" selected>是否有傭金</option>
                                            <option value="1" @if ($request->commission == 1) selected @endif>有</option>
                                            <option value="0" @if ($request->commission == 0) selected @endif>沒有</option>
                                        </select>
                                    </div>
                                    <div class="me-sm-1">
                                        <select class="form-select my-1 my-lg-0" id="status-select" name="seq"
                                            onchange="this.form.submit()">
                                            <option value="null" selected>排序方式</option>
                                            <option value="desc" @if ($request->seq == "desc") selected @endif>依最新時間</option>
                                            <option value="asc" @if ($request->seq == "asc") selected @endif>依最舊時間</option>
                                        </select>
                                    </div>
                                    <div class="me-2">
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
                                            class="mdi mdi-plus-circle me-1"></i>新增醫院</a>
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
                                        <th>寶貝名稱</th>
                                        <th>群組</th>
                                        <th>傭金</th>
                                        <th>拜訪</th>
                                        <th>拜訪次數</th>
                                        <th>新增時間</th>
                                        <th>拜訪紀錄</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $key => $data)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td class="table-user"><img src="{{ asset('assets/images/users/user-4.jpg') }}"
                                                    alt="table-user" class="me-2 rounded-circle">{{ $data->name }}</td>
                                            <td>{{ $data->mobile }}</td>
                                            <td>
                                                @if (isset($data->sale_datas))
                                                    @foreach ($data->sale_datas as $sale_data)
                                                        {{ $sale_data->pet_name }}<br>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($data->group))
                                                    {{ $data->group->name }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($data->commission == 1)
                                                    有
                                                @else
                                                    無
                                                @endif
                                            </td>
                                            <td>
                                                @if ($data->vist == 1)
                                                    有
                                                @else
                                                    無
                                                @endif
                                            </td>
                                            <td>{{ $data->visit_count }}次</td>
                                            <td>{{ date('Y-m-d', strtotime($data->created_at)) }}</td>
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
                                                            href="{{ route('customer.sales', $data->id) }}"><i
                                                                class="mdi mdi-clipboard-text-search me-2 font-18 text-muted vertical-middle"></i>業務紀錄</a>
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
@section('script')
    {{-- <script>
    function chgCountys(obj) {
        var value = $(obj).val();

        $.ajax({
            type: 'get',
            url: '{{ route('search.district') }}',
            data: { 'county': value },
            success: function(data) {
            console.log(data);
                $('#district').html(data);
            }
        });
    }
    $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
</script> --}}
@endsection

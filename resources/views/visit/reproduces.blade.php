@extends('layouts.vertical', ['page_title' => '繁殖場列表'])

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
                            <li class="breadcrumb-item active">繁殖場列表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">繁殖場列表</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- 操作按鈕區域 -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="text-end">
                    <button type="button" class="btn btn-info waves-effect waves-light me-2" onclick="exportToExcel()">
                        <i class="mdi mdi-download me-1"></i>匯出 Excel
                    </button>
                    <a href="{{ route('visit.company.create') }}" class="btn btn-danger waves-effect waves-light">
                        <i class="mdi mdi-plus-circle me-1"></i>新增繁殖場
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <form action="{{ route('reproduces') }}" method="GET">
                                    <!-- 第一行篩選 -->
                                    <div class="row mb-3">
                                        <div class="col-md-2 mb-2 mb-md-0">
                                            <input type="search" class="form-control" name="name" placeholder="姓名"
                                                value="{{ $request->name }}">
                                        </div>
                                        <div class="col-md-2 mb-2 mb-md-0">
                                            <input type="search" class="form-control" name="mobile" placeholder="電話"
                                                value="{{ $request->mobile }}">
                                        </div>
                                        <div class="col-md-2 mb-2 mb-md-0">
                                            <select class="form-select" name="county" onchange="this.form.submit()">
                                                <option value="null" @if (!isset($request->county) || $request->county == 'null') selected @endif>
                                                    選擇縣市</option>
                                                @foreach ($countys as $county)
                                                    <option value="{{ $county }}"
                                                        @if ($county == $request->county) selected @endif>
                                                        {{ $county }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-2 mb-md-0">
                                            <select class="form-select" name="district" onchange="this.form.submit()">
                                                <option value="null" @if (!isset($request->district) || $request->district == 'null') selected @endif>
                                                    選擇地區</option>
                                                @foreach ($districts as $district)
                                                    <option value="{{ $district }}"
                                                        @if ($district == $request->district) selected @endif>
                                                        {{ $district }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-2 mb-md-0">
                                            <select class="form-select" name="commission" onchange="this.form.submit()">
                                                <option value="null" @if (!isset($request->commission) || $request->commission == 'null') selected @endif>
                                                    是否有佣金</option>
                                                <option value="1" @if ($request->commission === '1') selected @endif>有佣金
                                                </option>
                                                <option value="0" @if ($request->commission === '0') selected @endif>
                                                    沒有佣金</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- 第二行篩選 -->
                                    <div class="row">
                                        <div class="col-md-2 mb-2 mb-md-0">
                                            <select class="form-select" name="has_bank_account"
                                                onchange="this.form.submit()">
                                                <option value="null" @if (!isset($request->has_bank_account) || $request->has_bank_account == 'null') selected @endif>
                                                    是否有匯款帳號</option>
                                                <option value="1" @if ($request->has_bank_account === '1') selected @endif>
                                                    有匯款帳號</option>
                                                <option value="0" @if ($request->has_bank_account === '0') selected @endif>
                                                    沒有匯款帳號</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-2 mb-md-0">
                                            <select class="form-select" name="contract_status"
                                                onchange="this.form.submit()">
                                                <option value="null" @if (!isset($request->contract_status) || $request->contract_status == 'null') selected @endif>
                                                    是否簽約過</option>
                                                <option value="1" @if ($request->contract_status === '1') selected @endif>
                                                    有簽約</option>
                                                <option value="0" @if ($request->contract_status === '0') selected @endif>
                                                    沒有簽約</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-2 mb-md-0">
                                            <select class="form-select" name="assigned_to" onchange="this.form.submit()">
                                                <option value="null" @if (!isset($request->assigned_to) || $request->assigned_to == 'null') selected @endif>
                                                    選擇負責人</option>
                                                @if (isset($users))
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}"
                                                            @if ($request->assigned_to == $user->id) selected @endif>
                                                            {{ $user->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-2 mb-md-0">
                                            <select class="form-select" name="seq" onchange="this.form.submit()">
                                                <option value="null" @if (!isset($request->seq) || $request->seq == 'null') selected @endif>
                                                    建立時間排序</option>
                                                <option value="desc" @if ($request->seq == 'desc') selected @endif>
                                                    最新建立</option>
                                                <option value="asc" @if ($request->seq == 'asc') selected @endif>
                                                    最舊建立</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-2 mb-md-0">
                                            <select class="form-select" name="recently_date_sort"
                                                onchange="this.form.submit()">
                                                <option value="null" @if (!isset($request->recently_date_sort) || $request->recently_date_sort == 'null') selected @endif>
                                                    叫件日期排序</option>
                                                <option value="desc" @if ($request->recently_date_sort == 'desc') selected @endif>
                                                    最新叫件</option>
                                                <option value="asc" @if ($request->recently_date_sort == 'asc') selected @endif>
                                                    最舊叫件</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end mb-2 mb-md-0">
                                            <button type="submit" class="btn btn-success waves-effect waves-light w-100">
                                                <i class="fe-search me-1"></i>搜尋
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
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
                                        <th>簽約</th>
                                        <th>指派人員</th>
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
                                            <td>
                                                @if ($data->contract_status == 1)
                                                    有
                                                @else
                                                    無
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($data->assigned_to_name))
                                                    {{ $data->assigned_to_name->name }}
                                                @else
                                                    -
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
                                                        <a class="dropdown-item" href="{{ route('visits', $data->id) }}"
                                                            target="_blank"><i
                                                                class="mdi mdi-file-document me-2 font-18 text-muted vertical-middle"></i>查看拜訪</a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('visit.create', $data->id) }}"
                                                            target="_blank"><i
                                                                class="mdi mdi-text-box-plus-outline me-2 text-muted font-18 vertical-middle"></i>新增拜訪</a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('visit.source.sale', $data->id) }}"
                                                            target="_blank"><i
                                                                class="mdi mdi-clipboard-text-search me-2 font-18 text-muted vertical-middle"></i>叫件紀錄</a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('visit.company.edit', $data->id) }}"
                                                            target="_blank"><i
                                                                class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯資料</a>
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
    <script>
        function exportToExcel() {
            // 獲取當前的篩選條件
            var currentUrl = new URL(window.location.href);
            var searchParams = currentUrl.searchParams;

            // 構建匯出 URL
            var exportUrl = '{{ route('reproduces.export') }}';
            if (searchParams.toString()) {
                exportUrl += '?' + searchParams.toString();
            }

            // 下載檔案
            window.location.href = exportUrl;
        }
    </script>
@endsection

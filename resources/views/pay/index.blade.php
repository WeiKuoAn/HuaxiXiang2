@extends('layouts.vertical', ['page_title' => '支出列表'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">支出管理</a></li>
                            <li class="breadcrumb-item active">支出列表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">支出列表</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="row">
            <div class="col-12">
                <div class="col mb-3">
                    <div class="text-lg-end my-1 my-lg-0 mt-5">
                        {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                        <button type="button" class="btn btn-success waves-effect waves-light me-1" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="mdi mdi-download me-1"></i>匯出 CSV
                        </button>
                        <a href="{{ route('pay.create') }}" class="btn btn-danger waves-effect waves-light"><i
                                class="mdi mdi-plus-circle me-1"></i>新增支出</a>
                    </div>
                </div><!-- end col-->
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between">
                            <div class="col-auto">
                                <form class="d-flex flex-wrap align-items-center" action="{{ route('pays') }}"
                                    method="GET">
                                    <div class="me-3">
                                        <label for="after_date" class="form-label">key單日期</label>
                                        <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="after_date" value="{{ $request->after_date }}">
                                    </div>
                                    <div class="me-3">
                                        <label for="before_date" class="form-label">&nbsp;</label>
                                        <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="before_date" value="{{ $request->before_date }}">
                                    </div>
                                    <div class="me-3">
                                        <label for="after_date" class="form-label">支出日期</label>
                                        <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="pay_after_date" value="{{ $request->pay_after_date }}">
                                    </div>
                                    <div class="me-3">
                                        <label for="before_date" class="form-label">&nbsp;</label>
                                        <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="pay_before_date" value="{{ $request->pay_before_date }}">
                                    </div>
                                    <div class="me-sm-3" w>
                                        <label for="after_date" class="form-label">備註</label>
                                        <input type="text" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="comment" value="{{ $request->comment }}">
                                    </div>
                                    <div class="me-sm-3">
                                        <label for="before_date" class="form-label">支出來源</label>
                                        <select id="inputState" class="form-select" name="pay"
                                            onchange="this.form.submit()">
                                            <option value="null" @if (isset($request->pay) || $request->pay == '') selected @endif>請選擇
                                            </option>
                                            @foreach ($pays as $pay)
                                                <option value="{{ $pay->id }}"
                                                    @if ($request->pay == $pay->id) selected @endif>
                                                    {{ $pay->name }}</option>
                                            @endforeach
                                        </select>
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
                                    <div class="me-sm-3">
                                        <label for="before_date" class="form-label">狀態</label>
                                        <select id="inputState" class="form-select" name="status"
                                            onchange="this.form.submit()">
                                            <option value="0" @if (!isset($request->status) || $request->status == '0') selected @endif>未審核
                                            </option>
                                            <option value="1" @if ($request->status == '1') selected @endif>已審核
                                            </option>
                                        </select>
                                    </div>
                                    <div class="me-3 mt-3">
                                        <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i
                                                class="fe-search me-1"></i>搜尋</button>
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
                                        <th>key單日期</th>
                                        <th>key單單號</th>
                                        <th>支出日期</th>
                                        <th>支出科目</th>
                                        <th width="20%">發票號碼</th>
                                        <th>支出總價格</th>
                                        <th width="15%">備註</th>
                                        <th width="10%">key單人員</th>
                                        @if ($request->status == '1')
                                            <th>查看</th>
                                        @else
                                            <th>審核</th>
                                        @endif
                                        <th width="10%">動作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $key => $data)
                                        <tr>
                                            <td>{{ $data->pay_date }}</td>
                                            <td>{{ $data->pay_on }}</td>
                                            <td>
                                                @if (isset($pay_items[$data->id]['items']))
                                                    @foreach ($pay_items[$data->id]['items'] as $item)
                                                        {{ $item->pay_date }}<br>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($pay_items[$data->id]['items']))
                                                    @foreach ($pay_items[$data->id]['items'] as $item)
                                                        @if (!empty($item->pay_id))
                                                            {{ $item->pay_name->name }}<br>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($pay_items[$data->id]['items']))
                                                    @foreach ($pay_items[$data->id]['items'] as $item)
                                                        @if (isset($item->pay_id))
                                                            <b>{{ $item->invoice_number }}</b> -
                                                            ${{ number_format($item->price) }}<br>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>{{ number_format($data->price) }}</td>
                                            <td>{{ $data->comment }}</td>
                                            <td>{{ $data->user_name->name }}</td>
                                            <td>
                                                <a href="{{ route('pay.check', $data->id) }}">
                                                    <i
                                                        class="mdi mdi-file-document me-2 text-muted font-18 vertical-middle"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <div class="btn-group dropdown">
                                                    <a href="javascript: void(0);"
                                                        class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect"
                                                        data-bs-toggle="dropdown" aria-expanded="false">動作 <i
                                                            class="mdi mdi-arrow-down-drop-circle"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item"
                                                            href="{{ route('pay.edit', $data->id) }}"><i
                                                                class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯</a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('pay.history', $data->id) }}"><i
                                                                class="mdi mdi-eye me-2 font-18 text-muted vertical-middle"></i>支出軌跡</a>
                                                        @if (Auth::user()->job_id == 1 || Auth::user()->job_id == 2)
                                                            <a class="dropdown-item"
                                                                href="{{ route('pay.del', $data->id) }}"><i
                                                                    class="mdi mdi-delete me-2 text-muted font-18 vertical-middle"></i>刪除</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <br>
                            <ul class="pagination pagination-rounded justify-content-end mb-0">
                                {{ $datas->appends($condition)->links('vendor.pagination.bootstrap-4') }}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div> <!-- container -->

    <!-- 匯出 Excel 彈跳視窗 -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">匯出支出資料</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="exportForm" action="{{ route('pay.export') }}" method="POST">
                    @csrf
                    <!-- 傳遞當前搜尋條件 -->
                    @if($request)
                        <input type="hidden" name="after_date" value="{{ $request->after_date }}">
                        <input type="hidden" name="before_date" value="{{ $request->before_date }}">
                        <input type="hidden" name="pay_after_date" value="{{ $request->pay_after_date }}">
                        <input type="hidden" name="pay_before_date" value="{{ $request->pay_before_date }}">
                        <input type="hidden" name="comment" value="{{ $request->comment }}">
                        <input type="hidden" name="pay" value="{{ $request->pay }}">
                        <input type="hidden" name="user" value="{{ $request->user }}">
                        <input type="hidden" name="status" value="{{ $request->status }}">
                    @endif
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <h6 class="text-muted">選擇要匯出的欄位：</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="pay_date" id="col_pay_date" checked>
                                        <label class="form-check-label" for="col_pay_date">Key單日期</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="pay_on" id="col_pay_on" checked>
                                        <label class="form-check-label" for="col_pay_on">Key單單號</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="item_pay_date" id="col_item_pay_date">
                                        <label class="form-check-label" for="col_item_pay_date">支出日期</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="pay_name" id="col_pay_name">
                                        <label class="form-check-label" for="col_pay_name">支出科目</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="invoice_number" id="col_invoice_number">
                                        <label class="form-check-label" for="col_invoice_number">發票號碼</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="item_price" id="col_item_price">
                                        <label class="form-check-label" for="col_item_price">單項支出金額</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="total_price" id="col_total_price" checked>
                                        <label class="form-check-label" for="col_total_price">支出總價格</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="comment" id="col_comment">
                                        <label class="form-check-label" for="col_comment">備註</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="user_name" id="col_user_name" checked>
                                        <label class="form-check-label" for="col_user_name">Key單人員</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="status" id="col_status">
                                        <label class="form-check-label" for="col_status">審核狀態</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label fw-bold" for="selectAll">全選 / 全不選</label>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="mdi mdi-information me-2"></i>
                            匯出將根據目前的篩選條件進行，請確認已選擇正確的篩選設定。
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-success">
                            <i class="mdi mdi-download me-1"></i>匯出 CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // 全選 / 全不選功能
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="columns[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // 監聽個別checkbox變化，更新全選狀態
        document.querySelectorAll('input[name="columns[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('input[name="columns[]"]');
                const checkedCount = document.querySelectorAll('input[name="columns[]"]:checked').length;
                const selectAllCheckbox = document.getElementById('selectAll');
                
                if (checkedCount === checkboxes.length) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else if (checkedCount === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                }
            });
        });

        // 表單提交前驗證
        document.getElementById('exportForm').addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('input[name="columns[]"]:checked');
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('請至少選擇一個要匯出的欄位！');
                return false;
            }
        });
    </script>
@endsection

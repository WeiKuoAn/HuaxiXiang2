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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">業務管理</a></li>
                            <li class="breadcrumb-item active">業務列表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">業務列表</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between">
                            <form action="{{ route('sales') }}" method="GET">
                                <div class="col-auto d-flex flex-wrap align-items-center">
                                    <div class="me-2">
                                        <label for="after_date" class="form-label">單號日期</label>
                                        <input type="date" class="form-control" id="after_date" name="after_date"
                                            value="{{ $request->after_date }}">
                                    </div>
                                    <div class="me-2">
                                        <label for="before_date" class="form-label">&nbsp;</label>
                                        <input type="date" class="form-control" id="before_date" name="before_date"
                                            value="{{ $request->before_date }}">
                                    </div>
                                    <div class="me-2">
                                        <label for="sale_on" class="form-label">案件單類別</label>
                                        <select id="inputState" class="form-select" name="type_list"
                                            onchange="this.form.submit()">
                                            <option value="null" @if (!isset($request->type_list) || $request->type_list == 'null') selected @endif>不限
                                            </option>
                                            <option value="dispatch" @if ($request->type_list == 'dispatch') selected @endif>派件單
                                            </option>
                                            <option value="memorial" @if ($request->type_list == 'memorial') selected @endif>追思單
                                            </option>
                                        </select>
                                    </div>
                                    <div class="me-2">
                                        <label for="sale_on" class="form-label">單號</label>
                                        <input type="text" class="form-control" id="sale_on" name="sale_on"
                                            value="{{ $request->sale_on }}">
                                    </div>
                                    <div class="me-2">
                                        <label for="cust_name" class="form-label">客戶姓名</label>
                                        <input type="text" class="form-control" id="cust_name" name="cust_name"
                                            value="{{ $request->cust_name }}">
                                    </div>
                                </div>
                                <div class="col-auto d-flex flex-wrap align-items-center mt-3">
                                    <div class="me-2">
                                        <label for="pet_name" class="form-label">寶貝名稱</label>
                                        <input type="text" class="form-control" id="pet_name" name="pet_name"
                                            value="{{ $request->pet_name }}">
                                    </div>
                                    <div class="me-2">
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
                                    <div class="me-2">
                                        <label for="sale_on" class="form-label">方案</label>
                                        <select id="inputState" class="form-select" name="plan"
                                            onchange="this.form.submit()">
                                            <option value="null" @if (isset($request->plan) || $request->plan == '') selected @endif>請選擇
                                            </option>
                                            @foreach ($plans as $plan)
                                                <option value="{{ $plan->id }}"
                                                    @if ($request->plan == $plan->id) selected @endif>
                                                    {{ $plan->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="me-3 mt-1">
                                        <label for="after_date">付款方式</label>
                                        <select id="inputState" class="form-select" name="pay_id"
                                            onchange="this.form.submit()">
                                            <option value="" @if (!isset($request->pay_id)) selected @endif>請選擇
                                            </option>
                                            <option value="A" @if ($request->pay_id == 'A') selected @endif>一次付清
                                            </option>
                                            <option value="C" @if ($request->pay_id == 'C') selected @endif>訂金
                                            </option>
                                            <option value="E" @if ($request->pay_id == 'E') selected @endif>追加
                                            </option>
                                            <option value="D" @if ($request->pay_id == 'D') selected @endif>尾款
                                            </option>
                                        </select>
                                    </div>
                                    <div class="me-3 mt-1">
                                        <label for="after_date">其他動作</label>
                                        <select id="inputState" class="form-select" name="other"
                                            onchange="this.form.submit()">
                                            <option value="" @if (!isset($request->other)) selected @endif>請選擇
                                            </option>
                                            <option value="change" @if ($request->other == 'change') selected @endif>轉單
                                            </option>
                                            <option value="split" @if ($request->other == 'split') selected @endif>對拆
                                            </option>
                                        </select>
                                    </div>
                                    <div class="me-3 mt-1">
                                        <label for="after_date">狀態</label>
                                        <select id="inputState" class="form-select" name="status"
                                            onchange="this.form.submit()">
                                            <option value="not_check" @if (isset($request->status) || $request->status == 'not_check') selected @endif>
                                                未對帳</option>
                                            <option value="check" @if ($request->status == 'check') selected @endif>已對帳
                                            </option>
                                        </select>
                                    </div>
                                    <div class="me-4">
                                        <label for="sale_on" class="form-label">對帳人員</label>
                                        <select id="inputState" class="form-select" name="check_user_id"
                                            onchange="this.form.submit()">
                                            <option value="null" @if (isset($request->check_user_id) || $request->check_user_id == '') selected @endif>請選擇
                                            </option>
                                            @foreach ($check_users as $check_user)
                                                <option value="{{ $check_user->id }}"
                                                    @if ($request->check_user_id == $check_user->id) selected @endif>
                                                    {{ $check_user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="me-3 mt-4">
                                        <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i
                                                class="fe-search me-1"></i>搜尋</button>
                                    </div>
                                    <div class="me-3 mt-4">
                                        <button type="button" class="btn btn-primary waves-effect waves-light me-1" onclick="showExportModal()">
                                            <i class="fe-download me-1"></i>匯出設定
                                        </button>
                                    </div>
                                    <div class="col mt-3" style="text-align: right;">
                                        {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                                        <a href="{{ route('sale.create') }}"
                                            class="btn btn-danger waves-effect waves-light"><i
                                                class="mdi mdi-plus-circle me-1"></i>新增業務</a>
                                    </div>
                                </div>
                            </form>
                            <!-- end col-->
                        </div> <!-- end row -->
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col-->
        </div>
        {{-- <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Accordion</h4>
                        <p class="sub-header">
                            include accordion in your FooTable.
                        </p>

                        <table id="demo-foo-accordion" class="table table-colored mb-0 toggle-arrow-tiny">
                            <thead>
                                <tr>
                                    <th data-toggle="true"> First Name </th>
                                    <th> Last Name </th>
                                    <th data-hide="phone"> Job Title </th>
                                    <th data-hide="all"> DOB </th>
                                    <th data-hide="all"> Status </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Isidra</td>
                                    <td>Boudreaux</td>
                                    <td>Traffic Court Referee</td>
                                    <td>22 Jun 1972</td>
                                    <td><span class="badge label-table bg-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td>Shona</td>
                                    <td>Woldt</td>
                                    <td>Airline Transport Pilot</td>
                                    <td>3 Oct 1981</td>
                                    <td><span class="badge label-table bg-secondary text-light">Disabled</span></td>
                                </tr>
                                <tr>
                                    <td>Granville</td>
                                    <td>Leonardo</td>
                                    <td>Business Services Sales Representative</td>
                                    <td>19 Apr 1969</td>
                                    <td><span class="badge label-table bg-danger">Suspended</span></td>
                                </tr>
                                <tr>
                                    <td>Easer</td>
                                    <td>Dragoo</td>
                                    <td>Drywall Stripper</td>
                                    <td>13 Dec 1977</td>
                                    <td><span class="badge label-table bg-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td>Maple</td>
                                    <td>Halladay</td>
                                    <td>Aviation Tactical Readiness Officer</td>
                                    <td>30 Dec 1991</td>
                                    <td><span class="badge label-table bg-danger">Suspended</span></td>
                                </tr>
                                <tr>
                                    <td>Maxine</td>
                                    <td><a href="#">Woldt</a></td>
                                    <td><a href="#">Business Services Sales Representative</a></td>
                                    <td>17 Oct 1987</td>
                                    <td><span class="badge label-table bg-secondary text-light">Disabled</span></td>
                                </tr>
                                <tr>
                                    <td>Lorraine</td>
                                    <td>Mcgaughy</td>
                                    <td>Hemodialysis Technician</td>
                                    <td>11 Nov 1983</td>
                                    <td><span class="badge label-table bg-secondary text-light">Disabled</span></td>
                                </tr>
                                <tr>
                                    <td>Lizzee</td>
                                    <td><a href="#">Goodlow</a></td>
                                    <td>Technical Services Librarian</td>
                                    <td>1 Nov 1961</td>
                                    <td><span class="badge label-table bg-danger">Suspended</span></td>
                                </tr>
                                <tr>
                                    <td>Judi</td>
                                    <td>Badgett</td>
                                    <td>Electrical Lineworker</td>
                                    <td>23 Jun 1981</td>
                                    <td><span class="badge label-table bg-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td>Lauri</td>
                                    <td>Hyland</td>
                                    <td>Blackjack Supervisor</td>
                                    <td>15 Nov 1985</td>
                                    <td><span class="badge label-table bg-danger">Suspended</span></td>
                                </tr>
                                <tr>
                                    <td>Isidra</td>
                                    <td>Boudreaux</td>
                                    <td>Traffic Court Referee</td>
                                    <td>22 Jun 1972</td>
                                    <td><span class="badge label-table bg-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td>Shona</td>
                                    <td>Woldt</td>
                                    <td>Airline Transport Pilot</td>
                                    <td>3 Oct 1981</td>
                                    <td><span class="badge label-table bg-secondary text-light">Disabled</span></td>
                                </tr>
                                <tr>
                                    <td>Granville</td>
                                    <td>Leonardo</td>
                                    <td>Business Services Sales Representative</td>
                                    <td>19 Apr 1969</td>
                                    <td><span class="badge label-table bg-danger">Suspended</span></td>
                                </tr>
                                <tr>
                                    <td>Easer</td>
                                    <td>Dragoo</td>
                                    <td>Drywall Stripper</td>
                                    <td>13 Dec 1977</td>
                                    <td><span class="badge label-table bg-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td>Maple</td>
                                    <td>Halladay</td>
                                    <td>Aviation Tactical Readiness Officer</td>
                                    <td>30 Dec 1991</td>
                                    <td><span class="badge label-table bg-danger">Suspended</span></td>
                                </tr>
                                <tr>
                                    <td>Maxine</td>
                                    <td><a href="#">Woldt</a></td>
                                    <td><a href="#">Business Services Sales Representative</a></td>
                                    <td>17 Oct 1987</td>
                                    <td><span class="badge label-table bg-secondary text-light">Disabled</span></td>
                                </tr>
                                <tr>
                                    <td>Lorraine</td>
                                    <td>Mcgaughy</td>
                                    <td>Hemodialysis Technician</td>
                                    <td>11 Nov 1983</td>
                                    <td><span class="badge label-table bg-secondary text-light">Disabled</span></td>
                                </tr>
                                <tr>
                                    <td>Lizzee</td>
                                    <td><a href="#">Goodlow</a></td>
                                    <td>Technical Services Librarian</td>
                                    <td>1 Nov 1961</td>
                                    <td><span class="badge label-table bg-danger">Suspended</span></td>
                                </tr>
                                <tr>
                                    <td>Judi</td>
                                    <td>Badgett</td>
                                    <td>Electrical Lineworker</td>
                                    <td>23 Jun 1981</td>
                                    <td><span class="badge label-table bg-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td>Lauri</td>
                                    <td>Hyland</td>
                                    <td>Blackjack Supervisor</td>
                                    <td>15 Nov 1985</td>
                                    <td><span class="badge label-table bg-danger">Suspended</span></td>
                                </tr>
                                <tr>
                                    <td>Isidra</td>
                                    <td>Boudreaux</td>
                                    <td>Traffic Court Referee</td>
                                    <td>22 Jun 1972</td>
                                    <td><span class="badge label-table bg-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td>Shona</td>
                                    <td>Woldt</td>
                                    <td>Airline Transport Pilot</td>
                                    <td>3 Oct 1981</td>
                                    <td><span class="badge label-table bg-secondary text-light">Disabled</span></td>
                                </tr>
                                <tr>
                                    <td>Granville</td>
                                    <td>Leonardo</td>
                                    <td>Business Services Sales Representative</td>
                                    <td>19 Apr 1969</td>
                                    <td><span class="badge label-table bg-danger">Suspended</span></td>
                                </tr>
                                <tr>
                                    <td>Easer</td>
                                    <td>Dragoo</td>
                                    <td>Drywall Stripper</td>
                                    <td>13 Dec 1977</td>
                                    <td><span class="badge label-table bg-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td>Maple</td>
                                    <td>Halladay</td>
                                    <td>Aviation Tactical Readiness Officer</td>
                                    <td>30 Dec 1991</td>
                                    <td><span class="badge label-table bg-danger">Suspended</span></td>
                                </tr>
                                <tr>
                                    <td>Maxine</td>
                                    <td><a href="#">Woldt</a></td>
                                    <td><a href="#">Business Services Sales Representative</a></td>
                                    <td>17 Oct 1987</td>
                                    <td><span class="badge label-table  text-light">Disabled</span></td>
                                </tr>
                                <tr>
                                    <td>Lorraine</td>
                                    <td>Mcgaughy</td>
                                    <td>Hemodialysis Technician</td>
                                    <td>11 Nov 1983</td>
                                    <td><span class="badge label-table bg-secondary text-light">Disabled</span></td>
                                </tr>
                                <tr>
                                    <td>Lizzee</td>
                                    <td><a href="#">Goodlow</a></td>
                                    <td>Technical Services Librarian</td>
                                    <td>1 Nov 1961</td>
                                    <td><span class="badge label-table bg-danger">Suspended</span></td>
                                </tr>
                                <tr>
                                    <td>Judi</td>
                                    <td>Badgett</td>
                                    <td>Electrical Lineworker</td>
                                    <td>23 Jun 1981</td>
                                    <td><span class="badge label-table bg-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td>Lauri</td>
                                    <td>Hyland</td>
                                    <td>Blackjack Supervisor</td>
                                    <td>15 Nov 1985</td>
                                    <td><span class="badge label-table bg-danger">Suspended</span></td>
                                </tr>
                            </tbody>

                        </table>
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div> --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead class="table-light">
                                    <tr>
                                        <th>單號</th>
                                        <th>Key單人員</th>
                                        <th>日期</th>
                                        <th>客戶</th>
                                        <th>寶貝名</th>
                                        <th>類別</th>
                                        <th>方案</th>
                                        <th>金紙</th>
                                        <th>安葬方式</th>
                                        <th>後續處理</th>
                                        <th>付款方式</th>
                                        <th>實收價格</th>
                                        @if ($request->status == 'check')
                                            <th>轉單</th>
                                            <th>對拆</th>
                                        @endif
                                        <th>動作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $sale)
                                        <tr>
                                            <td>{{ $sale->sale_on }}</td>
                                            <td>{{ $sale->user_name->name }}</td>
                                            <td>{{ $sale->sale_date }}</td>
                                            <td>
                                                @if ($sale->type_list == 'dispatch')
                                                    @if (isset($sale->customer_id))
                                                        @if (isset($sale->cust_name))
                                                            {{ $sale->cust_name->name }}
                                                        @else
                                                            {{ $sale->customer_id }}<b style="color: red;">（客戶姓名須重新登入）</b>
                                                        @endif
                                                    @endif
                                                @elseif($sale->type_list == 'memorial')
                                                    @if (isset($sale->customer_id))
                                                        @if (isset($sale->cust_name))
                                                            {{ $sale->cust_name->name }}-追思
                                                        @else
                                                            {{ $sale->customer_id }}<b style="color: red;">（客戶姓名須重新登入）</b>
                                                        @endif
                                                    @else
                                                        追思
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($sale->pet_name))
                                                    {{ $sale->pet_name }}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($sale->type))
                                                    @if (isset($sale->source_type))
                                                        {{ $sale->source_type->name }}
                                                    @else
                                                        {{ $sale->type }}
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($sale->plan_id))
                                                    @if (isset($sale->plan_name))
                                                        {{ $sale->plan_name->name }}
                                                    @else
                                                        {{ $sale->plan_id }}
                                                    @endif
                                                @endif
                                                {{-- {{ $sale->plan_id }} --}}
                                            </td>
                                            <td>
                                                @if (isset($sale->gdpapers))
                                                    @foreach ($sale->gdpapers as $gdpaper)
                                                        @if (isset($gdpaper->gdpaper_id))
                                                            @if (isset($gdpaper->gdpaper_name))
                                                                {{ $gdpaper->gdpaper_name->name }}({{ number_format($gdpaper->gdpaper_total) }})元<br>
                                                            @endif
                                                        @else
                                                            無
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($sale->before_prom_id))
                                                    @if (isset($sale->PromA_name))
                                                        {{ $sale->PromA_name->name }}-{{ number_format($sale->before_prom_price) }}
                                                    @else
                                                        {{ $sale->before_prom_id }}
                                                    @endif
                                                @endif
                                                {{ $sale->before_prom_id }}
                                                @foreach ($sale->proms as $prom)
                                                    @if ($prom->prom_type == 'A')
                                                        @if (isset($prom->prom_id))
                                                            {{ $prom->prom_name->name }}-{{ number_format($prom->prom_total) }}<br>
                                                        @else
                                                            無
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($sale->proms as $prom)
                                                    @if ($prom->prom_type == 'B')
                                                        @if (isset($prom->prom_id))
                                                            {{ $prom->prom_name->name }}-{{ number_format($prom->prom_total) }}<br>
                                                        @else
                                                            無
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @if (isset($sale->pay_id))
                                                    {{ $sale->pay_type() }}
                                                @endif
                                            </td>
                                            <td>{{ number_format($sale->pay_price) }}</td>
                                            @if ($request->status == 'check')
                                                <td>
                                                    @if (isset($sale->SaleChange))
                                                        Y
                                                    @else
                                                        N
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (isset($sale->SaleSplit))
                                                        {{ $sale->SaleSplit->user_name->name }}
                                                    @else
                                                        N
                                                    @endif
                                                </td>
                                            @endif

                                            <td>
                                                {{-- @if ($sale->status != '9')
                                                <a href="{{ route('edit-sale', $sale->id) }}"><button type="button"
                                                        class="btn btn-secondary btn-sm">修改</button></a>
                                                        <a href="{{ route('del-sale', $sale->id) }}"><button type="button"
                                                            class="btn btn-secondary btn-sm">刪除</button></a>
                                                <a href="{{ route('check-sale', $sale->id) }}"><button type="button"
                                                        class="btn btn-success btn-sm">送出對帳</button></a>
                                            @else
                                                <a href="{{ route('check-sale', $sale->id) }}"><button type="button"
                                                        class="btn btn-danger btn-sm">查看</button></a>
                                            @endif --}}
                                                @if (Auth::user()->level != 2 || $sale->user_id == Auth::user()->id)
                                                    @if ($sale->status != '9')
                                                        <div class="btn-group dropdown">
                                                            <a href="javascript: void(0);"
                                                                class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect"
                                                                data-bs-toggle="dropdown" aria-expanded="false">動作 <i
                                                                    class="mdi mdi-arrow-down-drop-circle"></i></a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a class="dropdown-item"
                                                                    href="{{ route('sale.edit', $sale->id) }}"><i
                                                                        class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯</a>
                                                                {{-- <a class="dropdown-item" href="#"><i class="mdi mdi-delete me-2 text-muted font-18 vertical-middle"></i>刪除</a> --}}
                                                                <a class="dropdown-item"
                                                                    href="{{ route('sale.del', $sale->id) }}"><i
                                                                        class="mdi mdi-delete me-2 font-18 text-muted vertical-middle"></i>刪除</a>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('sale.check', $sale->id) }}"><i
                                                                        class="mdi mdi-send me-2 font-18 text-muted vertical-middle"></i>送出對帳</a>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="btn-group dropdown">
                                                            <a href="javascript: void(0);"
                                                                class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect"
                                                                data-bs-toggle="dropdown" aria-expanded="false">動作 <i
                                                                    class="mdi mdi-arrow-down-drop-circle"></i></a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a class="dropdown-item"
                                                                    href="{{ route('sale.check', $sale->id) }}"><i
                                                                        class="mdi mdi-eye me-2 font-18 text-muted vertical-middle"></i>查看</a>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('sale.history', $sale->id) }}"><i
                                                                        class="mdi mdi-eye me-2 font-18 text-muted vertical-middle"></i>業務軌跡</a>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('sale.change_plan', $sale->id) }}"><i
                                                                        class="mdi mdi-vanish me-2 text-muted font-18 vertical-middle"></i>修改方案</a>
                                                                {{-- <a class="dropdown-item" href="{{ route('sale.change',$sale->id) }}"><i class="mdi mdi-autorenew me-2 text-muted font-18 vertical-middle"></i>轉單/對拆</a>
                                                            <a class="dropdown-item" href="{{ route('sale.change.record',$sale->id) }}"><i class="mdi mdi-cash me-2 text-muted font-18 vertical-middle"></i>轉單/對拆紀錄</a> --}}
                                                            </div>
                                                        </div>
                                                    @endif
                                                @else
                                                    @if ($sale->status == '9')
                                                        <div class="btn-group dropdown">
                                                            <a href="javascript: void(0);"
                                                                class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect"
                                                                data-bs-toggle="dropdown" aria-expanded="false">動作 <i
                                                                    class="mdi mdi-arrow-down-drop-circle"></i></a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a class="dropdown-item"
                                                                    href="{{ route('sale.check', $sale->id) }}"><i
                                                                        class="mdi mdi-eye me-2 font-18 text-muted vertical-middle"></i>查看</a>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('sale.change_plan', $sale->id) }}"><i
                                                                        class="mdi mdi-vanish me-2 text-muted font-18 vertical-middle"></i>修改方案</a>
                                                                {{-- <a class="dropdown-item" href="{{ route('sale.change',$sale->id) }}"><i class="mdi mdi-autorenew me-2 text-muted font-18 vertical-middle"></i>轉單/對拆</a>
                                                            <a class="dropdown-item" href="{{ route('sale.change.record',$sale->id) }}"><i class="mdi mdi-cash me-2 text-muted font-18 vertical-middle"></i>轉單/對拆紀錄</a> --}}
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <br>
                            <ul class="pagination pagination-rounded justify-content-end mb-0">
                                {{ $sales->appends($condition)->links('vendor.pagination.bootstrap-4') }}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 匯出設定 Modal -->
        <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportModalLabel">匯出設定</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeExportModal()"></button>
                    </div>
                    <form action="{{ route('sales.export') }}" method="GET" id="exportForm">
                        <!-- 隱藏的篩選條件 -->
                        <input type="hidden" name="after_date" value="{{ $request->after_date }}">
                        <input type="hidden" name="before_date" value="{{ $request->before_date }}">
                        <input type="hidden" name="type_list" value="{{ $request->type_list }}">
                        <input type="hidden" name="sale_on" value="{{ $request->sale_on }}">
                        <input type="hidden" name="cust_name" value="{{ $request->cust_name }}">
                        <input type="hidden" name="pet_name" value="{{ $request->pet_name }}">
                        <input type="hidden" name="user" value="{{ $request->user }}">
                        <input type="hidden" name="plan" value="{{ $request->plan }}">
                        <input type="hidden" name="pay_id" value="{{ $request->pay_id }}">
                        <input type="hidden" name="other" value="{{ $request->other }}">
                        <input type="hidden" name="status" value="{{ $request->status }}">
                        <input type="hidden" name="check_user_id" value="{{ $request->check_user_id }}">
                        
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h6>選擇要匯出的欄位：</h6>
                                    <div class="mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="selectAllFields()">全選</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllFields()">取消全選</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- 基本資訊 -->
                                <div class="col-md-4">
                                    <h6 class="text-primary">基本資訊</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="案件單類別" id="field_案件單類別" checked>
                                        <label class="form-check-label" for="field_案件單類別">案件單類別</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="單號" id="field_單號" checked>
                                        <label class="form-check-label" for="field_單號">單號</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="專員" id="field_專員" checked>
                                        <label class="form-check-label" for="field_專員">專員</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="日期" id="field_日期" checked>
                                        <label class="form-check-label" for="field_日期">日期</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="客戶" id="field_客戶" checked>
                                        <label class="form-check-label" for="field_客戶">客戶</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="寶貝名" id="field_寶貝名" checked>
                                        <label class="form-check-label" for="field_寶貝名">寶貝名</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="寵物品種" id="field_寵物品種" checked>
                                        <label class="form-check-label" for="field_寵物品種">寵物品種</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="公斤數" id="field_公斤數" checked>
                                        <label class="form-check-label" for="field_公斤數">公斤數</label>
                                    </div>
                                    
                                </div>
                                
                                <!-- 方案資訊 -->
                                <div class="col-md-4">
                                    <h6 class="text-success">方案資訊</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="方案" id="field_方案" checked>
                                        <label class="form-check-label" for="field_方案">方案</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="方案價格" id="field_方案價格" checked>
                                        <label class="form-check-label" for="field_方案價格">方案價格</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="案件來源" id="field_案件來源" checked>
                                        <label class="form-check-label" for="field_案件來源">案件來源</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="來源名稱" id="field_來源名稱" checked>
                                        <label class="form-check-label" for="field_來源名稱">來源名稱</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="套裝" id="field_套裝" checked>
                                        <label class="form-check-label" for="field_套裝">套裝</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="金紙" id="field_金紙" checked>
                                        <label class="form-check-label" for="field_金紙">金紙</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="金紙總賣價" id="field_金紙總賣價" checked>
                                        <label class="form-check-label" for="field_金紙總賣價">金紙總賣價</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="安葬方式" id="field_安葬方式" checked>
                                        <label class="form-check-label" for="field_安葬方式">安葬方式</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="後續處理" id="field_後續處理" checked>
                                        <label class="form-check-label" for="field_後續處理">後續處理</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="其他處理" id="field_其他處理" checked>
                                        <label class="form-check-label" for="field_其他處理">其他處理</label>
                                    </div>
                                </div>
                                
                                <!-- 付款資訊 -->
                                <div class="col-md-4">
                                    <h6 class="text-warning">付款/其他資訊</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="付款類別" id="field_付款類別" checked>
                                        <label class="form-check-label" for="field_付款類別">付款類別</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="支付方式" id="field_支付方式" checked>
                                        <label class="form-check-label" for="field_支付方式">支付方式</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="實收價格" id="field_實收價格" checked>
                                        <label class="form-check-label" for="field_實收價格">實收價格</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="狀態" id="field_狀態" checked>
                                        <label class="form-check-label" for="field_狀態">狀態</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="親送" id="field_親送" checked>
                                        <label class="form-check-label" for="field_親送">親送</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="接體地址不為客戶地址" id="field_接體地址不為客戶地址" checked>
                                        <label class="form-check-label" for="field_接體地址不為客戶地址">接體地址不為客戶地址</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="接體為醫院" id="field_接體為醫院" checked>
                                        <label class="form-check-label" for="field_接體為醫院">接體為醫院</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="備註" id="field_備註" checked>
                                        <label class="form-check-label" for="field_備註">備註</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="更改後方案" id="field_更改後方案" checked>
                                        <label class="form-check-label" for="field_更改後方案">更改後方案</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="確認對帳人員" id="field_確認對帳人員" checked>
                                        <label class="form-check-label" for="field_確認對帳人員">確認對帳人員</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="export_fields[]" value="確認對帳時間" id="field_確認對帳時間" checked>
                                        <label class="form-check-label" for="field_確認對帳時間">確認對帳時間</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeExportModal()">取消</button>
                            <button type="submit" class="btn btn-primary" onclick="return validateExportForm()">
                                <i class="fe-download me-1"></i>匯出 CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div> <!-- container -->
@endsection
@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/libs/footable/footable.min.js') }}"></script>
    <!-- third party js ends -->

    <!-- demo app -->
    <script src="{{ asset('assets/js/pages/foo-tables.init.js') }}"></script>
    <!-- end demo js-->
    
    <script>
        function showExportModal() {
            // 使用 jQuery 的方式來確保相容性
            if (typeof $ !== 'undefined') {
                $('#exportModal').modal('show');
            } else {
                // 備用方案：使用原生 JavaScript
                var modal = document.getElementById('exportModal');
                if (modal) {
                    var bootstrapModal = new bootstrap.Modal(modal);
                    bootstrapModal.show();
                } else {
                    // 如果 Modal 不存在，直接提交表單
                    submitExportForm();
                }
            }
        }
        
        function selectAllFields() {
            document.querySelectorAll('input[name="export_fields[]"]').forEach(checkbox => {
                checkbox.checked = true;
            });
        }
        
        function deselectAllFields() {
            document.querySelectorAll('input[name="export_fields[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        }
        
        function validateExportForm() {
            const checkedFields = document.querySelectorAll('input[name="export_fields[]"]:checked');
            if (checkedFields.length === 0) {
                alert('請至少選擇一個欄位進行匯出');
                return false;
            }
            return true;
        }
        
        // 備用匯出函數
        function submitExportForm() {
            // 創建一個隱藏的表單來提交匯出請求
            var form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("sales.export") }}';
            
            // 添加所有篩選條件
            var filters = {
                'after_date': '{{ $request->after_date }}',
                'before_date': '{{ $request->before_date }}',
                'type_list': '{{ $request->type_list }}',
                'sale_on': '{{ $request->sale_on }}',
                'cust_name': '{{ $request->cust_name }}',
                'pet_name': '{{ $request->pet_name }}',
                'user': '{{ $request->user }}',
                'plan': '{{ $request->plan }}',
                'pay_id': '{{ $request->pay_id }}',
                'other': '{{ $request->other }}',
                'status': '{{ $request->status }}',
                'check_user_id': '{{ $request->check_user_id }}'
            };
            
            // 添加預設欄位
            var defaultFields = [
                '案件單類別', '單號', '專員', '日期', '客戶', '寶貝名', 
                '類別', '原方案', '套裝', '金紙', '金紙總賣價', 
                '安葬方式', '後續處理', '其他處理', '付款方式', 
                '實收價格', '狀態', '備註', '更改後方案', 
                '確認對帳人員', '確認對帳時間'
            ];
            
            // 添加篩選條件
            for (var key in filters) {
                if (filters[key]) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = filters[key];
                    form.appendChild(input);
                }
            }
            
            // 添加預設欄位
            defaultFields.forEach(function(field) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'export_fields[]';
                input.value = field;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
        
        // 關閉 Modal 的函數
        function closeExportModal() {
            if (typeof $ !== 'undefined') {
                $('#exportModal').modal('hide');
            } else {
                var modal = document.getElementById('exportModal');
                if (modal) {
                    var bootstrapModal = bootstrap.Modal.getInstance(modal);
                    if (bootstrapModal) {
                        bootstrapModal.hide();
                    }
                }
            }
        }
        
        // 確保頁面載入完成後初始化
        document.addEventListener('DOMContentLoaded', function() {
            // 檢查 Bootstrap Modal 是否可用
            if (typeof bootstrap === 'undefined') {
                console.log('Bootstrap Modal 不可用，將使用備用匯出功能');
            }
            
            // 添加錯誤處理
            window.addEventListener('error', function(e) {
                console.log('JavaScript 錯誤:', e.error);
                if (e.error && e.error.message && e.error.message.includes('bootstrap')) {
                    console.log('Bootstrap 相關錯誤，將使用備用匯出功能');
                }
            });
        });
    </script>
@endsection

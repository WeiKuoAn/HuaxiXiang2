@extends('layouts.vertical', ['page_title' => '平安燈列表'])

@section('content')
@section('css')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection
<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Huaxixiang</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">平安燈管理</a></li>
                        <li class="breadcrumb-item active">平安燈列表</li>
                    </ol>
                </div>
                <h4 class="page-title">平安燈列表</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <form class="d-flex flex-wrap align-items-center" action="{{ route('lamps') }}" method="GET">
                            <div class="me-1 col-1">
                                <label for="start_date_start" class="form-label">起始日期</label>
                                <input type="text" class="form-control date change_cal_date" id="start_date_start"
                                    name="start_date_start" value="{{ $request->start_date_start }}">
                            </div>
                            <div class="me-2 col-1">
                                <label for="start_date" class="form-label ">&nbsp;</label>
                                <input type="text" class="form-control date change_cal_date" id="start_date_end"
                                    name="start_date_end" value="{{ $request->start_date_end }}">
                            </div>
                            <div class="me-2 col-1">
                                <label for="end_date_start" class="form-label">結束日期</label>
                                <input type="text" class="form-control date change_cal_date" id="end_date_start"
                                    name="end_date_start" value="{{ $request->end_date_start }}">
                            </div>
                            <div class="me-2 col-1">
                                <label for="end_date_end" class="form-label">&nbsp;</label>
                                <input type="text" class="form-control date change_cal_date" id="end_date_end"
                                    name="end_date_end" value="{{ $request->end_date_end }}">
                            </div>
                            <div class="me-2 col-1">
                                <label for="before_date" class="form-label">顧客姓名</label>
                                <input type="search" class="form-control my-1 my-lg-0" id="cust_name" name="cust_name"
                                    value="{{ $request->cust_name }}">
                            </div>
                            <div class="me-2 col-1">
                                <label for="before_date" class="form-label">寶貝姓名</label>
                                <input type="search" class="form-control my-1 my-lg-0" id="pet_name" name="pet_name"
                                    value="{{ $request->pet_name }}">
                            </div>
                            <div class="me-sm-2">
                                <label class="form-label">平安燈類別</label>
                                <select class="form-select my-1 my-lg-0" id="status-select" name="type"
                                    onchange="this.form.submit()">
                                    <option value="null" selected>請選擇...</option>
                                    @foreach ($lamp_types as $lamp_type)
                                        <option value="{{ $lamp_type->id }}"
                                            @if ($request->type == $lamp_type->id) selected @endif>{{ $lamp_type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="me-sm-2">
                                <label class="form-label">續約</label>
                                <select class="form-select my-1 my-lg-0" id="status-select" name="check_renew"
                                    onchange="this.form.submit()">
                                    <option value="" selected>請選擇</option>
                                    <option value="1" @if ($request->check_renew == '1') selected @endif>是</option>
                                    <option value="0" @if ($request->check_renew == '0') selected @endif>否</option>
                                </select>
                            </div>
                            <div class="me-sm-2">
                                <label class="form-label">狀態</label>
                                <select class="form-select my-1 my-lg-0" id="status-select" name="check_close"
                                    onchange="this.form.submit()">
                                    <option value="1" @if ($request->check_close == '1' || !isset($request->check_close)) selected @endif>未結案
                                    </option>
                                    <option value="0" @if ($request->check_close == '0') selected @endif>已結案
                                    </option>
                                </select>
                            </div>
                            <div class="me-2 mt-3">
                                <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i
                                        class="fe-search me-1"></i>搜尋</button>
                            </div>
                            <div class="me-2 mt-3">
                                <a href="{{ route('lamp.export', request()->input()) }}">
                                    <button type="button" class="btn btn-primary waves-effect waves-light me-1"><i
                                            class="fe-download me-1"></i>匯出</button>
                                </a>
                            </div>
                            <div class="col-auto" style="margin-top: 28px;">
                                <div class="text-lg-end my-1 my-lg-0">
                                    {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                                    <a href="{{ route('lamp.create') }}">
                                        <button type="button" class="btn btn-danger waves-effect waves-light"
                                            data-bs-toggle="modal" data-bs-target="#custom-modal"><i
                                                class="mdi mdi-plus-circle me-1"></i>新增平安燈</button>
                                    </a>
                                </div>
                            </div><!-- end col-->
                        </form>

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
                                    <th>編號</th>
                                    <th>平安燈類別</th>
                                    <th>顧客名稱</th>
                                    <th>電話</th>
                                    <th>寶貝名稱</th>
                                    <th>目前簽約年份</th>
                                    <th>開始日期</th>
                                    <th>結束日期</th>
                                    <th>金額</th>
                                    <th>續約</th>
                                    <th>動作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datas as $key => $data)
                                    <tr>
                                        <td>{{ $data->number }}</td>
                                        <td>
                                            <span
                                                @if ($data->type == '1') class=" bg-soft-success text-success p-1" 
                                            @elseif($data->type == '2') class=" bg-soft-danger text-danger p-1"
                                            @elseif($data->type == '4') class=" bg-soft-warning text-warning p-1"
                                            @else class=" bg-soft-blue text-blue p-1" @endif>
                                                {{ $data->type_data->name }}
                                            </span>
                                        </td>
                                        <td>{{ $data->cust_name->name }}</td>
                                        <td>{{ $data->mobile }}</td>
                                        <td>{{ $data->pet_name }}</td>
                                        <td>第{{ $data->year }}年</td>
                                        <td>{{ $data->getRocStartDateAttribute() }}</td>
                                        @if (!isset($request->check_close) || $request->check_close == '1')
                                            <td>{{ $data->getRocEndDateAttribute() }}</td>
                                        @else
                                            <td>{{ $data->getRocCloseDateAttribute() }}</td>
                                        @endif
                                        <td>{{ number_format($data->price) }}</td>
                                        <td>
                                            @if ($data->renew == '1')
                                                是（{{ $data->renew_year }}年）
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
                                                        href="{{ route('lamp.edit', $data->id) }}"><i
                                                            class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯</a>
                                                    @if (Auth::user()->level != 2)
                                                        <a class="dropdown-item"
                                                            href="{{ route('lamp.del', $data->id) }}"><i
                                                                class="mdi mdi-delete me-2 font-18 text-muted vertical-middle"></i>刪除</a>
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



                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>
    <!-- end row -->

</div> <!-- container -->
@endsection
@section('script')
<script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
<script src="{{ asset('assets/libs/mohithg-switchery/mohithg-switchery.min.js') }}"></script>
<script src="{{ asset('assets/libs/multiselect/multiselect.min.js') }}"></script>
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-mockjax/jquery-mockjax.min.js') }}"></script>
<script src="{{ asset('assets/libs/devbridge-autocomplete/devbridge-autocomplete.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

{{-- <script src="{{asset('assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js')}}"></script> --}}
<script type="text/javascript">
    $(document).ready(function() {
        $('input.date').datepicker({
            dateFormat: 'yy/mm/dd' // Set the date format
        });

        $(".change_cal_date").on("change keyup", function() {
            let inputValue = $(this).val(); // Get the input date value
            let formattedDate = convertToROC(inputValue); // Convert the date format
            $(this).val(formattedDate); // Update the input field value
        });

        function convertToROC(dateString) {
            dateString = dateString.replace(/[^0-9]/g, ''); // Remove non-numeric characters
            if (dateString.length === 8) {
                // Format is YYYYMMDD
                let year = parseInt(dateString.substr(0, 4)) - 1911;
                let month = dateString.substr(4, 2);
                let day = dateString.substr(6, 2);
                return `${year}/${month}/${day}`;
            } else if (dateString.length === 7) {
                // Format is YYYMMDD assuming it's already ROC year
                let year = parseInt(dateString.substr(0, 3));
                let month = dateString.substr(3, 2);
                let day = dateString.substr(5, 2);
                return `${year}/${month}/${day}`;
            }
            return dateString; // Return original string if it does not match expected lengths
        }
    });
</script>
@endsection

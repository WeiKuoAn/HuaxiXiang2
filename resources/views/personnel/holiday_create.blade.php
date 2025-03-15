@extends('layouts.vertical', ["page_title"=> "新增專員每月休假"])

@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
<!-- third party css end -->
@endsection

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">人事管理</a></li>
                        <li class="breadcrumb-item active">新增專員每月休假</li>
                    </ol>
                </div>
                <h4 class="page-title">新增專員每月休假</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <form action="{{ route('personnel.holidays.create.data') }}" method="POST">
        @csrf
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">年度/月份</h5>
                    
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="mb-3">
                                <div class="mb-3">
                                   <label class="form-label">年度<span class="text-danger">*</span></label>
                                   <input type="text" class="form-control" name="year" value="{{ $year }}" required>
                               </div>
                           </div>
                            <div class="mb-3">
                                <label for="project-priority" class="form-label">月份<span class="text-danger">*</span></label>
                                <select class="form-control" data-toggle="select" data-width="100%" name="month">
                                    @foreach($months as $key=>$month)
                                        <option value="{{ $key }}" @if($key == $this_month) selected @endif>{{ $month['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> <!-- end col-->
                    </div>
                    <!-- end row -->
                </div> <!-- end card-body -->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    
                    <div class="row">
                        <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">員工資訊</h5>
                            <div class="table-responsive">
                                <table id="tech-companies-1" class="table user-list">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>姓名<span class="text-danger">*</span></th>
                                            <th>天數<span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $j = 0; @endphp
                                        @for ($i = 0; $i < 1; $i++)
                                            @php $j = $i+1; @endphp
                                            <tr id="row-{{ $i }}">
                                                <td class="text-center">
                                                    @if($j==1)
                                                    <button type="button" class="ibtnAdd_user demo-delete-row btn btn-primary btn-sm btn-icon"><i class="fa fas fa-plus"></i></button>                                                    
                                                    @else
                                                    <button type="button" class="ibtnDel_user demo-delete-row btn btn-danger btn-sm btn-icon"><i class="fa fa-times"></i></button>
                                                    @endif
                                                </td>
                                                <td>
                                                    <select id="select_user_{{$i}}" alt="{{ $i }}" class="mobile form-select" name="users[]" onchange="chgItems(this)" required>
                                                        <option value="" selected>請選擇</option>
                                                        @foreach($users as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="mobile form-control total_number" id="prom_total_{{$i}}" name="holiday[]"required >
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div> <!-- end .table-responsive -->
                        </div> <!-- end col-->
                    </div>
                    <!-- end row -->
                </div> <!-- end card-body -->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>

    <!-- end row-->
    <div class="row mt-3">
        <div class="col-6 text-center">
            <button type="submit" class="btn btn-success waves-effect waves-light m-1"><i class="fe-check-circle me-1"></i>新增</button>
            <button type="reset" class="btn btn-secondary waves-effect waves-light m-1" onclick="history.go(-1)"><i class="fe-x me-1"></i>回上一頁</button>
        </div>
    </div>
</div> <!-- container -->
</form>

@endsection

@section('script')
<!-- third party js -->
<script>
    $(document).ready(function(){
        $("table.user-list tbody").on("click", ".ibtnDel_user", function() {
            $(this).closest('tr').remove();
        });

        $("table.user-list tbody").on("click", ".ibtnAdd_user", function() {
            rowCount = $('table.user-list tr').length - 1;
            var newRow = $("<tr>");
            var cols = '';
            cols += '<td class="text-center"><button type="button" class="ibtnDel_user demo-delete-row btn btn-danger btn-sm btn-icon"><i class="fa fa-times"></i></button></td>';
            cols += '<td>';
            cols += '<select id="gdpaper_id_'+rowCount+'" alt="'+rowCount+'" class="mobile form-select" name="users[]" required>';
            cols += '<option value="" selected>請選擇...</option>';
                @foreach($users as $user)
                    cols += '<option value="{{ $user->id }}">{{ $user->name }}</option>';
                @endforeach
            cols += '</select>';
            cols += '</td>';
            cols += '<td>';
            cols += '<input type="text" class="mobile form-control total_number" id="gdpaper_total_'+rowCount+'" name="holiday[]" required>';
            cols += '</td>';
            cols += '</tr>';
            newRow.append(cols);
            $("table.user-list tbody").append(newRow);
        });
        $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
    });
</script>
<script src="{{ asset('assets/js/twzipcode-1.4.1-min.js') }}"></script>
<script src="{{ asset('assets/js/twzipcode.js') }}"></script>
<script src="{{asset('assets/libs/dropzone/dropzone.min.js')}}"></script>
<script src="{{asset('assets/libs/select2/select2.min.js')}}"></script>
<script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>
<!-- third party js ends -->

<!-- demo app -->
<script src="{{asset('assets/js/pages/create-project.init.js')}}"></script>
<!-- end demo js-->


@endsection
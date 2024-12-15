@extends('layouts.vertical', ["page_title"=> "新增平安燈"])

@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/customization.css') }}" id="app-style" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">平安燈管理</a></li>
                        <li class="breadcrumb-item active">新增平安燈</li>
                    </ol>
                </div>
                <h4 class="page-title">新增平安燈</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('lamp.create.data') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="mb-3">
                                <div class="mb-3">
                                   <label class="form-label">類別名稱<span class="text-danger">*</span></label>
                                   <select class="form-control" data-toggle="select" data-width="100%" name="type" required>
                                   <option value="" selected>請選擇</option>
                                        @foreach($lamp_types as $lamp_type)
                                                <option value="{{ $lamp_type->id }}">{{ $lamp_type->name }}</option>
                                        @endforeach
                                   </select>
                               </div>
                           </div>
                           <div class="mb-3">
                                <label for="number" class="form-label">位置編號<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="number" name="number"  required>
                           </div>
                           <div class="mb-3">
                            <label for="customer_id" class="form-label">客戶名稱<span class="text-danger">*</span></label>
                            <select class="form-control" data-toggle="select2" data-width="100%" name="cust_name_q" id="cust_name_q" required>
                                <option value="">請選擇...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">No.{{ $customer->id }} {{ $customer->name }}（{{ $customer->mobile }}）</option>
                                @endforeach
                            </select>
                        </div>
                           <div class="mb-3">
                                <label for="mobile" class="form-label">客戶電話<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="mobile" name="mobile"  required>
                           </div>
                           <div class="mb-3">
                                <label for="pet_name" class="form-label">寶貝名稱<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pet_name" name="pet_name"  required>
                           </div>
                           <div class="mb-3">
                                <label for="year" class="form-label">第幾年<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="year" name="year"  required>
                           </div>
                           <div class="mb-3">
                                <label for="start_date" class="form-label ">開始日期<span class="text-danger">*</span></label>
                                <input type="text" class="date form-control change_cal_date" id="start_date" name="start_date"  required>
                           </div>
                           <div class="mb-3">
                                <label for="end_date" class="form-label ">結束日期<span class="text-danger">*</span></label>
                                <input type="text" class="date form-control change_cal_date" id="end_date" name="end_date"  required>
                           </div>
                           <div class="mb-3">
                                <label for="price" class="form-label">金額<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="price" name="price"  required>
                           </div>
                           <div id="renew_div">
                                <div class="mb-3">
                                    <label for="renew_year" class="form-label">再續約幾年<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="renew_year" name="renew_year" >
                                </div>
                            </div>
                           <div class="mb-3 mt-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="renew" name="renew" value="0">
                                    <label class="form-check-label" for="renew"><b>是否為續約？</b></label>
                                </div>
                            </div>
                            <div>
                                <label class="form-label">備註</label>
                                <textarea class="form-control" rows="3" placeholder="" name="comment"></textarea>
                            </div>
                        </div> <!-- end col-->
                    </div>
                    <!-- end row -->


                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-success waves-effect waves-light m-1"><i class="fe-check-circle me-1"></i>新增</button>
                            <button type="reset" class="btn btn-secondary waves-effect waves-light m-1" onclick="history.go(-1)"><i class="fe-x me-1"></i>回上一頁</button>
                        </div>
                    </div>
                  </form>
                </div> <!-- end card-body -->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>
    <!-- end row-->

</div> <!-- container -->
@endsection

@section('script')
<!-- third party js -->
<script src="{{asset('assets/libs/selectize/selectize.min.js')}}"></script>
<script src="{{asset('assets/libs/mohithg-switchery/mohithg-switchery.min.js')}}"></script>
<script src="{{asset('assets/libs/multiselect/multiselect.min.js')}}"></script>
<script src="{{asset('assets/libs/select2/select2.min.js')}}"></script>
<script src="{{asset('assets/libs/jquery-mockjax/jquery-mockjax.min.js')}}"></script>
<script src="{{asset('assets/libs/devbridge-autocomplete/devbridge-autocomplete.min.js')}}"></script>
<!-- third party js ends -->
<script src="{{asset('assets/js/pages/form-advanced.init.js')}}"></script>
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
    $("#renew_div").hide();
    $('#renew').change(function() {
        if ($(this).is(':checked')) {
            $(this).val(1);
            $("#renew_div").show(300);
            $("#renew_year").prop('required', true);
        } else {
            $(this).val(0);
            $("#renew_div").hide(300);
            $("#renew_year").prop('required', false);
        }
    });

    $( "#cust_name_q" ).change(function() {
        $value=$(this).val();
        console.log($value);
        $.ajax({
            type : 'get',
            url : '{{ route('customer.search') }}',
            data:{'cust_name':$value},
            success:function(data){
                $('#cust_name_list_q').html(data);
                $cust_id=$("#cust_name_q").val();
                console.log($cust_id);
                $.ajax({
                    type : 'get',
                    url : '{{ route('customer.data') }}',
                    data:{'cust_id':$cust_id},
                    success:function(data){
                        console.log(data);
                        $('#mobile').val(data['mobile']);
                    }
                });
            }
        });
        // console.log($value);
    });

    $('input.date').datepicker({
            dateFormat: 'yy/mm/dd' // Set the date format
        });

    $('#start_date').change(function() {
        var startDate = new Date($(this).val());
        startDate.setFullYear(startDate.getFullYear() + 1);
        startDate.setDate(startDate.getDate() - 1);

        var endYear = startDate.getFullYear();
        var endMonth = ("0" + (startDate.getMonth() + 1)).slice(-2); // JavaScript months are 0-indexed
        var endDate = ("0" + startDate.getDate()).slice(-2);

        var endDateString = `${endYear}-${endMonth}-${endDate}`;
        let endDateStringformattedDate = convertToROC(endDateString);
        $('#end_date').val(endDateStringformattedDate);
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
</script>
<!-- end demo js-->
@endsection
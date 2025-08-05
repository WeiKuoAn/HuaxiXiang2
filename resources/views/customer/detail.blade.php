@extends('layouts.vertical', ["page_title"=> "Create Project"])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">客戶管理</a></li>
                        <li class="breadcrumb-item active">查看客戶資料</li>
                    </ol>
                </div>
                <h4 class="page-title">查看客戶資料</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="mb-3">
                                <label for="project-priority" class="form-label">群組<span class="text-danger">*</span></label>

                                <select class="form-control" data-toggle="select" data-width="100%" name="group_id" readonly>
                                    @foreach($groups as $group)
                                    <option value="{{ $group->id }}" @if( $customer->group_id == $group->id ) selected @endif>{{$group->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                 <div class="mb-3">
                                    <label class="form-label">姓名<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name"  value="{{ $customer->name }}" readonly>
                                </div>
                            </div>

                            <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">聯絡電話</h5>
                            <div class="row">
                                <label class="form-label">電話<span class="text-danger">*</span></label>
                                <div id="phone-container">
                                    @if(isset($customer->mobiles) && count($customer->mobiles) > 0)
                                        @foreach ($customer->mobiles as $i => $mobile)
                                            <div class="phone-item mb-3">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="text-muted">電話 #{{ $i + 1 }}</span>
                                                            @if($mobile->is_primary)
                                                                <span class="badge bg-primary">主要電話</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <input type="text" class="form-control" value="{{ $mobile->mobile }}" readonly>
                                                    </div>
                                                </div>
                                                <hr class="mt-3 mb-0" style="border-color: #e9ecef; opacity: 0.5;">
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="phone-item mb-3">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="text-muted">電話 #1</span>
                                                        <span class="badge bg-primary">主要電話</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <input type="text" class="form-control" value="{{ $customer->mobile }}" readonly>
                                                </div>
                                            </div>
                                            <hr class="mt-3 mb-0" style="border-color: #e9ecef; opacity: 0.5;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">地址</h5>
                           <div class="row">
                                <label class="form-label">地址<span class="text-danger">*</span></label>
                                <div id="address-container">
                                    @if(isset($customer->addresses) && count($customer->addresses))
                                        @foreach ($customer->addresses as $i => $addr)
                                            <div class="address-item mb-3">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="text-muted">地址 #{{ $i + 1 }}</span>
                                                            @if($addr->is_primary)
                                                                <span class="badge bg-primary">主要地址</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div id="twzipcode-{{ $i + 1 }}" >
                                                            <div data-role="county" data-value="{{ $addr->county }}"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-12">
                                                        <input type="text" class="form-control" value="{{ $addr->address }}" readonly>
                                                    </div>
                                                </div>
                                                <hr class="mt-3 mb-0" style="border-color: #e9ecef; opacity: 0.5;">
                                            </div>
                                        @endforeach
                                    @elseif (isset($customer->address) && $customer->address)
                                        <div class="address-item mb-3">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="text-muted">地址 #1</span>
                                                        <span class="badge bg-primary">主要地址</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div id="twzipcode-1" >
                                                        <div data-role="county" data-value="{{ $customer->county }}"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-12">
                                                    <input type="text" class="form-control" value="{{ $customer->address }}" readonly>
                                                </div>
                                            </div>
                                            <hr class="mt-3 mb-0" style="border-color: #e9ecef; opacity: 0.5;">
                                        </div>
                                    @else
                                        <div class="address-item mb-3">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="text-muted">地址 #1</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div id="twzipcode-1" >
                                                        <div data-role="county" data-value=""></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-12">
                                                    <input type="text" class="form-control" value="未提供地址" readonly>
                                                </div>
                                            </div>
                                            <hr class="mt-3 mb-0" style="border-color: #e9ecef; opacity: 0.5;">
                                        </div>
                                    @endif
                                </div>
                           </div>

                           <div class="row">
                            <label class="form-label">舊地址<span class="text-danger">*</span></label>
                            <div class="mb-3 mt-1">
                                <input type="text" class="form-control" name="old-address" placeholder="輸入地址" value="{{ $customer->address }}" readonly>
                            </div>
                       </div>

                           
                        </div> <!-- end col-->
                        
                    </div>
                    <!-- end row -->


                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <button type="reset" class="btn btn-secondary waves-effect waves-light m-1" onclick="history.go(-1)"><i class="fe-x me-1"></i>回上一頁</button>
                        </div>
                    </div>
                </div> <!-- end card-body -->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>
    <!-- end row-->

</div> <!-- container -->
@endsection

@section('script')
<!-- third party js -->


<script src="{{ asset('assets/js/twzipcode-1.4.1-min.js') }}"></script>
<script src="{{ asset('assets/js/twzipcode.js') }}"></script>
<script src="{{asset('assets/libs/dropzone/dropzone.min.js')}}"></script>
<script src="{{asset('assets/libs/select2/select2.min.js')}}"></script>
<script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>
<script>
    $(document).ready(function(){
        // 初始化所有地址的郵遞區號選擇器
        $(".address-item").each(function(index) {
            const addressNumber = index + 1;
            const twzipcodeId = `twzipcode-${addressNumber}`;
            
            if ($(this).find(`#${twzipcodeId}`).length > 0) {
                const countyValue = $(this).find('[data-role="county"]').attr('data-value');
                const districtValue = $(this).find('[data-role="district"]').attr('data-value');
                
                $(`#${twzipcodeId}`).twzipcode({
                    zipcodeIntoDistrict: true,
                    css: [" form-control", "mt-1 form-control", "mt-1 form-control"],
                    countyName: "county",
                    districtName: "district",
                    countySel: countyValue || '',
                    districtSel: districtValue || '',
                });
            }
        });
    });
</script>
<!-- third party js ends -->

<!-- demo app -->
<script src="{{asset('assets/js/pages/create-project.init.js')}}"></script>
<!-- end demo js-->
@endsection
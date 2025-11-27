@extends('layouts.vertical', ['page_title' => 'Create Project'])

@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
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
                            <li class="breadcrumb-item active">編輯客戶</li>
                        </ol>
                    </div>
                    <h4 class="page-title">編輯客戶</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        @if (isset($hint) && $hint == '1')
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                @if (isset($error_message))
                                    {{ $error_message }}
                                @else
                                    電話號碼已被使用
                                @endif
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        <form action="{{ route('customer.edit.data', $customer->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <label for="project-priority" class="form-label">群組<span
                                                class="text-danger">*</span></label>

                                        <select class="form-control" data-toggle="select" data-width="100%" name="group_id">
                                            @foreach ($groups as $group)
                                                <option value="{{ $group->id }}"
                                                    @if ($customer->group_id == $group->id) selected @endif>{{ $group->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">姓名<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name"
                                                value="{{ $customer->name }}" required>
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
                                                                    <button type="button" class="btn btn-sm btn-outline-danger remove-phone"
                                                                        @if ($i == 0) style="display:none;" @endif>
                                                                        <i class="fe-trash-2"></i> 移除
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <input type="text" class="form-control" name="mobiles[]" 
                                                                    value="{{ $mobile->mobile }}" placeholder="輸入電話號碼" required>
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
                                                                <button type="button" class="btn btn-sm btn-outline-danger remove-phone" style="display: none;">
                                                                    <i class="fe-trash-2"></i> 移除
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <input type="text" class="form-control" name="mobiles[]" 
                                                                value="{{ $customer->mobile }}" placeholder="輸入電話號碼" required>
                                                        </div>
                                                    </div>
                                                    <hr class="mt-3 mb-0" style="border-color: #e9ecef; opacity: 0.5;">
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mb-3 text-end">
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-phone">
                                                <i class="fe-plus"></i> 新增電話
                                            </button>
                                        </div>
                                    </div>
                                    <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">地址</h5>
                                    <div class="row">
                                        <label class="form-label">地址<span class="text-danger">*</span></label>
                                        <div id="address-container">
                                            @if(isset($customer->addresses) && count($customer->addresses))
                                                @foreach ($customer->addresses as $i => $addr)
                                                    <div class="address-item mb-3" data-county="{{ $addr->county ?? '' }}" data-district="{{ $addr->district ?? '' }}">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                                    <span class="text-muted">地址 #{{ $i + 1 }}</span>
                                                                    <button type="button" class="btn btn-sm btn-outline-danger remove-address"
                                                                        @if ($i == 0) style="display:none;" @endif>
                                                                        <i class="fe-trash-2"></i> 移除
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div id="twzipcode-{{ $i + 1 }}"></div>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-1">
                                                            <div class="col-12">
                                                                <input type="text" class="form-control" name="addresses[]" 
                                                                    value="{{ $addr->address }}" placeholder="輸入地址">
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
                                                                <button type="button" class="btn btn-sm btn-outline-danger remove-address" style="display: none;">
                                                                    <i class="fe-trash-2"></i> 移除
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div id="twzipcode-1"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-1">
                                                        <div class="col-12">
                                                            <input type="text" class="form-control" name="addresses[]"
                                                                value="{{ $customer->address }}" placeholder="輸入地址">
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
                                                                <button type="button" class="btn btn-sm btn-outline-danger remove-address" style="display: none;">
                                                                    <i class="fe-trash-2"></i> 移除
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div id="twzipcode-1"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-1">
                                                        <div class="col-12">
                                                            <input type="text" class="form-control" name="addresses[]" placeholder="輸入地址">
                                                        </div>
                                                    </div>
                                                    <hr class="mt-3 mb-0" style="border-color: #e9ecef; opacity: 0.5;">
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                id="add-address">
                                                <i class="fe-plus"></i> 新增地址
                                            </button>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <label class="form-label">備註</label>
                                        <div class="mb-3 mt-1">
                                            <textarea class="form-control" rows="3" placeholder="" name="comment">{{ $customer->comment }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="blacklist" name="blacklist" value="1" 
                                                    @if($customer->blacklist == 1) checked @endif>
                                                <label class="form-check-label" for="blacklist">
                                                    <span style="color: #dc3545; font-weight: bold;">🚫 黑名單</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end col-->

                            </div>
                            <!-- end row -->


                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success waves-effect waves-light m-1"><i
                                            class="fe-check-circle me-1"></i>修改</button>
                                    <button type="reset" class="btn btn-secondary waves-effect waves-light m-1"
                                        onclick="history.go(-1)"><i class="fe-x me-1"></i>回上一頁</button>
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


    <script src="{{ asset('assets/js/twzipcode-1.4.1-min.js') }}"></script>
    <script src="{{ asset('assets/js/twzipcode.js') }}"></script>
    <script src="{{ asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // 初始化所有現有地址的郵遞區號選擇器
            $(".address-item").each(function(index) {
                const addressNumber = index + 1;
                const twzipcodeId = `twzipcode-${addressNumber}`;
                
                if ($(this).find(`#${twzipcodeId}`).length > 0) {
                    // 從地址資料中取得縣市和區
                    const county = $(this).data('county') || '{{ $customer->county ?? "" }}';
                    const district = $(this).data('district') || '{{ $customer->district ?? "" }}';
                    
                    $(`#${twzipcodeId}`).twzipcode({
                        css: [" form-control", "mt-1 form-control", "mt-1 form-control"],
                        countyName: "county[]",
                        districtName: "district[]",
                        countySel: county,
                        districtSel: district,
                    });
                }
            });

            // 新增電話功能
            $("#add-phone").click(function() {
                const phoneCount = $(".phone-item").length + 1;
                const newPhoneHtml = `
                    <div class="phone-item mb-3">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">電話 #${phoneCount}</span>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-phone">
                                        <i class="fe-trash-2"></i> 移除
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <input type="text" class="form-control" name="mobiles[]" placeholder="輸入電話號碼" required>
                            </div>
                        </div>
                        <hr class="mt-3 mb-0" style="border-color: #e9ecef; opacity: 0.5;">
                    </div>
                `;
                
                $("#phone-container").append(newPhoneHtml);
                
                // 更新所有電話的編號
                updatePhoneNumbers();
            });

            // 移除電話功能
            $(document).on("click", ".remove-phone", function() {
                $(this).closest(".phone-item").remove();
                updatePhoneNumbers();
                
                // 如果只剩一個電話，隱藏移除按鈕
                if ($(".phone-item").length === 1) {
                    $(".remove-phone").hide();
                }
            });

            // 更新電話編號
            function updatePhoneNumbers() {
                $(".phone-item").each(function(index) {
                    const phoneNumber = index + 1;
                    $(this).find(".text-muted").text(`電話 #${phoneNumber}`);
                });
            }

            // 初始化時隱藏第一個電話的移除按鈕
            if ($(".phone-item").length === 1) {
                $(".remove-phone").hide();
            }

            // 新增地址功能
            $("#add-address").click(function() {
                const addressCount = $(".address-item").length + 1;
                const newAddressHtml = `
                    <div class="address-item mb-3">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">地址 #${addressCount}</span>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-address">
                                        <i class="fe-trash-2"></i> 移除
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div id="twzipcode-${addressCount}"></div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-12">
                                <input type="text" class="form-control" name="addresses[]" placeholder="輸入地址">
                            </div>
                        </div>
                        <hr class="mt-3 mb-0" style="border-color: #e9ecef; opacity: 0.5;">
                    </div>
                `;
                
                $("#address-container").append(newAddressHtml);
                
                // 初始化新地址的郵遞區號選擇器
                $(`#twzipcode-${addressCount}`).twzipcode({
                    css: [" form-control", "mt-1 form-control", "mt-1 form-control"],
                    countyName: "county[]",
                    districtName: "district[]",
                });
                
                // 更新所有地址的編號
                updateAddressNumbers();
            });

            // 移除地址功能
            $(document).on("click", ".remove-address", function() {
                $(this).closest(".address-item").remove();
                updateAddressNumbers();
                
                // 如果只剩一個地址，隱藏移除按鈕
                if ($(".address-item").length === 1) {
                    $(".remove-address").hide();
                }
            });

            // 更新地址編號
            function updateAddressNumbers() {
                $(".address-item").each(function(index) {
                    const addressNumber = index + 1;
                    $(this).find(".text-muted").text(`地址 #${addressNumber}`);
                    
                    // 更新郵遞區號選擇器的 ID
                    const oldId = $(this).find("[id^='twzipcode-']").attr("id");
                    const newId = `twzipcode-${addressNumber}`;
                    if (oldId !== newId) {
                        $(this).find("[id^='twzipcode-']").attr("id", newId);
                    }
                });
            }

            // 初始化時隱藏第一個地址的移除按鈕
            if ($(".address-item").length === 1) {
                $(".remove-address").hide();
            }
        });
    </script>
    <!-- third party js ends -->

    <!-- demo app -->
    <script src="{{ asset('assets/js/pages/create-project.init.js') }}"></script>
    <!-- end demo js-->
@endsection

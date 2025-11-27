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
                            <li class="breadcrumb-item active">新增客戶</li>
                        </ol>
                    </div>
                    <h4 class="page-title">新增客戶</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12 col-xl-6">
                <div class="card">
                    <div class="card-body">
                        @if ($hint == '1')
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                客戶已存在
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        <form action="{{ route('customer.create.data') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-xl-12">
                                    {{-- <div class="mb-3">
                                <label for="project-priority" class="form-label">群組<span class="text-danger">*</span></label>
                                <select class="form-control" data-toggle="select" data-width="100%" name="group_id">
                                    @foreach ($groups as $group)
                                    <option value="{{ $group->id }}">{{$group->name}}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">姓名<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" required>
                                        </div>
                                    </div>
                                    <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">聯絡電話</h5>
                                    <div class="row">
                                        <label class="form-label">電話<span class="text-danger">*</span></label>
                                        <div id="phone-container">
                                            <div class="phone-item mb-3">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="text-muted">電話 #1</span>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger remove-phone"
                                                                style="display: none;">
                                                                <i class="fe-trash-2"></i> 移除
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <input type="text" class="form-control" name="mobiles[]"
                                                            placeholder="輸入電話號碼" required>
                                                    </div>
                                                </div>
                                                <hr class="mt-3 mb-0" style="border-color: #e9ecef; opacity: 0.5;">
                                            </div>
                                        </div>
                                        <div class="mb-3   text-end">
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-phone">
                                                <i class="fe-plus"></i> 新增電話
                                            </button>
                                        </div>
                                    </div>

                                    <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">地址</h5>
                                    <div class="row">
                                        <div id="address-container">
                                            <div class="address-item mb-3">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="text-muted">地址 #1</span>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger remove-address"
                                                                style="display: none;">
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
                                                            placeholder="輸入地址">
                                                    </div>
                                                </div>
                                                <hr class="mt-3 mb-0" style="border-color: #e9ecef; opacity: 0.5;">
                                            </div>
                                        </div>
                                        <div class="mb-3 text-end">
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-address">
                                                <i class="fe-plus"></i> 新增地址
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="form-label">備註</label>
                                        <div class="mb-3 mt-1">
                                            <textarea class="form-control" rows="3" placeholder="" name="comment"></textarea>
                                        </div>
                                    </div>


                                    <div class="mb-1 mt-1">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="not_mobile"
                                                name="not_mobile">
                                            <label class="form-check-label" for="not_mobile"><b>未提供電話</b></label>
                                        </div>
                                    </div>
                                    <div class="mb-1 mt-1">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="not_address"
                                                name="not_address">
                                            <label class="form-check-label" for="not_address"><b>（親送）未提供地址</b></label>
                                        </div>
                                    </div>
                                </div> <!-- end col-->
                            </div>
                            <!-- end row -->


                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success waves-effect waves-light m-1"><i
                                            class="fe-check-circle me-1"></i>建立</button>
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
    <script>
        $('#not_mobile').change(function() {
            if ($(this).is(':checked')) {
                $(this).val(1);
                $("input[name='mobiles[]']").prop('required', false);
            } else {
                $(this).val(0);
                $("input[name='mobiles[]']").prop('required', true);
            }
        });
        $('#not_address').change(function() {
            if ($(this).is(':checked')) {
                $(this).val(1);
            } else {
                $(this).val(0);
            }
        });
        $(document).ready(function() {
            // 初始化第一個地址的郵遞區號選擇器
            $("#twzipcode-1").twzipcode({
                css: [" form-control", "mt-1 form-control", "mt-1 form-control"],
                countyName: "county[]",
                districtName: "district[]",
            });

            // 新增地址
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

            // 移除地址
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

            // 新增電話
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

            // 移除電話
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
        });
    </script>

    <script src="{{ asset('assets/js/twzipcode-1.4.1-min.js') }}"></script>
    <script src="{{ asset('assets/js/twzipcode.js') }}"></script>
    <script src="{{ asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <!-- third party js ends -->

    <!-- demo app -->
    <script src="{{ asset('assets/js/pages/create-project.init.js') }}"></script>
    <!-- end demo js-->
@endsection

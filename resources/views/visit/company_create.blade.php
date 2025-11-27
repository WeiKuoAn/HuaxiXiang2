@extends('layouts.vertical', ['page_title' => '新增合作公司'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">拜訪管理</a></li>
                            <li class="breadcrumb-item active">新增合作公司</li>
                        </ol>
                    </div>
                    <h4 class="page-title">新增合作公司</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        @if ($hint == '1')
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                客戶已存在
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        <form action="{{ route('visit.company.create') }}" method="POST">
                            @csrf
                            <div class="row">
                                <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">基本資訊</h5>
                                <div class="col-xl-12">
                                    {{-- <div class="mb-3">
                                <label for="project-priority" class="form-label">群組<span class="text-danger">*</span></label>
                                <select class="form-control" data-toggle="select" data-width="100%" name="group_id">
                                    @foreach ($groups as $group)
                                    <option value="{{ $group->id }}">{{$group->name}}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                                    <input type="hidden" class="form-control" name="company_type"
                                        value="{{ $company_type }}">
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
                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-phone" style="display: none;">
                                                                <i class="fe-trash-2"></i> 移除
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <input type="text" class="form-control" name="mobiles[]" id="mobile" placeholder="輸入電話號碼" required>
                                                    </div>
                                                </div>
                                                <hr class="mt-3 mb-0" style="border-color: #e9ecef; opacity: 0.5;">
                                            </div>
                                        </div>
                                        <div class="mb-3 text-end">
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-phone">
                                                <i class="fe-plus"></i> 新增電話
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="form-label">地址<span class="text-danger">*</span></label>
                                        <div id="twzipcode"></div>
                                        <div class="mb-3 mt-1">
                                            <input type="text" class="form-control" name="address" placeholder="輸入地址"
                                                required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <!-- Date View -->
                                            <div class="mb-3">
                                                <label for="bank">匯款帳戶</label>
                                                <select id="bank" name="bank" class="form-control bank-field"
                                                    data-toggle="select2" data-width="100%" onchange="updateBranches()">
                                                    <option value="">請選擇銀行</option>
                                                    @foreach ($groupedBanks as $bankCode => $branches)
                                                        <option value="{{ $bankCode }}">
                                                            {{ $branches->first()['金融機構名稱'] }}
                                                            ({{ $bankCode }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>

                                        <div class="col-lg-3">
                                            <div class="mb-3">
                                                <div class="form-group">
                                                    <label for="branch">選擇分行</label>
                                                    <select id="branch" name="branch" class="form-control bank-field"
                                                        data-toggle="select2" data-width="100%">
                                                        <option value="">請選擇分行</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <!-- Date View -->
                                            <div class="mb-3">
                                                <label for="bank_number">帳戶號碼</label>
                                                <input type="text" class="form-control bank-field" id="bank_number" name="bank_number"
                                                    value="">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="mb-1 mt-1">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="not_provide_bank"
                                                name="not_provide_bank" value="1">
                                            <label class="form-check-label" for="not_provide_bank"><b>不提供帳戶</b></label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="form-label">備註</label>
                                        <div class="mb-3">
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


                                </div> <!-- end col-->

                            </div>
                            <!-- end row -->



                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col-->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">其他設定</h5>
                            <div class="col-xl-12">
                                <div class="mb-3">
                                    <label for="project-priority" class="form-label">是否有佣金<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" data-toggle="select" data-width="100%"
                                        name="commission">
                                        <option value="1">有</option>
                                        <option value="0" selected>無</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="project-priority" class="form-label">是否拜訪過<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" data-toggle="select" data-width="100%"
                                        name="visit_status">
                                        <option value="1">有</option>
                                        <option value="0" selected>無</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="project-priority" class="form-label">是否簽約過<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" data-toggle="select" data-width="100%"
                                        name="contract_status">
                                        <option value="1">有</option>
                                        <option value="0" selected>無</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="project-priority" class="form-label">指派人員<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" data-toggle="select" data-width="100%"
                                        name="assigned_to">
                                        <option value="null">無須指派</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- end row -->
                        </div> <!-- end card-body -->

                    </div> <!-- end card-->
                </div>

                <!-- end row-->

            </div> <!-- container -->
        </div>
        <div class="row mt-3">
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-success waves-effect waves-light m-1"><i
                        class="fe-check-circle me-1"></i>建立</button>
                <button type="reset" class="btn btn-secondary waves-effect waves-light m-1" onclick="history.go(-1)"><i
                        class="fe-x me-1"></i>回上一頁</button>
            </div>
        </div>
        </form>
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

        // 電話新增/移除功能
        $(document).ready(function() {
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

        // 不提供帳戶的邏輯
        $('#not_provide_bank').change(function() {
            if ($(this).is(':checked')) {
                // 勾選「不提供帳戶」時，禁用並清空所有帳戶欄位
                $('.bank-field').prop('disabled', true).val('').trigger('change');
                // 使用 hidden input 來確保值被提交（即使欄位被 disabled）
                if ($('#bank_hidden').length === 0) {
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'bank_hidden',
                        name: 'bank',
                        value: ''
                    }).appendTo('form');
                }
                if ($('#branch_hidden').length === 0) {
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'branch_hidden',
                        name: 'branch',
                        value: ''
                    }).appendTo('form');
                }
                if ($('#bank_number_hidden').length === 0) {
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'bank_number_hidden',
                        name: 'bank_number',
                        value: ''
                    }).appendTo('form');
                }
                $('#bank').select2('destroy').select2({ width: '100%', disabled: true });
                $('#branch').select2('destroy').select2({ width: '100%', disabled: true });
            } else {
                // 取消勾選時，啟用帳戶欄位並移除 hidden input
                $('.bank-field').prop('disabled', false);
                $('#bank_hidden, #branch_hidden, #bank_number_hidden').remove();
                $('#bank').select2({ width: '100%' });
                $('#branch').select2({ width: '100%' });
            }
        });

        // 當填寫任何帳戶欄位時，取消勾選「不提供帳戶」
        $('.bank-field').on('change input', function() {
            var bank = $('#bank').val();
            var branch = $('#branch').val();
            var bankNumber = $('#bank_number').val();
            
            // 如果任何欄位有值，取消勾選「不提供帳戶」
            if (bank || branch || bankNumber) {
                $('#not_provide_bank').prop('checked', false);
                $('.bank-field').prop('disabled', false);
            }
        });

        // 表單提交驗證
        $('form').on('submit', function(e) {
            var notProvideBank = $('#not_provide_bank').is(':checked');
            var bank = $('#bank').val();
            var branch = $('#branch').val();
            var bankNumber = $('#bank_number').val();
            
            // 檢查：要麼勾選「不提供帳戶」，要麼三個欄位都填寫
            if (!notProvideBank) {
                if (!bank || !branch || !bankNumber) {
                    e.preventDefault();
                    alert('請完整填寫匯款帳戶（銀行、分行、帳戶號碼），或勾選「不提供帳戶」');
                    return false;
                }
            } else {
                // 如果勾選「不提供帳戶」，確保欄位都是空的
                if (bank || branch || bankNumber) {
                    e.preventDefault();
                    alert('已勾選「不提供帳戶」，請清空所有帳戶欄位');
                    return false;
                }
            }
        });

        $(document).ready(function() {
            $("#twzipcode").twzipcode({
                css: [" form-control", "mt-1 form-control", "mt-1 form-control"], // 自訂 "城市"、"地區" class 名稱 
                countyName: "county", // 自訂城市 select 標籤的 name 值
                districtName: "district", // 自訂地區 select 標籤的 name 值
            });
        });
    </script>
    <script>
        function updateBranches() {
            const bankCode = document.getElementById('bank').value;
            const branchSelect = document.getElementById('branch');

            // 清空舊的分行選項
            branchSelect.innerHTML = '<option value="">載入中...</option>';

            if (bankCode) {
                fetch(`/api/banks/${bankCode}/branches`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log(data);
                        branchSelect.innerHTML = '<option value="">請選擇分行</option>';

                        // 確認數據格式
                        if (Array.isArray(data)) {
                            data.forEach(branch => {
                                const option = document.createElement('option');
                                option.value = branch['分支機構代號'];
                                option.textContent = `${branch['分支機構名稱']} (${branch['分支機構代號']})`;
                                branchSelect.appendChild(option);
                            });
                        } else {
                            console.error('Data format error:', data);
                            branchSelect.innerHTML = '<option value="">數據格式錯誤</option>';
                        }
                    })
                    .catch((error) => {
                        console.error('Fetch error:', error);
                        branchSelect.innerHTML = '<option value="">載入失敗</option>';
                    });
            } else {
                branchSelect.innerHTML = '<option value="">請先選擇銀行</option>';
            }
        }
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

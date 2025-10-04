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

                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">電話<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="mobile" id="mobile"
                                                required>
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
                                                <select id="bank" name="bank" class="form-control"
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
                                                    <select id="branch" name="branch" class="form-control"
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
                                                <input type="text" class="form-control" name="bank_number"
                                                    value="">
                                            </div>
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
                $("#mobile").prop('required', false);
            } else {
                $(this).val(0);
                $("#mobile").prop('required', true);
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

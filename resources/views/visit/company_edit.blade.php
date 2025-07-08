@extends('layouts.vertical', ['page_title' => '編輯合作公司'])

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
                            <li class="breadcrumb-item active">編輯合作公司</li>
                        </ol>
                    </div>
                    <h4 class="page-title">編輯合作公司</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('visit.company.edit.data', $data->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">基本資訊</h5>
                                <input type="hidden" class="form-control" name="company_type" value="{{ $company_type }}">
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <label for="project-priority" class="form-label">群組<span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" data-toggle="select" data-width="100%" name="group_id">
                                            @foreach ($groups as $group)
                                                <option value="{{ $group->id }}"
                                                    @if ($data->group_id == $group->id) selected @endif>{{ $group->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">姓名<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name"
                                                value="{{ $data->name }}" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">電話<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="mobile"
                                                value="{{ $data->mobile }}" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="form-label">地址<span class="text-danger">*</span></label>
                                        <div id="twzipcode">
                                            <div data-role="county" data-value="{{ $data->county }}"></div>
                                        </div>
                                        <div class="mb-3 mt-1">
                                            <input type="text" class="form-control" name="address" placeholder="輸入地址"
                                                value="{{ $data->address }}" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <!-- Date View -->
                                            <div class="mb-3">
                                                <label for="bank">薪資帳戶</label>
                                                <select id="bank" name="bank" class="form-control"
                                                    onchange="updateBranches()">
                                                    <option value="">請選擇銀行</option>
                                                    @foreach ($groupedBanks as $bankCode => $branches)
                                                        <option value="{{ $bankCode }}"
                                                            @if ($data->bank == $bankCode) selected @endif>
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
                                                    <select id="branch" name="branch" class="form-control">
                                                        @if ($data->bank)
                                                            @foreach ($groupedBanks[$data->bank] as $branch)
                                                                <option value="{{ $branch['分支機構代號'] }}"
                                                                    @if ($data->branch == $branch['分支機構代號']) selected @endif>
                                                                    {{ $branch['分支機構名稱'] }}
                                                                </option>
                                                            @endforeach
                                                        @else
                                                            <option value="">請先選擇銀行</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <!-- Date View -->
                                            <div class="mb-3">
                                                <label for="bank_number">帳戶號碼</label>
                                                <input type="text" class="form-control" name="bank_number"
                                                    value="{{ $data->bank_number }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="row">
                                            <label class="form-label">舊地址<span class="text-danger">*</span></label>
                                            <div class="mb-3 mt-1">
                                                <input type="text" class="form-control" name="old-address"
                                                    placeholder="輸入地址" value="{{ $data->address }}">
                                            </div>
                                        </div>
                                    </div> <!-- end col-->
                                    <div class="row">
                                        <label class="form-label">備註</label>
                                        <div class="mb-3">
                                            <textarea class="form-control" rows="3" placeholder="" name="comment">{{ $data->comment }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <!-- end row -->
                            </div> <!-- end card-body -->
                    </div> <!-- end card-->
                </div> <!-- end col-->
            </div>
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
                                        <option value="1" @if ($data->commission == '1') selected @endif>有
                                        </option>
                                        <option value="0" @if ($data->commission == '0' || $data->commission == null) selected @endif>無
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="project-priority" class="form-label">是否拜訪過<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" data-toggle="select" data-width="100%"
                                        name="visit_status">
                                        <option value="1" @if ($data->visit_status == '1') selected @endif>有
                                        </option>
                                        <option value="0" @if ($data->visit_status == '0' || $data->visit_status == null) selected @endif>無
                                        </option>
                                    </select>
                                </div>

                            </div>
                            <!-- end row -->
                        </div> <!-- end card-body -->

                    </div> <!-- end card-->
                </div>

                <!-- end row-->

            </div> <!-- container -->
            <div class="row mt-3">
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-success waves-effect waves-light m-1"><i
                            class="fe-check-circle me-1"></i>修改</button>
                    <button type="reset" class="btn btn-secondary waves-effect waves-light m-1"
                        onclick="history.go(-1)"><i class="fe-x me-1"></i>回上一頁</button>
                </div>
            </div>
            </form>
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
                    $("#twzipcode").twzipcode({
                        zipcodeIntoDistrict: true,
                        css: [" form-control", "mt-1 form-control", "mt-1 form-control"], // 自訂 "城市"、"地區" class 名稱 
                        countyName: "county", // 自訂城市 select 標籤的 name 值
                        districtName: "district", // 自訂地區 select 標籤的 name 值
                        countySel: '{{ $data->county }}',
                        districtSel: '{{ $data->district }}',
                    });
                });
            </script>
            <!-- third party js ends -->

            <!-- demo app -->
            <script src="{{ asset('assets/js/pages/create-project.init.js') }}"></script>
            <!-- end demo js-->
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
        @endsection

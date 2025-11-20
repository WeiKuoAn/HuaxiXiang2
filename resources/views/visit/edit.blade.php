@extends('layouts.vertical', ['page_title' => '編輯拜訪紀錄'])

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
                            <li class="breadcrumb-item active">編輯拜訪紀錄</li>
                        </ol>
                    </div>
                    <h4 class="page-title">編輯拜訪紀錄</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('visit.edit.data', [$customer->id, $data->id]) }}" method="POST"
                            id="visitForm" onsubmit="return validateForm(event)">
                            @csrf
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">客戶名稱<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name"
                                                value="{{ $customer->name }}" required>
                                            <input type="hidden" class="form-control" name="customer_id"
                                                value="{{ $customer->id }}" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">拜訪日期<span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="date"
                                                value="{{ $data->date }}" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">拜訪類別<span class="text-danger">*</span></label>
                                            <select class="form-select" id="visit_type" name="visit_type" required
                                                onchange="toggleVisitTypeFields()">
                                                <option value="">請選擇拜訪類別</option>
                                                <option value="visit" {{ $data->visit_type == 'visit' ? 'selected' : '' }}>
                                                    拜訪</option>
                                                <option value="supply" {{ $data->visit_type == 'supply' ? 'selected' : '' }}>
                                                    補給</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- 拜訪類別欄位 -->
                                    <div id="visit_fields" style="{{ $data->visit_type == 'supply' ? 'display: none;' : '' }}">
                                        <div class="mb-3">
                                            <div class="mb-3">
                                                <label class="form-label">拜訪紀錄<span class="text-danger">*</span></label>
                                                <textarea class="form-control" rows="5" placeholder="" name="comment"
                                                    id="visit_comment">{{ $data->visit_type == 'visit' ? $data->comment : '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="mb-3">
                                                <label class="form-label">拜訪專員<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="visit_user_name_display" value="{{ Auth::user()->name }}" readonly>
                                                <input type="hidden" name="user_id" id="visit_user_id" value="{{ Auth::user()->id }}">
                                                <small class="text-muted">拜訪專員固定為當前登入者</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 補給類別欄位 -->
                                    <div id="supply_fields"
                                        style="{{ $data->visit_type == 'supply' ? '' : 'display: none;' }}">
                                        <div class="mb-3">
                                            <label class="form-label">需要補充的項目：<span class="text-danger">*</span></label>

                                            @php
                                                $supplementItems = $data->supplement_items ?? [];
                                                $largeQty = $supplementItems['大箱'] ?? 0;
                                                $smallQty = $supplementItems['小箱'] ?? 0;
                                                $miniQty = $supplementItems['迷你箱'] ?? 0;
                                                $dmQty = $supplementItems['DM'] ?? 0;
                                            @endphp

                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <span class="input-group-text">大箱</span>
                                                        <input type="number" class="form-control supplement-quantity"
                                                            id="supplement_box_large_qty" name="supplement_quantities[大箱]"
                                                            min="0" value="{{ $largeQty }}" placeholder="數量">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <span class="input-group-text">小箱</span>
                                                        <input type="number" class="form-control supplement-quantity"
                                                            id="supplement_box_small_qty" name="supplement_quantities[小箱]"
                                                            min="0" value="{{ $smallQty }}" placeholder="數量">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <span class="input-group-text">迷你箱</span>
                                                        <input type="number" class="form-control supplement-quantity"
                                                            id="supplement_box_mini_qty" name="supplement_quantities[迷你箱]"
                                                            min="0" value="{{ $miniQty }}" placeholder="數量">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <span class="input-group-text">DM</span>
                                                        <input type="number" class="form-control supplement-quantity"
                                                            id="supplement_dm_qty" name="supplement_quantities[DM]"
                                                            min="0" value="{{ $dmQty }}" placeholder="數量">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">誰補<span class="text-danger">*</span></label>
                                            <select class="form-select" id="supplement_by" name="supplement_by">
                                                <option value="">請選擇人員</option>
                                                @if (isset($users))
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}"
                                                            {{ ($data->visit_type == 'supply' && $data->supplement_by == $user->id) ? 'selected' : '' }}>
                                                            {{ $user->name }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option value="{{ Auth::user()->id }}">{{ Auth::user()->name }}
                                                    </option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                </div> <!-- end col-->

                            </div>
                            <!-- end row -->


                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success waves-effect waves-light m-1"><i
                                            class="fe-check-circle me-1"></i>更新</button>
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
    <!-- third party js ends -->

    <!-- demo app -->
    <script src="{{ asset('assets/js/pages/create-project.init.js') }}"></script>
    <!-- end demo js-->

    <script>
        // 當前登入者資訊（從 Blade 傳遞）
        const currentUserId = {{ Auth::user()->id }};
        const currentUserName = '{{ Auth::user()->name }}';

        function toggleVisitTypeFields() {
            const visitType = document.getElementById('visit_type').value;
            const visitFields = document.getElementById('visit_fields');
            const supplyFields = document.getElementById('supply_fields');
            const visitComment = document.getElementById('visit_comment');
            const visitUserId = document.getElementById('visit_user_id');
            const visitUserNameDisplay = document.getElementById('visit_user_name_display');
            const supplementBy = document.getElementById('supplement_by');

            if (visitType === 'visit') {
                // 顯示拜訪欄位
                visitFields.style.display = 'block';
                visitComment.required = true;
                
                // 拜訪專員固定為當前登入者（唯讀）
                if (visitUserId) {
                    visitUserId.value = currentUserId;
                }
                if (visitUserNameDisplay) {
                    visitUserNameDisplay.value = currentUserName;
                }

                // 隱藏補給欄位
                supplyFields.style.display = 'none';
                supplementBy.required = false;
                // 清空補給欄位的數量
                document.querySelectorAll('.supplement-quantity').forEach(function(input) {
                    if (parseInt(input.value) === 0) {
                        input.value = 0;
                    }
                });
            } else if (visitType === 'supply') {
                // 顯示補給欄位
                supplyFields.style.display = 'block';
                supplementBy.required = true;

                // 隱藏拜訪欄位
                visitFields.style.display = 'none';
                visitComment.required = false;
                if (visitUserId) {
                    visitUserId.value = '';
                }
                visitComment.value = '';
            } else {
                // 未選擇時隱藏所有欄位
                visitFields.style.display = 'none';
                supplyFields.style.display = 'none';
                visitComment.required = false;
                if (visitUserId) {
                    visitUserId.required = false;
                }
                supplementBy.required = false;
            }
        }

        // 驗證表單函數
        function validateForm(event) {
            const visitType = document.getElementById('visit_type');
            if (!visitType) return true;

            const visitTypeValue = visitType.value;
            if (visitTypeValue === 'supply') {
                const quantities = document.querySelectorAll('.supplement-quantity');
                let hasQuantity = false;
                let totalQuantity = 0;

                quantities.forEach(function(input) {
                    const qty = parseInt(input.value) || 0;
                    totalQuantity += qty;
                    if (qty > 0) {
                        hasQuantity = true;
                    }
                });

                if (!hasQuantity || totalQuantity === 0) {
                    event.preventDefault();
                    event.stopPropagation();

                    alert('請至少輸入一個需要補充的項目數量！所有數量不能都是 0！');

                    // 高亮顯示所有數量輸入欄位
                    quantities.forEach(function(input) {
                        input.style.borderColor = '#dc3545';
                        input.style.backgroundColor = '#fff5f5';
                        input.classList.add('is-invalid');

                        // 移除高亮（當用戶開始輸入時）
                        const removeHighlight = function() {
                            this.style.borderColor = '';
                            this.style.backgroundColor = '';
                            this.classList.remove('is-invalid');
                        };
                        input.addEventListener('input', removeHighlight, {
                            once: true
                        });
                        input.addEventListener('change', removeHighlight, {
                            once: true
                        });
                    });

                    // 滾動到第一個數量輸入欄位
                    if (quantities.length > 0) {
                        quantities[0].scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        setTimeout(function() {
                            quantities[0].focus();
                        }, 100);
                    }

                    return false;
                }
            }
            return true;
        }

        // 頁面載入時初始化
        document.addEventListener('DOMContentLoaded', function() {
            toggleVisitTypeFields();
        });
    </script>
@endsection

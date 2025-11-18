@extends('layouts.vertical', ['page_title' => '新增懲戒'])

@section('css')
    <style>
        .date-row {
            border: 1px solid #dee2e6;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            background-color: #f8f9fa;
        }
    </style>
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
                            <li class="breadcrumb-item"><a href="{{ route('disciplines.index') }}">懲戒管理</a></li>
                            <li class="breadcrumb-item active">新增懲戒</li>
                        </ol>
                    </div>
                    <h4 class="page-title">新增懲戒</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('disciplines.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="user_id" class="form-label">受懲處人 <span
                                                class="text-danger">*</span></label>
                                        <select name="user_id" id="user_id" class="form-select" required>
                                            <option value="">請選擇受懲處人</option>
                                            @foreach ($punish_users as $punish_user)
                                                <option value="{{ $punish_user->id }}"
                                                    {{ old('user_id') == $punish_user->id ? 'selected' : '' }}>
                                                    {{ $punish_user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="severity" class="form-label">嚴重性 <span
                                                class="text-danger">*</span></label>
                                        <select name="severity" id="severity" class="form-select" required>
                                            <option value="">請選擇嚴重性</option>
                                            <option value="輕度" {{ old('severity') == '輕度' ? 'selected' : '' }}>輕度
                                            </option>
                                            <option value="中度" {{ old('severity') == '中度' ? 'selected' : '' }}>中度
                                            </option>
                                            <option value="重度" {{ old('severity') == '重度' ? 'selected' : '' }}>重度
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="amount_type" class="form-label">懲處金額 <span
                                                class="text-danger">*</span></label>
                                        <select name="amount_type" id="amount_type" class="form-select" required>
                                            <option value="">請選擇</option>
                                            <option value="月獎" {{ old('amount_type') == '月獎' ? 'selected' : '' }}>月獎
                                            </option>
                                            <option value="季獎" {{ old('amount_type') == '季獎' ? 'selected' : '' }}>季獎
                                            </option>
                                            <option value="月＋季獎" {{ old('amount_type') == '月＋季獎' ? 'selected' : '' }}>
                                                月＋季獎</option>
                                            <option value="其他" {{ old('amount_type') == '其他' ? 'selected' : '' }}>其他
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6" id="amount_input_container" style="display: none;">
                                    <div class="mb-3">
                                        <label for="amount" class="form-label">金額 <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="amount" id="amount" class="form-control"
                                            value="{{ old('amount', 0) }}" min="0" step="1">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="reason" class="form-label">事由 <span class="text-danger">*</span></label>
                                <textarea name="reason" id="reason" class="form-control" rows="4" required>{{ old('reason') }}</textarea>
                            </div>



                            <div class="mb-3">
                                <label class="form-label">發生日期 <span class="text-danger">*</span></label>
                                <div id="dates-container">
                                    <div class="date-row">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <label class="form-label">日期</label>
                                                <input type="date" name="incident_dates[]" class="form-control"
                                                    value="{{ old('incident_dates.0') }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">備註</label>
                                                <input type="text" name="incident_notes[]" class="form-control"
                                                    value="{{ old('incident_notes.0') }}" placeholder="選填">
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger btn-sm remove-date-btn"
                                                    style="display: none;">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success btn-sm mt-2" id="add-date-btn">
                                    <i class="mdi mdi-plus"></i> 新增日期
                                </button>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">審核人員（決議） <span class="text-danger">*</span></label>
                                <div class="row">
                                    @foreach ($approver_users as $approver_user)
                                        <div class="col-md-3">
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="approver_ids[]" value="{{ $approver_user->id }}"
                                                    class="form-check-input" id="approver_{{ $approver_user->id }}"
                                                    {{ is_array(old('approver_ids')) && in_array($approver_user->id, old('approver_ids')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="approver_{{ $approver_user->id }}">
                                                    {{ $approver_user->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted">至少選擇一位審核人員</small>
                            </div>

                            <div class="mb-3">
                                <label for="resolution" class="form-label">決議內容</label>
                                <textarea name="resolution" id="resolution" class="form-control" rows="3">{{ old('resolution') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="meeting_reviewed" id="meeting_reviewed"
                                        class="form-check-input" value="1"
                                        {{ old('meeting_reviewed') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="meeting_reviewed">
                                        有無經過會議審核
                                    </label>
                                </div>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('disciplines.index') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left"></i> 返回
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-content-save"></i> 儲存
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- container -->

@endsection

@section('script')
    <script>
        $(document).ready(function() {
            let dateIndex = 1;

            // 新增日期
            $('#add-date-btn').click(function() {
                const newDateRow = `
                <div class="date-row">
                    <div class="row">
                        <div class="col-md-5">
                            <label class="form-label">日期</label>
                            <input type="date" name="incident_dates[]" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">備註</label>
                            <input type="text" name="incident_notes[]" class="form-control" placeholder="選填">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm remove-date-btn">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
                $('#dates-container').append(newDateRow);
                dateIndex++;
                updateRemoveButtons();
            });

            // 刪除日期
            $(document).on('click', '.remove-date-btn', function() {
                $(this).closest('.date-row').remove();
                updateRemoveButtons();
            });

            // 更新刪除按鈕顯示
            function updateRemoveButtons() {
                const dateRows = $('.date-row');
                if (dateRows.length > 1) {
                    $('.remove-date-btn').show();
                } else {
                    $('.remove-date-btn').hide();
                }
            }

            updateRemoveButtons();

            // 處理懲處金額類型選擇
            function handleAmountTypeChange() {
                const amountType = $('#amount_type').val();
                const amountInputContainer = $('#amount_input_container');
                const amountInput = $('#amount');

                if (amountType === '其他') {
                    amountInputContainer.show();
                    amountInput.prop('required', true);
                } else {
                    amountInputContainer.hide();
                    amountInput.prop('required', false);
                    amountInput.val(0);
                }
            }

            // 監聽下拉選單變化
            $('#amount_type').on('change', handleAmountTypeChange);

            // 初始化顯示狀態
            handleAmountTypeChange();
        });
    </script>
@endsection

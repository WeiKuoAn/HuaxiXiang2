@extends('layouts.vertical', ['page_title' => '新增加成'])

@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- third party css end -->
    <style>
        .category-section {
            border: 1px solid #e3eaef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
        }

        .category-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }

        .person-row {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
        }

        .overtime-row {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
        }

        .role-checkboxes {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            margin-top: 5px;
        }

        .category-checkboxes {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            margin-top: 5px;
            height: 38px;
            display: flex;
            align-items: center;
        }

        .form-field-wrapper {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .form-field-wrapper .form-label {
            margin-bottom: 5px;
        }

        .form-field-wrapper .form-control,
        .form-field-wrapper .category-checkboxes {
            flex: 1;
            margin-top: 0;
        }

        .remove-btn {
            color: #dc3545;
            cursor: pointer;
        }

        .add-btn {
            color: #28a745;
            cursor: pointer;
        }

        /* 確保夜間開爐時段選擇為三欄布局 */
        #time-slot-section-0:not([style*="display: none"]) {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: flex-start !important;
        }
        
        #time-slot-section-0 .col-4 {
            flex: 0 0 33.333333% !important;
            max-width: 33.333333% !important;
            display: flex !important;
            flex-direction: column !important;
        }
        
        /* 修正 select2 在夜間開爐區段的樣式 */
        #time-slot-section-0 .select2-container {
            width: 100% !important;
            margin-top: 0 !important;
        }
        
        #time-slot-section-0 .select2-selection {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
        }
        
        #time-slot-section-0 .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
        }
        
        #time-slot-section-0 .select2-selection__arrow {
            height: 36px !important;
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">加成管理</a></li>
                            <li class="breadcrumb-item active">新增加成</li>
                        </ol>
                    </div>
                    <h4 class="page-title">新增加成</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('increase.create.data') }}" method="POST" id="increaseForm">
                            @csrf

                            <!-- 基本資訊 -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">加成日期<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="increase_date"
                                            value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">備註</label>
                                        <textarea class="form-control" name="comment" rows="3" placeholder="請輸入備註..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- 1. 加成類別區段（夜間、晚間、颱風） -->
                            <div class="category-section">
                                <h5 class="category-title">
                                    <i class="fe-award me-2"></i>加成類別（夜間、晚間、颱風）
                                </h5>
                                <div id="increase-container">
                                    <div class="person-row" data-index="0">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <!-- 類別選擇 -->
                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        <label class="form-label">適用類別</label>
                                                        <div class="category-checkboxes">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="increase[0][categories][]" value="night"
                                                                    id="night_0" onchange="calculateBonus(0)">
                                                                <label class="form-check-label" for="night_0">夜間加成</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="increase[0][categories][]" value="evening"
                                                                    id="evening_0" onchange="calculateBonus(0)">
                                                                <label class="form-check-label" for="evening_0">晚間加成</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="increase[0][categories][]" value="typhoon"
                                                                    id="typhoon_0" onchange="calculateBonus(0)">
                                                                <label class="form-check-label" for="typhoon_0">颱風加成</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- 人員選擇 -->
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label">接電話人員</label>
                                                        <select class="form-control" name="increase[0][phone_person]" data-toggle="select" onchange="calculateBonus(0)">
                                                            <option value="">請選擇人員</option>
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="mt-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="increase[0][phone_exclude_bonus]" value="1" id="phone_exclude_bonus_0" onchange="calculateBonus(0)">
                                                                <label class="form-check-label" for="phone_exclude_bonus_0">
                                                                    <small class="text-muted">不計入獎金</small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">接件人員</label>
                                                        <select class="form-control" name="increase[0][receive_person]" data-toggle="select">
                                                            <option value="">請選擇人員</option>
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">獎金計算</label>
                                                <div class="bonus-calculation" id="bonus-calculation-0">
                                                    <small class="text-muted">請選擇類別</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-person"
                                                    onclick="removePerson(this)">
                                                    <i class="fe-trash-2 me-1"></i>移除
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="addPerson()">
                                    <i class="fe-plus me-1"></i>新增加成人員
                                </button>
                            </div>

                            <!-- 2. 夜間開爐區段 -->
                            <div class="category-section">
                                <h5 class="category-title">
                                    <i class="fe-thermometer me-2"></i>夜間開爐
                                </h5>
                                <div id="furnace-container">
                                    <div class="person-row" data-furnace-index="0">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label">夜間開爐時段</label>
                                                <select class="form-control" name="furnace[0][time_slot_id]" id="furnace_time_slot_0" onchange="calculateFurnacePrice(0)">
                                                    <option value="">請選擇時段</option>
                                                    @foreach ($timeSlots as $timeSlot)
                                                        <option value="{{ $timeSlot->id }}" 
                                                                data-price="{{ $timeSlot->price }}">
                                                            {{ $timeSlot->full_description }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">負責人員</label>
                                                <select class="form-control" name="furnace[0][furnace_person]" data-toggle="select">
                                                    <option value="">請選擇人員</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">計算價格</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="text" class="form-control" id="furnace_calculated_price_0" readonly>
                                                </div>
                                                <small class="text-muted">根據時段自動計算</small>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-furnace"
                                                    onclick="removeFurnace(this)">
                                                    <i class="fe-trash-2 me-1"></i>移除
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="addFurnace()">
                                    <i class="fe-plus me-1"></i>新增夜間開爐
                                </button>
                            </div>

                            <!-- 3. 加班費區段 -->
                            <div class="category-section">
                                <h5 class="category-title">
                                    <i class="fe-clock me-2"></i>加班費
                                </h5>
                                <div id="overtime-container">
                                    <div class="person-row" data-overtime-index="0">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="form-label">加班記錄選擇</label>
                                                <div id="overtime-records-container-0">
                                                    <div class="alert alert-info">
                                                        <i class="fe-info me-2"></i>請選擇加成日期以載入該日期的加班記錄
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <div id="overtime_edit_section_0" style="display: none;">
                                                    <label class="form-label">加班詳細資料</label>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="row mb-2">
                                                                <input type="hidden" name="overtime[0][overtime_record]" id="overtime_record_field_0" value="">
                                                                <div class="col-md-4">
                                                                    <label class="form-label small">加班分鐘</label>
                                                                    <input type="number" class="form-control form-control-sm" 
                                                                           name="overtime[0][minutes]" 
                                                                           id="overtime_minutes_field_0" 
                                                                           min="1" step="1" 
                                                                           onchange="calculateOvertimePayFromMinutes(0)">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label small">事由</label>
                                                                    <input type="text" class="form-control form-control-sm" 
                                                                           name="overtime[0][reason]" 
                                                                           id="overtime_reason_field_0" 
                                                                           placeholder="請輸入加班事由">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label small">加班費金額</label>
                                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">$</span>
                                                                        <input type="number" class="form-control" 
                                                                               name="overtime[0][overtime_amount]" 
                                                                               id="overtime_amount_field_0" 
                                                                               min="0" step="1" 
                                                               onchange="updateOvertimeAmountDisplay(0, this.value)">
                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <small class="text-muted">可自行調整加班資料</small>
                                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                        onclick="saveOvertimeRecord(0)">
                                                                    <i class="fe-save me-1"></i>儲存變更
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-overtime"
                                                    onclick="removeOvertime(this)">
                                                    <i class="fe-trash-2 me-1"></i>移除
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="addOvertime()">
                                    <i class="fe-plus me-1"></i>新增加班費
                                </button>
                                    <button type="button" class="btn btn-sm btn-outline-info" onclick="toggleManualOvertimeForm()">
                                        <i class="fe-edit me-1"></i>手動新增加班記錄
                                </button>
                                </div>
                                
                                <!-- 手動新增加班記錄表單 -->
                                <div id="manual-overtime-form" style="display: none;" class="mt-3">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="fe-edit me-2"></i>手動新增加班記錄</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="form-label">加班人員<span class="text-danger">*</span></label>
                                                    <select class="form-control" id="manual_overtime_user" name="manual_overtime_user">
                                                        <option value="">請選擇人員</option>
                                                        @foreach ($users as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">加班分鐘<span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" id="manual_overtime_minutes" name="manual_overtime_minutes"
                                                           min="1" step="1" 
                                                           onchange="calculateManualOvertimeAmount()"
                                                           oninput="calculateManualOvertimeAmount()">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">事由</label>
                                                    <input type="text" class="form-control" id="manual_overtime_reason" name="manual_overtime_reason"
                                                           placeholder="請輸入加班事由">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">加班費金額</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control" id="manual_overtime_amount" name="manual_overtime_amount"
                                                               min="0" step="1" readonly>
                                                    </div>
                                                    <small class="text-muted">自動計算</small>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" class="btn btn-success me-2" onclick="saveManualOvertimeRecord()">
                                                        <i class="fe-save me-1"></i>儲存
                                                    </button>
                                                    <button type="button" class="btn btn-secondary" onclick="cancelManualOvertimeForm()">
                                                        <i class="fe-x me-1"></i>取消
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- 提交按鈕 -->
                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success waves-effect waves-light m-1">
                                        <i class="fe-check-circle me-1"></i>新增加成
                                    </button>
                                    <button type="reset" class="btn btn-secondary waves-effect waves-light m-1"
                                        onclick="history.go(-1)">
                                        <i class="fe-x me-1"></i>回上一頁
                                    </button>
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
        // 新增加成人員
        function addPerson() {
            const container = document.getElementById('increase-container');
            const existingRows = container.querySelectorAll('.person-row');
            const newIndex = existingRows.length;

            const newRow = document.createElement('div');
            newRow.className = 'person-row';
            newRow.setAttribute('data-index', newIndex);

            newRow.innerHTML = `
         <div class="row">
             <div class="col-md-8">
                 <!-- 類別選擇 -->
                 <div class="row mb-3">
                     <div class="col-md-12">
                         <label class="form-label">適用類別</label>
                         <div class="category-checkboxes">
                             <div class="form-check form-check-inline">
                                 <input class="form-check-input" type="checkbox" name="increase[${newIndex}][categories][]" value="night" id="night_${newIndex}" onchange="calculateBonus(${newIndex})">
                                 <label class="form-check-label" for="night_${newIndex}">夜間加成</label>
                             </div>
                             <div class="form-check form-check-inline">
                                 <input class="form-check-input" type="checkbox" name="increase[${newIndex}][categories][]" value="evening" id="evening_${newIndex}" onchange="calculateBonus(${newIndex})">
                                 <label class="form-check-label" for="evening_${newIndex}">晚間加成</label>
                             </div>
                             <div class="form-check form-check-inline">
                                 <input class="form-check-input" type="checkbox" name="increase[${newIndex}][categories][]" value="typhoon" id="typhoon_${newIndex}" onchange="calculateBonus(${newIndex})">
                                 <label class="form-check-label" for="typhoon_${newIndex}">颱風加成</label>
                             </div>
                         </div>
                     </div>
                 </div>
                 <!-- 人員選擇 -->
                 <div class="row">
                     <div class="col-md-6">
                         <label class="form-label">接電話人員</label>
                         <select class="form-control" name="increase[${newIndex}][phone_person]" data-toggle="select" onchange="calculateBonus(${newIndex})">
                             <option value="">請選擇人員</option>
                             @foreach ($users as $user)
                                 <option value="{{ $user->id }}">{{ $user->name }}</option>
                             @endforeach
                         </select>
                         <div class="mt-2">
                             <div class="form-check">
                                 <input class="form-check-input" type="checkbox" name="increase[${newIndex}][phone_exclude_bonus]" value="1" id="phone_exclude_bonus_${newIndex}" onchange="calculateBonus(${newIndex})">
                                 <label class="form-check-label" for="phone_exclude_bonus_${newIndex}">
                                     <small class="text-muted">不計入獎金</small>
                                 </label>
                             </div>
                         </div>
                     </div>
                     <div class="col-md-6">
                         <label class="form-label">接件人員</label>
                         <select class="form-control" name="increase[${newIndex}][receive_person]" data-toggle="select">
                             <option value="">請選擇人員</option>
                             @foreach ($users as $user)
                                 <option value="{{ $user->id }}">{{ $user->name }}</option>
                             @endforeach
                         </select>
                     </div>
                 </div>
             </div>
             <div class="col-md-4">
                 <label class="form-label">獎金計算</label>
                 <div class="bonus-calculation" id="bonus-calculation-${newIndex}">
                     <small class="text-muted">請選擇類別</small>
                 </div>
             </div>
         </div>
         <div class="row mt-2">
             <div class="col-md-12">
                 <button type="button" class="btn btn-sm btn-outline-danger remove-person" onclick="removePerson(this)">
                     <i class="fe-trash-2 me-1"></i>移除
                 </button>
             </div>
         </div>
    `;

            container.appendChild(newRow);

            // 重新初始化 select2
            $(newRow).find('select[data-toggle="select"]').select2();
        }

        // 新增夜間開爐
        function addFurnace() {
            const container = document.getElementById('furnace-container');
            const existingRows = container.querySelectorAll('.person-row');
            const newIndex = existingRows.length;

            const newRow = document.createElement('div');
            newRow.className = 'person-row';
            newRow.setAttribute('data-furnace-index', newIndex);

            newRow.innerHTML = `
         <div class="row">
             <div class="col-md-4">
                 <label class="form-label">夜間開爐時段</label>
                 <select class="form-control" name="furnace[${newIndex}][time_slot_id]" id="furnace_time_slot_${newIndex}" onchange="calculateFurnacePrice(${newIndex})">
                     <option value="">請選擇時段</option>
                     @foreach ($timeSlots as $timeSlot)
                         <option value="{{ $timeSlot->id }}" data-price="{{ $timeSlot->price }}">
                             {{ $timeSlot->full_description }}
                         </option>
                     @endforeach
                 </select>
             </div>
             <div class="col-md-4">
                 <label class="form-label">負責人員</label>
                 <select class="form-control" name="furnace[${newIndex}][furnace_person]" data-toggle="select">
                     <option value="">請選擇人員</option>
                     @foreach ($users as $user)
                         <option value="{{ $user->id }}">{{ $user->name }}</option>
                     @endforeach
                 </select>
             </div>
             <div class="col-md-4">
                 <label class="form-label">計算價格</label>
                 <div class="input-group">
                     <span class="input-group-text">$</span>
                     <input type="text" class="form-control" id="furnace_calculated_price_${newIndex}" readonly>
                 </div>
                 <small class="text-muted">根據時段自動計算</small>
             </div>
         </div>
         <div class="row mt-2">
             <div class="col-md-12">
                 <button type="button" class="btn btn-sm btn-outline-danger remove-furnace" onclick="removeFurnace(this)">
                     <i class="fe-trash-2 me-1"></i>移除
                 </button>
             </div>
         </div>
    `;

            container.appendChild(newRow);

            // 重新初始化 select2
            $(newRow).find('select[data-toggle="select"]').select2();
        }

        // 新增加班費
        function addOvertime() {
            const container = document.getElementById('overtime-container');
            const existingRows = container.querySelectorAll('.person-row');
            const newIndex = existingRows.length;

            const newRow = document.createElement('div');
            newRow.className = 'person-row';
            newRow.setAttribute('data-overtime-index', newIndex);

            newRow.innerHTML = `
         <div class="row">
             <div class="col-md-12">
                 <label class="form-label">加班記錄選擇</label>
                 <div id="overtime-records-container-${newIndex}">
                     <div class="alert alert-info">
                         <i class="fe-info me-2"></i>請選擇加成日期以載入該日期的加班記錄
                     </div>
                 </div>
             </div>
         </div>
         <div class="row mt-3">
             <div class="col-md-12">
                 <div id="overtime_edit_section_${newIndex}" style="display: none;">
                     <label class="form-label">加班詳細資料</label>
                     <div class="card">
                         <div class="card-body">
                             <div class="row mb-2">
                                 <input type="hidden" name="overtime[${newIndex}][overtime_record]" id="overtime_record_field_${newIndex}" value="">
                                 <div class="col-md-4">
                                     <label class="form-label small">加班分鐘</label>
                                     <input type="number" class="form-control form-control-sm" 
                                            name="overtime[${newIndex}][minutes]" 
                                            id="overtime_minutes_field_${newIndex}" 
                                            min="1" step="1" 
                                            onchange="calculateOvertimePayFromMinutes(${newIndex})">
                                 </div>
                                 <div class="col-md-4">
                                     <label class="form-label small">事由</label>
                                     <input type="text" class="form-control form-control-sm" 
                                            name="overtime[${newIndex}][reason]" 
                                            id="overtime_reason_field_${newIndex}" 
                                            placeholder="請輸入加班事由">
                                 </div>
                                 <div class="col-md-4">
                                     <label class="form-label small">加班費金額</label>
                                     <div class="input-group input-group-sm">
                         <span class="input-group-text">$</span>
                                         <input type="number" class="form-control" 
                                                name="overtime[${newIndex}][overtime_amount]" 
                                                id="overtime_amount_field_${newIndex}" 
                                                min="0" step="1" 
                                onchange="updateOvertimeAmountDisplay(${newIndex}, this.value)">
                     </div>
                                 </div>
                             </div>
                             <div class="d-flex justify-content-between align-items-center">
                                 <small class="text-muted">可自行調整加班資料</small>
                                 <button type="button" class="btn btn-sm btn-outline-primary" 
                                         onclick="saveOvertimeRecord(${newIndex})">
                                     <i class="fe-save me-1"></i>儲存變更
                                 </button>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
         <div class="row mt-2">
             <div class="col-md-12">
                 <button type="button" class="btn btn-sm btn-outline-danger remove-overtime" onclick="removeOvertime(this)">
                     <i class="fe-trash-2 me-1"></i>移除
                 </button>
             </div>
         </div>
    `;

            container.appendChild(newRow);
            
            // 載入加班記錄（延遲執行以確保 DOM 元素已建立）
            setTimeout(() => {
                loadOvertimeRecordsForIndex(newIndex);
            }, 100);
        }

        // 移除加成人員
        function removePerson(button) {
            const container = document.getElementById('increase-container');
            const rows = container.querySelectorAll('.person-row');

            if (rows.length > 1) {
                button.closest('.person-row').remove();
                // 重新編號
                container.querySelectorAll('.person-row').forEach((row, index) => {
                    row.setAttribute('data-index', index);
                    row.querySelectorAll('select, input').forEach(input => {
                        const name = input.getAttribute('name');
                        if (name) {
                            input.setAttribute('name', name.replace(/increase\[\d+\]/,
                                `increase[${index}]`));
                        }
                    });
                                    // 重新設定 checkbox 的 id 和 for
                row.querySelectorAll('input[type="checkbox"]').forEach(input => {
                    const oldId = input.id;
                    const newId = oldId.replace(/\d+$/, index);
                    input.id = newId;
                    const label = row.querySelector(`label[for="${oldId}"]`);
                    if (label) {
                        label.setAttribute('for', newId);
                    }
                });
                
                // 重新設定 onchange 事件
                row.querySelectorAll('input[type="checkbox"]').forEach(input => {
                    if (input.id.includes('night_furnace_')) {
                        input.setAttribute('onchange', `toggleTimeSlotSelection(${index}); calculateBonus(${index})`);
                    } else if (input.id.includes('overtime_')) {
                        input.setAttribute('onchange', `toggleOvertimeSection(${index}); calculateBonus(${index})`);
                    } else {
                        input.setAttribute('onchange', `calculateBonus(${index})`);
                    }
                });
                row.querySelectorAll('select').forEach(select => {
                    if (select.name.includes('phone_person')) {
                        select.setAttribute('onchange', `calculateBonus(${index})`);
                    }
                });
                });
            }
        }


        // 移除夜間開爐
        function removeFurnace(button) {
            const container = document.getElementById('furnace-container');
            const rows = container.querySelectorAll('.person-row');

            if (rows.length > 1) {
                button.closest('.person-row').remove();
                // 重新編號
                container.querySelectorAll('.person-row').forEach((row, index) => {
                    row.setAttribute('data-furnace-index', index);
                    row.querySelectorAll('select, input').forEach(input => {
                        const name = input.getAttribute('name');
                        if (name) {
                            input.setAttribute('name', name.replace(/furnace\[\d+\]/,
                                `furnace[${index}]`));
                        }
                    });
                });
            }
        }

        // 移除加班費
        function removeOvertime(button) {
            const container = document.getElementById('overtime-container');
            const rows = container.querySelectorAll('.person-row');

            if (rows.length > 1) {
                button.closest('.person-row').remove();
                // 重新編號
                container.querySelectorAll('.person-row').forEach((row, index) => {
                    row.setAttribute('data-overtime-index', index);
                    row.querySelectorAll('select, input').forEach(input => {
                        const name = input.getAttribute('name');
                        if (name) {
                            input.setAttribute('name', name.replace(/overtime\[\d+\]/,
                                `overtime[${index}]`));
                        }
                    });
                });
            }
        }

        // 計算夜間開爐價格
        function calculateFurnacePrice(index) {
            const timeSlotSelect = document.getElementById(`furnace_time_slot_${index}`);
            const priceInput = document.getElementById(`furnace_calculated_price_${index}`);

            if (timeSlotSelect && timeSlotSelect.value) {
                const selectedOption = timeSlotSelect.options[timeSlotSelect.selectedIndex];
                const price = selectedOption.dataset.price || 0;
                priceInput.value = price;
            } else {
                priceInput.value = '';
            }
        }

        // 計算加班費
        function calculateOvertimePay(input) {
            const minutes = parseInt(input.value) || 0;
            const row = input.closest('.overtime-row');
            const index = row.getAttribute('data-index');
            const calculationDiv = document.getElementById(`overtime-calculation-${index}`);

            if (minutes <= 0) {
                calculationDiv.innerHTML = '<small class="text-muted">請輸入加班分鐘</small>';
                return;
            }

            // 計算小時
            const totalHours = minutes / 60;
            let firstTwoHours = 0;
            let remainingHours = 0;

            if (totalHours <= 2) {
                firstTwoHours = totalHours;
            } else {
                firstTwoHours = 2;
                remainingHours = totalHours - 2;
            }

            // 格式化顯示
            let displayText = '';

            if (firstTwoHours > 0) {
                const firstTwoHoursFormatted = firstTwoHours === Math.floor(firstTwoHours) ?
                    Math.floor(firstTwoHours) + '小時' :
                    Math.floor(firstTwoHours) + '小時' + Math.round((firstTwoHours % 1) * 60) + '分鐘';
                displayText += `<div><span class="badge bg-primary">${firstTwoHoursFormatted} × 1.34</span></div>`;
            }

            if (remainingHours > 0) {
                const remainingHoursFormatted = remainingHours === Math.floor(remainingHours) ?
                    Math.floor(remainingHours) + '小時' :
                    Math.floor(remainingHours) + '小時' + Math.round((remainingHours % 1) * 60) + '分鐘';
                displayText += `<div><span class="badge bg-success">${remainingHoursFormatted} × 1.67</span></div>`;
            }

            calculationDiv.innerHTML = displayText;
        }

        // 計算獎金（只處理傳統加成：夜間、晚間、颱風）
        function calculateBonus(index) {
            const row = document.querySelector(`.person-row[data-index="${index}"]`);
            const calculationDiv = document.getElementById(`bonus-calculation-${index}`);

            // 獲取選中的類別
            const nightChecked = document.getElementById(`night_${index}`).checked;
            const eveningChecked = document.getElementById(`evening_${index}`).checked;
            const typhoonChecked = document.getElementById(`typhoon_${index}`).checked;

            if (!nightChecked && !eveningChecked && !typhoonChecked) {
                calculationDiv.innerHTML = '<small class="text-muted">請選擇類別</small>';
                return;
            }

            // 檢查接電話人員是否不計入獎金
            const phoneExcludeBonus = document.getElementById(`phone_exclude_bonus_${index}`).checked;

            let displayText = '';
            let totalPhoneBonus = 0;
            let totalReceiveBonus = 0;

            // 計算各類別獎金
            if (nightChecked) {
                if (!phoneExcludeBonus) {
                    totalPhoneBonus += 100;
                }
                totalReceiveBonus += 500;
                displayText += `<div><span class="badge bg-primary">夜間：電話$100、接件$500</span></div>`;
            }

            if (eveningChecked) {
                if (!phoneExcludeBonus) {
                    totalPhoneBonus += 50;
                }
                totalReceiveBonus += 250;
                displayText += `<div><span class="badge bg-success">晚間：電話$50、接件$250</span></div>`;
            }

            if (typhoonChecked) {
                if (!phoneExcludeBonus) {
                    totalPhoneBonus += 100;
                }
                totalReceiveBonus += 500;
                displayText += `<div><span class="badge bg-warning">颱風：電話$100、接件$500</span></div>`;
            }

            // 顯示總計
            let totalDisplay = `<div class="mt-2"><strong>總計：接件$${totalReceiveBonus}`;
            if (!phoneExcludeBonus) {
                totalDisplay += `、電話$${totalPhoneBonus}`;
            } else {
                totalDisplay += `、電話不計入獎金`;
            }
            totalDisplay += `</strong></div>`;
            
            displayText += totalDisplay;

            calculationDiv.innerHTML = displayText;
        }



        // 切換加班費功能
        function toggleOvertimeFee() {
            const enableCheckbox = document.getElementById('enable_overtime_fee');
            const overtimeContainer = document.getElementById('overtime-fee-container');
            
            if (enableCheckbox.checked) {
                overtimeContainer.style.display = 'block';
                // 載入當日期的加班記錄
                loadOvertimeRecords();
            } else {
                overtimeContainer.style.display = 'none';
            }
        }

        // 載入加班記錄（用於特定索引）
        function loadOvertimeRecordsForIndex(index) {
            const increaseDate = document.querySelector('input[name="increase_date"]').value;
            const container = document.getElementById(`overtime-records-container-${index}`);
            
            if (!increaseDate) {
                container.innerHTML = 
                    '<div class="alert alert-warning"><i class="fe-alert-triangle me-2"></i>請先選擇加成日期</div>';
                return;
            }

            // 顯示載入中
            container.innerHTML = 
                '<div class="alert alert-info"><i class="fe-loader me-2"></i>載入中...</div>';

            // 發送 AJAX 請求
            fetch(`/increase/overtime-records/${increaseDate}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayOvertimeRecordsForIndex(data.records, index);
                } else {
                    container.innerHTML = 
                        `<div class="alert alert-danger"><i class="fe-alert-triangle me-2"></i>${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = 
                    '<div class="alert alert-danger"><i class="fe-alert-triangle me-2"></i>載入失敗，請稍後再試</div>';
            });
        }

        // 載入加班記錄（舊版本，保留用於獨立加班費區段）
        function loadOvertimeRecords() {
            const increaseDate = document.querySelector('input[name="increase_date"]').value;
            if (!increaseDate) {
                document.getElementById('overtime-records-container').innerHTML = 
                    '<div class="alert alert-warning"><i class="fe-alert-triangle me-2"></i>請先選擇加成日期</div>';
                return;
            }

            // 顯示載入中
            document.getElementById('overtime-records-container').innerHTML = 
                '<div class="alert alert-info"><i class="fe-loader me-2"></i>載入中...</div>';

            // 發送 AJAX 請求
            fetch(`/increase/overtime-records/${increaseDate}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayOvertimeRecords(data.records);
                } else {
                    document.getElementById('overtime-records-container').innerHTML = 
                        `<div class="alert alert-danger"><i class="fe-alert-triangle me-2"></i>${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('overtime-records-container').innerHTML = 
                    '<div class="alert alert-danger"><i class="fe-alert-triangle me-2"></i>載入失敗，請稍後再試</div>';
            });
        }

        // 顯示加班記錄（用於特定索引）
        function displayOvertimeRecordsForIndex(records, index) {
            const container = document.getElementById(`overtime-records-container-${index}`);
            
            if (records.length === 0) {
                container.innerHTML = 
                    '<div class="alert alert-info"><i class="fe-info me-2"></i>該日期無加班記錄</div>';
                return;
            }

            let html = `
                <select class="form-control" id="overtime_record_select_${index}" 
                        onchange="toggleOvertimeEditSection(${index})">
                    <option value="">選擇加班人員</option>`;
            
            records.forEach((record, recordIndex) => {
                // 確保加班費金額四捨五入到整數
                const roundedOvertimePay = Math.round(parseFloat(record.overtime_pay) || 0);
                html += `
                    <option value="${record.id}" 
                            data-formatted-hours="${record.formatted_hours}"
                            data-overtime-pay="${roundedOvertimePay}"
                            data-user-name="${record.user_name}"
                            data-minutes="${record.minutes}"
                            data-reason="${record.reason || ''}">
                        ${record.user_name} - ${record.formatted_hours} ($${roundedOvertimePay})
                    </option>`;
            });
            
            html += `</select>`;
            
            container.innerHTML = html;
        }

        // 顯示加班記錄（舊版本，保留用於獨立加班費區段）
        function displayOvertimeRecords(records) {
            const container = document.getElementById('overtime-records-container');
            
            if (records.length === 0) {
                container.innerHTML = 
                    '<div class="alert alert-info"><i class="fe-info me-2"></i>該日期無加班記錄</div>';
                return;
            }

            let html = '<div class="row">';
            records.forEach((record, index) => {
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <input type="checkbox" name="selected_overtime_records[]" value="${record.id}" 
                                           id="overtime_${record.id}" onchange="toggleOvertimeAmountInputLegacy(${record.id})">
                                    <label for="overtime_${record.id}" class="ms-2">${record.user_name}</label>
                                </h6>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">加班時間：${record.formatted_hours}</small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">原始加班費：$${record.overtime_pay}</small>
                                    </div>
                                </div>
                                <div class="mt-2" id="overtime_amount_input_legacy_${record.id}" style="display: none;">
                                    <label class="form-label">調整加班費金額</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" name="overtime_amounts[${record.id}]" 
                                               value="${record.overtime_pay}" min="0" step="1" 
                                               data-original-amount="${record.overtime_pay}">
                                    </div>
                                    <small class="text-muted">可自行調整加班費金額</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            container.innerHTML = html;
        }

        // 切換加班費金額輸入框（舊版本）
        function toggleOvertimeAmountInputLegacy(recordId) {
            const checkbox = document.getElementById(`overtime_${recordId}`);
            const amountInput = document.getElementById(`overtime_amount_input_legacy_${recordId}`);
            
            if (checkbox.checked) {
                amountInput.style.display = 'block';
            } else {
                amountInput.style.display = 'none';
                // 重置為原始金額
                const input = amountInput.querySelector('input[type="number"]');
                if (input) {
                    const originalAmount = input.getAttribute('data-original-amount');
                    if (originalAmount) {
                        input.value = originalAmount;
                    }
                }
            }
        }

        // 切換加班費編輯區段
        function toggleOvertimeEditSection(index) {
            console.log(`toggleOvertimeEditSection 被調用，索引: ${index}`);
            const selectElement = document.getElementById(`overtime_record_select_${index}`);
            const editSection = document.getElementById(`overtime_edit_section_${index}`);
            
            console.log(`區段 ${index} 下拉選單:`, selectElement);
            console.log(`區段 ${index} 編輯區段:`, editSection);
            console.log(`區段 ${index} 下拉選單值:`, selectElement ? selectElement.value : 'N/A');
            
            if (selectElement && selectElement.value) {
                // 有選擇記錄時，顯示編輯區段
                if (editSection) {
                    editSection.style.display = 'block';
                    console.log(`顯示區段 ${index} 的編輯區段`);
                }
                
                // 取得選中的選項資料並填入表單
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const minutes = selectedOption.getAttribute('data-minutes');
                const overtimePay = selectedOption.getAttribute('data-overtime-pay');
                const reason = selectedOption.getAttribute('data-reason');
                
                console.log(`區段 ${index} 選中選項:`, selectedOption);
                console.log(`區段 ${index} 分鐘:`, minutes);
                console.log(`區段 ${index} 加班費:`, overtimePay);
                console.log(`區段 ${index} 事由:`, reason);
                
                // 填入表單資料
                const recordField = document.getElementById(`overtime_record_field_${index}`);
                const minutesField = document.getElementById(`overtime_minutes_field_${index}`);
                const amountField = document.getElementById(`overtime_amount_field_${index}`);
                const reasonField = document.getElementById(`overtime_reason_field_${index}`);
                
                if (recordField) {
                    recordField.value = selectElement.value;
                }
                if (minutesField) {
                    minutesField.value = minutes;
                    minutesField.setAttribute('data-original-minutes', minutes);
                }
                if (amountField) {
                    // 確保加班費金額四捨五入到整數
                    const roundedOvertimePay = Math.round(parseFloat(overtimePay) || 0);
                    amountField.value = roundedOvertimePay;
                    amountField.setAttribute('data-original-amount', roundedOvertimePay);
                }
                if (reasonField) {
                    reasonField.value = reason;
                    reasonField.setAttribute('data-original-reason', reason);
                }
            } else {
                // 沒有選擇記錄時，隱藏編輯區段
                if (editSection) {
                    editSection.style.display = 'none';
                }
                
                // 清空表單資料
                const recordField = document.getElementById(`overtime_record_field_${index}`);
                const minutesField = document.getElementById(`overtime_minutes_field_${index}`);
                const amountField = document.getElementById(`overtime_amount_field_${index}`);
                const reasonField = document.getElementById(`overtime_reason_field_${index}`);
                
                if (recordField) {
                    recordField.value = '';
                }
                if (minutesField) {
                    minutesField.value = '';
                }
                if (amountField) {
                    amountField.value = '';
                }
                if (reasonField) {
                    reasonField.value = '';
                }
            }
        }

        // 從分鐘計算加班費
        function calculateOvertimePayFromMinutes(index) {
            const minutesField = document.getElementById(`overtime_minutes_field_${index}`);
            const amountField = document.getElementById(`overtime_amount_field_${index}`);
            
            if (!minutesField || !amountField) return;
            
            const minutes = parseInt(minutesField.value) || 0;
            if (minutes <= 0) {
                amountField.value = '';
                return;
            }
            
            // 計算加班費（使用與 OvertimeRecord 模型相同的邏輯）
            const totalHours = minutes / 60;
            let firstTwoHours = 0;
            let remainingHours = 0;
            
            if (totalHours <= 2) {
                firstTwoHours = totalHours;
            } else {
                firstTwoHours = 2;
                remainingHours = totalHours - 2;
            }
            
            // 基本時薪 200 元
            const baseHourlyRate = 200;
            
            // 前兩小時：1.34 倍，剩餘時間：1.67 倍
            const firstTwoHoursPay = firstTwoHours * baseHourlyRate * 1.34;
            const remainingHoursPay = remainingHours * baseHourlyRate * 1.67;
            const totalOvertimePay = firstTwoHoursPay + remainingHoursPay;
            
            amountField.value = Math.round(totalOvertimePay);
        }

        // 儲存加班記錄變更
        function saveOvertimeRecord(index) {
            const selectElement = document.getElementById(`overtime_record_select_${index}`);
            const minutesField = document.getElementById(`overtime_minutes_field_${index}`);
            const amountField = document.getElementById(`overtime_amount_field_${index}`);
            const reasonField = document.getElementById(`overtime_reason_field_${index}`);
            
            if (!selectElement || !selectElement.value) {
                alert('請先選擇加班記錄');
                return;
            }
            
            const recordId = selectElement.value;
            const minutes = minutesField ? parseInt(minutesField.value) : 0;
            const overtimePay = amountField ? Math.round(parseFloat(amountField.value)) : 0;
            const reason = reasonField ? reasonField.value : '';
            
            if (minutes <= 0) {
                alert('請輸入有效的加班分鐘數');
                return;
            }
            
            if (overtimePay < 0) {
                alert('加班費金額不能為負數');
                return;
            }
            
            // 顯示載入中
            const saveButton = document.querySelector(`#overtime_edit_section_${index} .btn-outline-primary`);
            const originalText = saveButton.innerHTML;
            saveButton.innerHTML = '<i class="fe-loader me-1"></i>儲存中...';
            saveButton.disabled = true;
            
            // 發送 AJAX 請求更新記錄
            fetch(`/overtime-records/${recordId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    minutes: minutes,
                    overtime_pay: overtimePay,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 更新成功，更新原始值
                    if (minutesField) {
                        minutesField.setAttribute('data-original-minutes', minutes);
                    }
                    if (amountField) {
                        amountField.setAttribute('data-original-amount', overtimePay);
                    }
                    if (reasonField) {
                        reasonField.setAttribute('data-original-reason', reason);
                    }
                    
                    // 更新下拉選單顯示
                    const selectedOption = selectElement.options[selectElement.selectedIndex];
                    const totalHours = minutes / 60;
                    let formattedHours = '';
                    const hours = Math.floor(totalHours);
                    const remainingMinutes = minutes % 60;
                    
                    if (hours > 0 && remainingMinutes > 0) {
                        formattedHours = `${hours}小時${remainingMinutes}分鐘`;
                    } else if (hours > 0) {
                        formattedHours = `${hours}小時`;
                    } else {
                        formattedHours = `${remainingMinutes}分鐘`;
                    }
                    
                    selectedOption.setAttribute('data-minutes', minutes);
                    selectedOption.setAttribute('data-overtime-pay', overtimePay);
                    selectedOption.setAttribute('data-reason', reason);
                    selectedOption.textContent = `${selectedOption.getAttribute('data-user-name')} - ${formattedHours} ($${overtimePay})`;
                    
                    alert('加班記錄已成功更新');
                } else {
                    alert('更新失敗：' + (data.message || '未知錯誤'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('更新失敗，請稍後再試');
            })
            .finally(() => {
                // 恢復按鈕狀態
                saveButton.innerHTML = originalText;
                saveButton.disabled = false;
            });
        }

        // 更新加班費金額顯示
        function updateOvertimeAmountDisplay(index, newAmount) {
            // 確保金額四捨五入到整數
            const roundedAmount = Math.round(parseFloat(newAmount) || 0);
            console.log(`更新加班費金額: 索引${index}, 金額$${roundedAmount}`);
        }

        // 切換手動新增加班記錄表單
        function toggleManualOvertimeForm() {
            const form = document.getElementById('manual-overtime-form');
            if (form.style.display === 'none') {
                form.style.display = 'block';
                // 清空表單
                document.getElementById('manual_overtime_user').value = '';
                document.getElementById('manual_overtime_minutes').value = '';
                document.getElementById('manual_overtime_reason').value = '';
                document.getElementById('manual_overtime_amount').value = '';
            } else {
                form.style.display = 'none';
            }
        }

        // 取消手動新增加班記錄表單
        function cancelManualOvertimeForm() {
            const form = document.getElementById('manual-overtime-form');
            form.style.display = 'none';
            // 清空表單
            document.getElementById('manual_overtime_user').value = '';
            document.getElementById('manual_overtime_minutes').value = '';
            document.getElementById('manual_overtime_reason').value = '';
            document.getElementById('manual_overtime_amount').value = '';
        }

        // 計算手動加班記錄的加班費
        function calculateManualOvertimeAmount() {
            const minutesInput = document.getElementById('manual_overtime_minutes');
            const amountInput = document.getElementById('manual_overtime_amount');
            
            if (!minutesInput || !amountInput) return;
            
            const minutes = parseInt(minutesInput.value) || 0;
            if (minutes <= 0) {
                amountInput.value = '';
                return;
            }
            
            // 計算加班費（使用與 OvertimeRecord 模型相同的邏輯）
            const totalHours = minutes / 60;
            let firstTwoHours = 0;
            let remainingHours = 0;
            
            if (totalHours <= 2) {
                firstTwoHours = totalHours;
            } else {
                firstTwoHours = 2;
                remainingHours = totalHours - 2;
            }
            
            // 基本時薪 200 元
            const baseHourlyRate = 200;
            
            // 前兩小時：1.34 倍，剩餘時間：1.67 倍
            const firstTwoHoursPay = firstTwoHours * baseHourlyRate * 1.34;
            const remainingHoursPay = remainingHours * baseHourlyRate * 1.67;
            const totalOvertimePay = firstTwoHoursPay + remainingHoursPay;
            
            amountInput.value = Math.round(totalOvertimePay);
        }

        // 儲存手動新增加班記錄
        function saveManualOvertimeRecord() {
            const userId = document.getElementById('manual_overtime_user').value;
            const minutes = document.getElementById('manual_overtime_minutes').value;
            const reason = document.getElementById('manual_overtime_reason').value;
            const overtimePay = document.getElementById('manual_overtime_amount').value;
            const increaseDate = document.querySelector('input[name="increase_date"]').value;
            
            // 驗證必填欄位
            if (!userId) {
                alert('請選擇加班人員');
                return;
            }
            
            if (!minutes || minutes <= 0) {
                alert('請輸入有效的加班分鐘數');
                return;
            }
            
            if (!increaseDate) {
                alert('請先選擇加成日期');
                return;
            }
            
            // 顯示載入中
            const saveButton = document.querySelector('#manual-overtime-form .btn-success');
            const originalText = saveButton.innerHTML;
            saveButton.innerHTML = '<i class="fe-loader me-1"></i>儲存中...';
            saveButton.disabled = true;
            
            // 發送 AJAX 請求建立新的加班記錄
            fetch('/overtime/create-record', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    user_id: userId,
                    overtime_date: increaseDate,
                    minutes: parseInt(minutes),
                    reason: reason,
                    overtime_pay: Math.round(parseFloat(overtimePay))
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('加班記錄建立成功！');
                    
                    // 隱藏表單並清空
                    cancelManualOvertimeForm();
                    
                    // 直接添加新記錄到現有的加班費區段，不重新載入
                    autoSelectNewOvertimeRecord(data.data);
                } else {
                    alert('建立失敗：' + (data.message || '未知錯誤'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('建立失敗，請稍後再試');
            })
            .finally(() => {
                // 恢復按鈕狀態
                saveButton.innerHTML = originalText;
                saveButton.disabled = false;
            });
        }

        // 自動選擇新建立的加班記錄
        function autoSelectNewOvertimeRecord(recordData) {
            console.log('開始處理新建立的加班記錄:', recordData);
            
            // 記錄現有區段的數量
            const existingContainers = document.querySelectorAll('[id^="overtime-records-container-"]');
            console.log('現有區段數量:', existingContainers.length);
            
            // 總是創建新的區段，避免影響現有的區段
            console.log('創建新區段用於手動新增的加班記錄');
            addOvertime();
            
            // 等待新區段建立完成
            setTimeout(() => {
                const newContainers = document.querySelectorAll('[id^="overtime-records-container-"]');
                const targetIndex = newContainers.length - 1;
                console.log('新區段數量:', newContainers.length, '目標索引:', targetIndex);
                
                // 直接添加新記錄，不載入現有記錄
                addRecordToOvertimeSection(recordData, targetIndex);
            }, 100);
        }
        
        // 將記錄添加到指定的加班費區段（帶有回調函數版本）
        function addRecordToOvertimeSection(recordData, index, callback) {
            console.log(`添加記錄到區段 ${index}:`, recordData);
            let selectElement = document.getElementById(`overtime_record_select_${index}`);
            
            // 定義實際添加記錄的函數
            const actuallyAddRecord = () => {
                selectElement = document.getElementById(`overtime_record_select_${index}`);
                
                if (selectElement) {
                    console.log(`找到區段 ${index} 的下拉選單，現有選項數量:`, selectElement.options.length);
                    
                    // 檢查是否已經存在相同的記錄
                    const existingOption = selectElement.querySelector(`option[value="${recordData.id}"]`);
                    if (existingOption) {
                        console.log(`區段 ${index} 已存在記錄 ${recordData.id}，直接選擇`);
                        selectElement.value = recordData.id;
                    } else {
                        console.log(`區段 ${index} 不存在記錄 ${recordData.id}，添加新選項`);
                        // 直接添加新記錄到下拉選單
                        const newOption = document.createElement('option');
                        newOption.value = recordData.id;
                        newOption.setAttribute('data-formatted-hours', recordData.formatted_hours);
                        // 確保加班費金額四捨五入到整數
                        const roundedOvertimePay = Math.round(parseFloat(recordData.overtime_pay) || 0);
                        newOption.setAttribute('data-overtime-pay', roundedOvertimePay);
                        newOption.setAttribute('data-user-name', recordData.user_name);
                        newOption.setAttribute('data-minutes', recordData.minutes);
                        newOption.setAttribute('data-reason', recordData.reason || '');
                        newOption.textContent = `${recordData.user_name} - ${recordData.formatted_hours} ($${roundedOvertimePay})`;
                        
                        // 添加到下拉選單
                        selectElement.appendChild(newOption);
                        
                        // 選擇新建立的記錄
                        selectElement.value = recordData.id;
                    }
                    
                    console.log(`設置區段 ${index} 的值為:`, recordData.id);
                    
                    // 更新隱藏的 overtime_record 欄位
                    const recordField = document.getElementById(`overtime_record_field_${index}`);
                    if (recordField) {
                        recordField.value = recordData.id;
                        console.log(`設置隱藏欄位 overtime_record_field_${index} 的值為:`, recordData.id);
                    }
                    
                    // 觸發變更事件來顯示編輯區段
                    console.log(`觸發區段 ${index} 的編輯區段顯示`);
                    toggleOvertimeEditSection(index);
                    
                    if (callback) callback();
                } else {
                    console.error(`無法找到區段 ${index} 的下拉選單`);
                }
            };
            
            if (!selectElement) {
                console.log(`區段 ${index} 沒有下拉選單，先載入該日期的所有記錄`);
                // 先載入該日期的所有加班記錄，創建下拉選單
                const increaseDate = document.querySelector('input[name="increase_date"]').value;
                const container = document.getElementById(`overtime-records-container-${index}`);
                
                if (!increaseDate) {
                    console.error('未選擇加成日期，無法載入記錄');
                    return;
                }
                
                // 使用 fetch 載入記錄
                fetch(`/increase/overtime-records/${increaseDate}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 先顯示該日期的所有記錄
                        displayOvertimeRecordsForIndex(data.records, index);
                        // 然後添加新記錄
                        setTimeout(() => actuallyAddRecord(), 50);
                    } else {
                        console.error('載入記錄失敗:', data.message);
                    }
                })
                .catch(error => {
                    console.error('載入記錄時發生錯誤:', error);
                });
            } else {
                // 下拉選單已存在，直接添加記錄
                actuallyAddRecord();
            }
        }


        // 監聽日期變化
        document.querySelector('input[name="increase_date"]').addEventListener('change', function() {
            // 載入所有加班費區段的加班記錄
            loadAllOvertimeRecords();
        });

        // 載入所有加班費區段的加班記錄
        function loadAllOvertimeRecords() {
            const overtimeContainers = document.querySelectorAll('[id^="overtime-records-container-"]');
            overtimeContainers.forEach(container => {
                const index = container.id.match(/overtime-records-container-(\d+)/)[1];
                loadOvertimeRecordsForIndex(index);
            });
        }

        // 初始化頁面載入時的 select2
        document.addEventListener('DOMContentLoaded', function() {
            // 初始化所有預設的 select2 下拉選單
            $('select[data-toggle="select"]').select2();
            
            // 載入預設加班費區段的加班記錄
            const increaseDate = document.querySelector('input[name="increase_date"]').value;
            if (increaseDate) {
                loadOvertimeRecordsForIndex(0); // 載入預設的加班費區段
            }
        });

        // 表單驗證
        document.getElementById('increaseForm').addEventListener('submit', function(e) {
            // 這裡可以添加表單驗證邏輯
            console.log('表單提交');
        });
    </script>
@endsection

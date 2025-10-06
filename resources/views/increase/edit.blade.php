@extends('layouts.vertical', ['page_title' => '編輯加成'])

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
                            <li class="breadcrumb-item active">編輯加成</li>
                        </ol>
                    </div>
                    <h4 class="page-title">編輯加成</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('increase.edit.data', $increase->id) }}" method="POST" id="increaseForm">
                            @csrf
                            @method('PUT')

                            <!-- 基本資訊 -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">加成日期<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="increase_date"
                                            value="{{ $increase->increase_date->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">備註</label>
                                        <textarea class="form-control" name="comment" rows="3" placeholder="請輸入備註...">{{ $increase->comment }}</textarea>
                                    </div>
                                </div>
                            </div>

                            @php
                                // 按類型分組現有項目
                                $traditionalItems = $increase->items->where('item_type', 'traditional')->values();
                                $furnaceItems = $increase->items->where('item_type', 'furnace')->values();
                                $overtimeItems = $increase->items->where('item_type', 'overtime')->values();
                            @endphp

                            <!-- 1. 加成類別區段（夜間、晚間、颱風） -->
                            <div class="category-section">
                                <h5 class="category-title">
                                    <i class="fe-award me-2"></i>加成類別（夜間、晚間、颱風）
                                </h5>
                                <div id="increase-container">
                                    @foreach($traditionalItems as $index => $item)
                                    <div class="person-row" data-index="{{ $index }}">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <!-- 類別選擇 -->
                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        <label class="form-label">適用類別</label>
                                                        <div class="category-checkboxes">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="increase[{{ $index }}][categories][]" value="night"
                                                                    id="night_{{ $index }}" onchange="calculateBonus({{ $index }})"
                                                                    {{ ($item->night_phone_amount > 0 || $item->night_receive_amount > 0) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="night_{{ $index }}">夜間加成</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="increase[{{ $index }}][categories][]" value="evening"
                                                                    id="evening_{{ $index }}" onchange="calculateBonus({{ $index }})"
                                                                    {{ ($item->evening_phone_amount > 0 || $item->evening_receive_amount > 0) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="evening_{{ $index }}">晚間加成</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="increase[{{ $index }}][categories][]" value="typhoon"
                                                                    id="typhoon_{{ $index }}" onchange="calculateBonus({{ $index }})"
                                                                    {{ ($item->typhoon_phone_amount > 0 || $item->typhoon_receive_amount > 0) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="typhoon_{{ $index }}">颱風加成</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- 人員選擇 -->
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label">接電話人員</label>
                                                        <select class="form-control" name="increase[{{ $index }}][phone_person]" data-toggle="select" onchange="calculateBonus({{ $index }})">
                                                            <option value="">請選擇人員</option>
                                                            @foreach ($users ?? [] as $user)
                                                                <option value="{{ $user->id }}" {{ $item->phone_person_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="mt-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="increase[{{ $index }}][phone_exclude_bonus]" value="1" id="phone_exclude_bonus_{{ $index }}" onchange="calculateBonus({{ $index }})" {{ $item->phone_exclude_bonus ?? false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="phone_exclude_bonus_{{ $index }}">
                                                                    <small class="text-muted">不計入獎金</small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">接件人員</label>
                                                        <select class="form-control" name="increase[{{ $index }}][receive_person]" data-toggle="select">
                                                            <option value="">請選擇人員</option>
                                                            @foreach ($users ?? [] as $user)
                                                                <option value="{{ $user->id }}" {{ $item->receive_person_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">獎金計算</label>
                                                <div class="bonus-calculation" id="bonus-calculation-{{ $index }}">
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
                                    @endforeach
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
                                    @foreach($furnaceItems as $index => $item)
                                    <div class="person-row" data-furnace-index="{{ $index }}">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label">夜間開爐時段</label>
                                                <select class="form-control" name="furnace[{{ $index }}][time_slot_id]" id="furnace_time_slot_{{ $index }}" onchange="calculateFurnacePrice({{ $index }})">
                                                    <option value="">請選擇時段</option>
                                                    @foreach ($timeSlots ?? [] as $timeSlot)
                                                        <option value="{{ $timeSlot->id }}" 
                                                                data-price="{{ $timeSlot->price }}"
                                                                {{ $item->time_slot_id == $timeSlot->id ? 'selected' : '' }}>
                                                            {{ $timeSlot->full_description }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">負責人員</label>
                                                <select class="form-control" name="furnace[{{ $index }}][furnace_person]" data-toggle="select">
                                                    <option value="">請選擇人員</option>
                                                    @foreach ($users ?? [] as $user)
                                                        <option value="{{ $user->id }}" {{ $item->furnace_person_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">計算價格</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="text" class="form-control" id="furnace_calculated_price_{{ $index }}" value="{{ $item->total_amount }}" readonly>
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
                                    @endforeach
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
                                    @foreach($overtimeItems as $index => $item)
                                    <div class="person-row" data-overtime-index="{{ $index }}">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">加班記錄選擇</label>
                                                <div id="overtime-records-container-{{ $index }}">
                                                    @if($item->overtimeRecord)
                                                        <select class="form-control" name="overtime[{{ $index }}][overtime_record]" 
                                                                id="overtime_record_select_{{ $index }}" 
                                                                onchange="toggleOvertimeAmountInputDropdown({{ $index }})">
                                                            <option value="">選擇加班人員</option>
                                                            <option value="{{ $item->overtime_record_id }}" 
                                                                    data-formatted-hours="{{ $item->overtimeRecord->formatted_hours }}"
                                                                    data-overtime-pay="{{ $item->custom_amount ?? $item->overtimeRecord->overtime_pay }}"
                                                                    data-user-name="{{ $item->overtimeRecord->user->name ?? '未知人員' }}"
                                                                    selected>
                                                                {{ $item->overtimeRecord->user->name ?? '未知人員' }} - {{ $item->overtimeRecord->formatted_hours }} (${{ $item->custom_amount ?? $item->overtimeRecord->overtime_pay }})
                                                            </option>
                                                        </select>
                                                    @else
                                                        <div class="alert alert-info">
                                                            <i class="fe-info me-2"></i>請選擇加成日期以載入該日期的加班記錄
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div id="overtime_amount_input_dropdown_{{ $index }}" style="{{ $item->overtime_record_id ? 'display: block;' : 'display: none;' }}">
                                                    <label class="form-label">調整加班費金額</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control" name="overtime[{{ $index }}][overtime_amount]" 
                                                               id="overtime_amount_field_{{ $index }}" min="0" step="1" 
                                                               value="{{ $item->custom_amount ?? $item->overtimeRecord->overtime_pay ?? '' }}"
                                                               onchange="updateOvertimeAmountDisplay({{ $index }}, this.value)">
                                                    </div>
                                                    <small class="text-muted">可自行調整加班費金額</small>
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
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="addOvertime()">
                                    <i class="fe-plus me-1"></i>新增加班費
                                </button>
                            </div>

                            <!-- 提交按鈕 -->
                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success waves-effect waves-light m-1">
                                        <i class="fe-check-circle me-1"></i>更新加成
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
                             @foreach ($users ?? [] as $user)
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
                             @foreach ($users ?? [] as $user)
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
                     @foreach ($timeSlots ?? [] as $timeSlot)
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
                     @foreach ($users ?? [] as $user)
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
             <div class="col-md-6">
                 <label class="form-label">加班記錄選擇</label>
                 <div id="overtime-records-container-${newIndex}">
                     <div class="alert alert-info">
                         <i class="fe-info me-2"></i>請選擇加成日期以載入該日期的加班記錄
                     </div>
                 </div>
             </div>
             <div class="col-md-6">
                 <div id="overtime_amount_input_dropdown_${newIndex}" style="display: none;">
                     <label class="form-label">調整加班費金額</label>
                     <div class="input-group">
                         <span class="input-group-text">$</span>
                         <input type="number" class="form-control" name="overtime[${newIndex}][overtime_amount]" 
                                id="overtime_amount_field_${newIndex}" min="0" step="1" 
                                onchange="updateOvertimeAmountDisplay(${newIndex}, this.value)">
                     </div>
                     <small class="text-muted">可自行調整加班費金額</small>
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
                        input.setAttribute('onchange', `calculateBonus(${index})`);
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

        // 顯示加班記錄（用於特定索引）
        function displayOvertimeRecordsForIndex(records, index) {
            const container = document.getElementById(`overtime-records-container-${index}`);
            
            if (records.length === 0) {
                container.innerHTML = 
                    '<div class="alert alert-info"><i class="fe-info me-2"></i>該日期無加班記錄</div>';
                return;
            }

            let html = `
                <select class="form-control" name="overtime[${index}][overtime_record]" 
                        id="overtime_record_select_${index}" 
                        onchange="toggleOvertimeAmountInputDropdown(${index})">
                    <option value="">選擇加班人員</option>`;
            
            records.forEach((record, recordIndex) => {
                html += `
                    <option value="${record.id}" 
                            data-formatted-hours="${record.formatted_hours}"
                            data-overtime-pay="${record.overtime_pay}"
                            data-user-name="${record.user_name}">
                        ${record.user_name} - ${record.formatted_hours} ($${record.overtime_pay})
                    </option>`;
            });
            
            html += `</select>`;
            
            container.innerHTML = html;
        }

        // 切換加班費金額輸入框（用於下拉式選單）
        function toggleOvertimeAmountInputDropdown(index) {
            const selectElement = document.getElementById(`overtime_record_select_${index}`);
            const amountInputDiv = document.getElementById(`overtime_amount_input_dropdown_${index}`);
            const amountInputField = document.getElementById(`overtime_amount_field_${index}`);
            
            if (selectElement && selectElement.value) {
                // 有選擇記錄時，顯示金額輸入框
                if (amountInputDiv) {
                    amountInputDiv.style.display = 'block';
                }
                
                // 取得選中的選項資料
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const originalAmount = selectedOption.getAttribute('data-overtime-pay');
                
                // 設定金額輸入框的值
                if (amountInputField) {
                    amountInputField.value = originalAmount;
                    amountInputField.setAttribute('data-original-amount', originalAmount);
                }
            } else {
                // 沒有選擇記錄時，隱藏金額輸入框
                if (amountInputDiv) {
                    amountInputDiv.style.display = 'none';
                }
                
                // 清空金額輸入框
                if (amountInputField) {
                    amountInputField.value = '';
                }
            }
        }

        // 更新加班費金額顯示
        function updateOvertimeAmountDisplay(index, newAmount) {
            // 可以在這裡添加即時更新顯示的邏輯
            console.log(`更新加班費金額: 索引${index}, 金額$${newAmount}`);
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
            
            // 初始化所有現有項目的獎金計算
            document.querySelectorAll('#increase-container .person-row').forEach((row, index) => {
                calculateBonus(index);
            });
            
            // 初始化所有現有夜間開爐項目的價格計算
            document.querySelectorAll('#furnace-container .person-row').forEach((row, index) => {
                calculateFurnacePrice(index);
            });
            
            // 載入所有加班費區段的加班記錄
            const increaseDate = document.querySelector('input[name="increase_date"]').value;
            if (increaseDate) {
                loadAllOvertimeRecords();
            }
        });

        // 表單驗證
        document.getElementById('increaseForm').addEventListener('submit', function(e) {
            // 這裡可以添加表單驗證邏輯
            console.log('表單提交');
        });
    </script>
@endsection

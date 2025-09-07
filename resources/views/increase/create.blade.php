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

                            <!-- 1-3. 加成類別（夜間、晚間、颱風） -->
                            <div class="category-section">
                                <h5 class="category-title">
                                    <i class="fe-users me-2"></i>加成類別（夜間、晚間、颱風）
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
                                                            @foreach ($users ?? [] as $user)
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
                                                            @foreach ($users ?? [] as $user)
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

                            <!-- 4. 加班 -->
                            {{-- <div class="category-section">
                                <h5 class="category-title">
                                    <i class="fe-clock me-2"></i>加班
                                </h5>
                                <div id="overtime-container">
                                    <div class="overtime-row" data-index="0">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">加班人員</label>
                                                <select class="form-control" name="overtime[0][person]" data-toggle="select">
                                                    <option value="">請選擇人員</option>
                                                    @foreach ($users ?? [] as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">加班分鐘</label>
                                                <input type="number" class="form-control overtime-minutes"
                                                    name="overtime[0][minutes]" placeholder="分鐘"
                                                    onkeyup="calculateOvertimePay(this)"
                                                    onchange="calculateOvertimePay(this)">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">加班費計算</label>
                                                <div class="overtime-calculation" id="overtime-calculation-0">
                                                    <small class="text-muted">請輸入加班分鐘</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">事由</label>
                                                <input type="text" class="form-control" name="overtime[0][reason]"
                                                    placeholder="請輸入加班事由">
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end justify-content-center">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger remove-overtime"
                                                    onclick="removeOvertime(this)">
                                                    <i class="fe-trash-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="addOvertime()">
                                    <i class="fe-plus me-1"></i>新增加班記錄
                                </button>
                            </div> --}}

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

        // 新增加班記錄
        function addOvertime() {
            const container = document.getElementById('overtime-container');
            const existingRows = container.querySelectorAll('.overtime-row');
            const newIndex = existingRows.length;

            const newRow = document.createElement('div');
            newRow.className = 'overtime-row';
            newRow.setAttribute('data-index', newIndex);

            newRow.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">加班人員</label>
                <select class="form-control" name="overtime[${newIndex}][person]" data-toggle="select">
                    <option value="">請選擇人員</option>
                    @foreach ($users ?? [] as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
                         <div class="col-md-2">
                 <label class="form-label">加班分鐘</label>
                                   <input type="number" class="form-control overtime-minutes" name="overtime[${newIndex}][minutes]" placeholder="分鐘" onkeyup="calculateOvertimePay(this)" onchange="calculateOvertimePay(this)">
             </div>
             <div class="col-md-2">
                 <label class="form-label">加班費計算</label>
                 <div class="overtime-calculation" id="overtime-calculation-${newIndex}">
                     <small class="text-muted">請輸入加班分鐘</small>
                 </div>
             </div>
             <div class="col-md-3">
                 <label class="form-label">事由</label>
                 <input type="text" class="form-control" name="overtime[${newIndex}][reason]" placeholder="請輸入加班事由">
             </div>
                         <div class="col-md-1 d-flex align-items-end justify-content-center">
                 <button type="button" class="btn btn-sm btn-outline-danger remove-overtime" onclick="removeOvertime(this)">
                     <i class="fe-trash-2"></i>
                 </button>
             </div>
        </div>
    `;

            container.appendChild(newRow);

            // 重新初始化 select2
            $(newRow).find('select[data-toggle="select"]').select2();
        }

        // 移除加班記錄
        function removeOvertime(button) {
            const container = document.getElementById('overtime-container');
            const rows = container.querySelectorAll('.overtime-row');

            if (rows.length > 1) {
                button.closest('.overtime-row').remove();
                // 重新編號
                container.querySelectorAll('.overtime-row').forEach((row, index) => {
                    row.setAttribute('data-index', index);
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

        // 計算獎金
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

        // 初始化頁面載入時的 select2
        document.addEventListener('DOMContentLoaded', function() {
            // 初始化所有預設的 select2 下拉選單
            $('select[data-toggle="select"]').select2();
        });

        // 表單驗證
        document.getElementById('increaseForm').addEventListener('submit', function(e) {
            // 這裡可以添加表單驗證邏輯
            console.log('表單提交');
        });
    </script>
@endsection

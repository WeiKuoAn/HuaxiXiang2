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

        .day-worklog-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .day-worklog-card {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            height: 100%;
            box-shadow: 0 2px 4px rgba(15, 34, 58, 0.05);
        }

        .day-worklog-name {
            font-weight: 600;
            color: #212529;
            font-size: 1.15rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .day-worklog-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .day-worklog-label {
            color: #6c757d;
            font-size: 1.05rem;
        }

        .day-worklog-badge {
            font-size: 1.15rem;
            padding: 0.5rem 0.85rem;
        }
        
        .form-label {
            font-size: 1.05rem;
        }
        
        .form-control, .form-control-sm {
            font-size: 1.05rem;
        }
        
        .btn {
            font-size: 1.05rem;
        }
        
        h5, h6 {
            font-size: 1.2rem;
        }
        
        .text-muted {
            font-size: 1rem;
        }
        
        .badge {
            font-size: 1rem !important;
            padding: 0.6rem 1rem !important;
        }
        
        .form-check-label .badge {
            font-size: 1rem !important;
            padding: 0.6rem 1rem !important;
        }

        @media (max-width: 991.98px) {
            .day-worklog-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575.98px) {
            .day-worklog-grid {
                grid-template-columns: 1fr;
            }
        }

        .day-worklog-placeholder {
            border: 1px dashed #dee2e6;
            border-radius: 8px;
            padding: 24px;
            text-align: center;
            color: #6c757d;
            background-color: #f8f9fa;
        }

        .day-worklog-error {
            border: 1px solid rgba(220, 53, 69, 0.2);
            background-color: rgba(220, 53, 69, 0.08);
            color: #dc3545;
            border-radius: 6px;
            padding: 12px 16px;
        }

        .day-worklog-loading {
            border: 1px solid rgba(13, 110, 253, 0.1);
            background-color: rgba(13, 110, 253, 0.06);
            color: #0d6efd;
            border-radius: 6px;
            padding: 12px 16px;
        }

        /* 統一人員 select2 欄位為長條樣式 */
        .person-row select[data-toggle="select"] {
            width: 100%;
        }

        .person-row .select2-container {
            width: 100% !important;
        }

        .person-row .select2-selection {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
        }

        .person-row .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
        }

        .person-row .select2-selection__arrow {
            height: 36px !important;
        }

        .role-checkboxes {
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
                                // 按類別和角色分組現有項目
                                $dayPhoneItems = $increase->items
                                    ->where('category', 'day')
                                    ->where('role', 'phone')
                                    ->values();
                                $dayReceiveItems = $increase->items
                                    ->where('category', 'day')
                                    ->where('role', 'receive')
                                    ->values();
                                $eveningPhoneItems = $increase->items
                                    ->where('category', 'evening')
                                    ->where('role', 'phone')
                                    ->values();
                                $eveningReceiveItems = $increase->items
                                    ->where('category', 'evening')
                                    ->where('role', 'receive')
                                    ->values();
                                $nightPhoneItems = $increase->items
                                    ->where('category', 'night')
                                    ->where('role', 'phone')
                                    ->values();
                                $nightReceiveItems = $increase->items
                                    ->where('category', 'night')
                                    ->where('role', 'receive')
                                    ->values();
                                $furnaceItems = $increase->items->where('item_type', 'furnace')->values();
                                $overtimeItems = $increase->items->where('item_type', 'overtime')->values();
                            @endphp

                            <!-- 人員加成頁籤 -->
                            <ul class="nav nav-tabs nav-tabs-custom mb-3" id="increaseTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="tab-day-tab" data-bs-toggle="tab" href="#tab-day" role="tab"
                                        aria-controls="tab-day" aria-selected="true">
                                        <i class="fe-sun me-1"></i>白天
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="tab-evening-tab" data-bs-toggle="tab" href="#tab-evening" role="tab"
                                        aria-controls="tab-evening" aria-selected="false">
                                        <i class="fe-moon me-1"></i>晚間
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="tab-night-tab" data-bs-toggle="tab" href="#tab-night" role="tab"
                                        aria-controls="tab-night" aria-selected="false">
                                        <i class="fe-star me-1"></i>夜間
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="tab-furnace-tab" data-bs-toggle="tab" href="#tab-furnace" role="tab"
                                        aria-controls="tab-furnace" aria-selected="false">
                                        <i class="fe-thermometer me-1"></i>夜間開爐
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="tab-overtime-tab" data-bs-toggle="tab" href="#tab-overtime" role="tab"
                                        aria-controls="tab-overtime" aria-selected="false">
                                        <i class="fe-clock me-1"></i>加班
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content" id="increaseTabsContent">
                                <!-- 0. 白天加成區段 -->
                                <div class="tab-pane fade show active" id="tab-day" role="tabpanel" aria-labelledby="tab-day-tab">
                            <div class="category-section">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="category-title mb-0">
                                        <i class="fe-sun me-2"></i>白天加成
                                    </h5>
                                    <div class="text-end">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="day_is_typhoon"
                                                value="1" id="day_is_typhoon"
                                                {{ $increase->day_is_typhoon ? 'checked' : '' }}
                                                onchange="updateAllAmounts()">
                                            <label class="form-check-label fw-bold" for="day_is_typhoon">
                                                <span class="badge bg-warning text-dark">天災</span>
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="day_is_newyear"
                                                value="1" id="day_is_newyear"
                                                {{ $increase->day_is_newyear ? 'checked' : '' }}
                                                onchange="updateAllAmounts()">
                                            <label class="form-check-label fw-bold" for="day_is_newyear">
                                                <span class="badge bg-danger text-white">過年</span>
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                            基礎：電話${{ number_format($increaseSettings['day']->phone_bonus ?? 0, 0) }}/次、
                                            接件${{ number_format($increaseSettings['day']->receive_bonus ?? 0, 0) }}/次；
                                            天災/過年將累加。
                                        </small>
                                    </div>
                                </div>

                                <!-- 白天電話人員 -->
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">
                                        <i class="fe-phone me-1"></i>電話人員
                                    </h6>
                                    <div class="row text-muted fw-bold d-none d-md-flex px-2 mb-1">
                                        <div class="col-md-5">人員</div>
                                        <div class="col-md-2">次數</div>
                                        <div class="col-md-2">金額</div>
                                        <div class="col-md-3 text-center">操作</div>
                                    </div>
                                    <div id="day-phone-container">
                                        @forelse($dayPhoneItems as $index => $item)
                                            <div class="person-row mb-2" data-day-phone-index="{{ $index }}">
                                                <div class="row align-items-center g-2">
                                                    <div class="col-md-5">
                                                        <label class="form-label d-md-none">人員</label>
                                                        <select class="form-control"
                                                            name="day_phone[{{ $index }}][person]"
                                                            data-toggle="select">
                                                            <option value="">請選擇人員</option>
                                                            @foreach ($users ?? [] as $user)
                                                                <option value="{{ $user->id }}"
                                                                    {{ $item->phone_person_id == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">次數</label>
                                                        <input type="number" class="form-control"
                                                            name="day_phone[{{ $index }}][count]" min="0"
                                                            value="{{ $item->count ?? 1 }}"
                                                            onchange="calculateRowAmount(this, 'day_phone', {{ $index }})"
                                                            oninput="calculateRowAmount(this, 'day_phone', {{ $index }})">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">金額</label>
                                                        <input type="text" class="form-control text-end" id="day_phone_amount_{{ $index }}" readonly>
                                                    </div>
                                                    <div class="col-md-3 text-end text-md-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="removeDayPhone(this)">
                                                            <i class="fe-trash-2 me-1"></i>移除
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="person-row mb-2" data-day-phone-index="0">
                                                <div class="row align-items-center g-2">
                                                    <div class="col-md-5">
                                                        <label class="form-label d-md-none">人員</label>
                                                        <select class="form-control" name="day_phone[0][person]"
                                                            data-toggle="select">
                                                            <option value="">請選擇人員</option>
                                                            @foreach ($users ?? [] as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">次數</label>
                                                        <input type="number" class="form-control"
                                                            name="day_phone[0][count]" min="0" value="1"
                                                            onchange="calculateRowAmount(this, 'day_phone', 0)"
                                                            oninput="calculateRowAmount(this, 'day_phone', 0)">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">金額</label>
                                                        <input type="text" class="form-control text-end" id="day_phone_amount_0" readonly>
                                                    </div>
                                                    <div class="col-md-3 text-end text-md-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="removeDayPhone(this)">
                                                            <i class="fe-trash-2 me-1"></i>移除
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="addDayPhone()">
                                        <i class="fe-plus me-1"></i>新增電話人員
                                    </button>
                                </div>

                                <!-- 白天接件人員 -->
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">
                                        <i class="fe-user-check me-1"></i>接件人員
                                    </h6>
                                    <div class="row text-muted fw-bold d-none d-md-flex px-2 mb-1">
                                        <div class="col-md-5">人員</div>
                                        <div class="col-md-2">次數</div>
                                        <div class="col-md-2">金額</div>
                                        <div class="col-md-3 text-center">操作</div>
                                    </div>
                                    <div id="day-receive-container">
                                        @forelse($dayReceiveItems as $index => $item)
                                            <div class="person-row mb-2" data-day-receive-index="{{ $index }}">
                                                <div class="row align-items-center g-2">
                                                    <div class="col-md-5">
                                                        <label class="form-label d-md-none">人員</label>
                                                        <select class="form-control"
                                                            name="day_receive[{{ $index }}][person]"
                                                            data-toggle="select">
                                                            <option value="">請選擇人員</option>
                                                            @foreach ($users ?? [] as $user)
                                                                <option value="{{ $user->id }}"
                                                                    {{ $item->receive_person_id == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">次數</label>
                                                        <input type="number" class="form-control"
                                                            name="day_receive[{{ $index }}][count]" min="0"
                                                            value="{{ $item->count ?? 1 }}"
                                                            onchange="calculateRowAmount(this, 'day_receive', {{ $index }})"
                                                            oninput="calculateRowAmount(this, 'day_receive', {{ $index }})">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">金額</label>
                                                        <input type="text" class="form-control text-end" id="day_receive_amount_{{ $index }}" readonly>
                                                    </div>
                                                    <div class="col-md-3 text-end text-md-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="removeDayReceive(this)">
                                                            <i class="fe-trash-2 me-1"></i>移除
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="person-row mb-2" data-day-receive-index="0">
                                                <div class="row align-items-center g-2">
                                                    <div class="col-md-5">
                                                        <label class="form-label d-md-none">人員</label>
                                                        <select class="form-control" name="day_receive[0][person]"
                                                            data-toggle="select">
                                                            <option value="">請選擇人員</option>
                                                            @foreach ($users ?? [] as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">次數</label>
                                                        <input type="number" class="form-control"
                                                            name="day_receive[0][count]" min="0" value="1"
                                                            onchange="calculateRowAmount(this, 'day_receive', 0)"
                                                            oninput="calculateRowAmount(this, 'day_receive', 0)">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">金額</label>
                                                        <input type="text" class="form-control text-end" id="day_receive_amount_0" readonly>
                                                    </div>
                                                    <div class="col-md-3 text-end text-md-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="removeDayReceive(this)">
                                                            <i class="fe-trash-2 me-1"></i>移除
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="addDayReceive()">
                                        <i class="fe-plus me-1"></i>新增接件人員
                                    </button>
                                </div>
                            </div>

                            </div>

                            <!-- 1. 晚間加成區段 -->
                                <div class="tab-pane fade" id="tab-evening" role="tabpanel" aria-labelledby="tab-evening-tab">
                            <div class="category-section">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="category-title mb-0">
                                        <i class="fe-moon me-2"></i>晚間加成
                                    </h5>
                                    <div class="text-end">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="evening_is_typhoon"
                                                value="1" id="evening_is_typhoon"
                                                {{ $increase->evening_is_typhoon ? 'checked' : '' }}
                                                onchange="updateAllAmounts()">
                                            <label class="form-check-label fw-bold" for="evening_is_typhoon">
                                                <span class="badge bg-warning text-dark">天災</span>
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="evening_is_newyear"
                                                value="1" id="evening_is_newyear"
                                                {{ $increase->evening_is_newyear ? 'checked' : '' }}
                                                onchange="updateAllAmounts()">
                                            <label class="form-check-label fw-bold" for="evening_is_newyear">
                                                <span class="badge bg-danger text-white">過年</span>
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                            天災：電話+${{ number_format($increaseSettings['typhoon']->phone_bonus ?? 100, 0) }}、接件+${{ number_format($increaseSettings['typhoon']->receive_bonus ?? 500, 0) }} | 
                                            過年：電話+${{ number_format($increaseSettings['newyear']->phone_bonus ?? 100, 0) }}、接件+${{ number_format($increaseSettings['newyear']->receive_bonus ?? 500, 0) }}
                                        </small>
                                    </div>
                                </div>

                                <!-- 電話人員區塊 -->
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">
                                        <i class="fe-phone me-1"></i>電話人員 <small class="text-muted">(一般${{ number_format($increaseSettings['evening']->phone_bonus ?? 50, 0) }}/次)</small>
                                    </h6>
                                    <div class="row text-muted fw-bold d-none d-md-flex px-2 mb-1">
                                        <div class="col-md-5">人員</div>
                                        <div class="col-md-2">次數</div>
                                        <div class="col-md-2">金額</div>
                                        <div class="col-md-3 text-center">操作</div>
                                    </div>
                                    <div id="evening-phone-container">
                                        @forelse($eveningPhoneItems as $index => $item)
                                            <div class="person-row mb-2" data-evening-phone-index="{{ $index }}">
                                                <div class="row align-items-center g-2">
                                                    <div class="col-md-5">
                                                        <label class="form-label d-md-none">人員</label>
                                                        <select class="form-control"
                                                            name="evening_phone[{{ $index }}][person]"
                                                            data-toggle="select">
                                                            <option value="">請選擇人員</option>
                                                            @foreach ($users ?? [] as $user)
                                                                <option value="{{ $user->id }}"
                                                                    {{ $item->phone_person_id == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">次數</label>
                                                        <input type="number" class="form-control"
                                                            name="evening_phone[{{ $index }}][count]" min="0"
                                                            value="{{ $item->count ?? 1 }}"
                                                            onchange="calculateRowAmount(this, 'evening_phone', {{ $index }})"
                                                            oninput="calculateRowAmount(this, 'evening_phone', {{ $index }})">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">金額</label>
                                                        <input type="text" class="form-control text-end" id="evening_phone_amount_{{ $index }}" readonly>
                                                    </div>
                                                    <div class="col-md-3 text-end text-md-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="removeEveningPhone(this)">
                                                            <i class="fe-trash-2 me-1"></i>移除
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="person-row mb-2" data-evening-phone-index="0">
                                                <div class="row align-items-center g-2">
                                                    <div class="col-md-5">
                                                        <label class="form-label d-md-none">人員</label>
                                                        <select class="form-control" name="evening_phone[0][person]"
                                                            data-toggle="select">
                                                            <option value="">請選擇人員</option>
                                                            @foreach ($users ?? [] as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">次數</label>
                                                        <input type="number" class="form-control"
                                                            name="evening_phone[0][count]" min="0" value="1"
                                                            onchange="calculateRowAmount(this, 'evening_phone', 0)"
                                                            oninput="calculateRowAmount(this, 'evening_phone', 0)">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">金額</label>
                                                        <input type="text" class="form-control text-end" id="evening_phone_amount_0" readonly>
                                                    </div>
                                                    <div class="col-md-3 text-end text-md-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="removeEveningPhone(this)">
                                                            <i class="fe-trash-2 me-1"></i>移除
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="addEveningPhone()">
                                        <i class="fe-plus me-1"></i>新增電話人員
                                    </button>
                                </div>

                                <!-- 接件人員區塊 -->
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">
                                        <i class="fe-user-check me-1"></i>接件人員 <small
                                            class="text-muted">(一般${{ number_format($increaseSettings['evening']->receive_bonus ?? 250, 0) }}/次)</small>
                                    </h6>
                                    <div class="row text-muted fw-bold d-none d-md-flex px-2 mb-1">
                                        <div class="col-md-5">人員</div>
                                        <div class="col-md-2">次數</div>
                                        <div class="col-md-2">金額</div>
                                        <div class="col-md-3 text-center">操作</div>
                                    </div>
                                    <div id="evening-receive-container">
                                        @forelse($eveningReceiveItems as $index => $item)
                                            <div class="person-row mb-2"
                                                data-evening-receive-index="{{ $index }}">
                                                <div class="row align-items-center g-2">
                                                    <div class="col-md-5">
                                                        <label class="form-label d-md-none">人員</label>
                                                        <select class="form-control"
                                                            name="evening_receive[{{ $index }}][person]"
                                                            data-toggle="select">
                                                            <option value="">請選擇人員</option>
                                                            @foreach ($users ?? [] as $user)
                                                                <option value="{{ $user->id }}"
                                                                    {{ $item->receive_person_id == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">次數</label>
                                                        <input type="number" class="form-control"
                                                            name="evening_receive[{{ $index }}][count]"
                                                            min="0" value="{{ $item->count ?? 1 }}"
                                                            onchange="calculateRowAmount(this, 'evening_receive', {{ $index }})"
                                                            oninput="calculateRowAmount(this, 'evening_receive', {{ $index }})">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">金額</label>
                                                        <input type="text" class="form-control text-end" id="evening_receive_amount_{{ $index }}" readonly>
                                                    </div>
                                                    <div class="col-md-3 text-end text-md-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="removeEveningReceive(this)">
                                                            <i class="fe-trash-2 me-1"></i>移除
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="person-row mb-2" data-evening-receive-index="0">
                                                <div class="row align-items-center g-2">
                                                    <div class="col-md-5">
                                                        <label class="form-label d-md-none">人員</label>
                                                        <select class="form-control" name="evening_receive[0][person]"
                                                            data-toggle="select">
                                                            <option value="">請選擇人員</option>
                                                            @foreach ($users ?? [] as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">次數</label>
                                                        <input type="number" class="form-control"
                                                            name="evening_receive[0][count]" min="0"
                                                            value="1"
                                                            onchange="calculateRowAmount(this, 'evening_receive', 0)"
                                                            oninput="calculateRowAmount(this, 'evening_receive', 0)">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">金額</label>
                                                        <input type="text" class="form-control text-end" id="evening_receive_amount_0" readonly>
                                                    </div>
                                                    <div class="col-md-3 text-end text-md-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="removeEveningReceive(this)">
                                                            <i class="fe-trash-2 me-1"></i>移除
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="addEveningReceive()">
                                        <i class="fe-plus me-1"></i>新增接件人員
                                    </button>
                                </div>
                            </div>

                            </div>

                            <!-- 2. 夜間加成區段 -->
                                <div class="tab-pane fade" id="tab-night" role="tabpanel" aria-labelledby="tab-night-tab">
                            <div class="category-section">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="category-title mb-0">
                                        <i class="fe-star me-2"></i>夜間加成
                                    </h5>
                                    <div class="text-end">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="night_is_typhoon"
                                                value="1" id="night_is_typhoon"
                                                {{ $increase->night_is_typhoon ? 'checked' : '' }}
                                                onchange="updateAllAmounts()">
                                            <label class="form-check-label fw-bold" for="night_is_typhoon">
                                                <span class="badge bg-warning text-dark">天災</span>
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="night_is_newyear"
                                                value="1" id="night_is_newyear"
                                                {{ $increase->night_is_newyear ? 'checked' : '' }}
                                                onchange="updateAllAmounts()">
                                            <label class="form-check-label fw-bold" for="night_is_newyear">
                                                <span class="badge bg-danger text-white">過年</span>
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                            夜間：電話${{ number_format($increaseSettings['night']->phone_bonus ?? 100, 0) }}、接件${{ number_format($increaseSettings['night']->receive_bonus ?? 500, 0) }} | 
                                            天災：電話+${{ number_format($increaseSettings['typhoon']->phone_bonus ?? 100, 0) }}、接件+${{ number_format($increaseSettings['typhoon']->receive_bonus ?? 500, 0) }} | 
                                            過年：電話+${{ number_format($increaseSettings['newyear']->phone_bonus ?? 100, 0) }}、接件+${{ number_format($increaseSettings['newyear']->receive_bonus ?? 500, 0) }}
                                        </small>
                                    </div>
                                </div>

                                <!-- 電話人員區塊 -->
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">
                                        <i class="fe-phone me-1"></i>電話人員 <small class="text-muted">(固定${{ number_format($increaseSettings['night']->phone_bonus ?? 100, 0) }}/次)</small>
                                    </h6>
                                    <div class="row text-muted fw-bold d-none d-md-flex px-2 mb-1">
                                        <div class="col-md-5">人員</div>
                                        <div class="col-md-2">次數</div>
                                        <div class="col-md-2">金額</div>
                                        <div class="col-md-3 text-center">操作</div>
                                    </div>
                                    <div id="night-phone-container">
                                        @forelse($nightPhoneItems as $index => $item)
                                            <div class="person-row mb-2" data-night-phone-index="{{ $index }}">
                                                <div class="row align-items-center g-2">
                                                    <div class="col-md-5">
                                                        <label class="form-label d-md-none">人員</label>
                                                        <select class="form-control"
                                                            name="night_phone[{{ $index }}][person]"
                                                            data-toggle="select">
                                                            <option value="">請選擇人員</option>
                                                            @foreach ($users ?? [] as $user)
                                                                <option value="{{ $user->id }}"
                                                                    {{ $item->phone_person_id == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">次數</label>
                                                        <input type="number" class="form-control"
                                                            name="night_phone[{{ $index }}][count]" min="0"
                                                            value="{{ $item->count ?? 1 }}"
                                                            onchange="calculateRowAmount(this, 'night_phone', {{ $index }})"
                                                            oninput="calculateRowAmount(this, 'night_phone', {{ $index }})">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">金額</label>
                                                        <input type="text" class="form-control text-end" id="night_phone_amount_{{ $index }}" readonly>
                                                    </div>
                                                    <div class="col-md-3 text-end text-md-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="removeNightPhone(this)">
                                                            <i class="fe-trash-2 me-1"></i>移除
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="person-row mb-2" data-night-phone-index="0">
                                                <div class="row align-items-center g-2">
                                                    <div class="col-md-5">
                                                        <label class="form-label d-md-none">人員</label>
                                                        <select class="form-control" name="night_phone[0][person]"
                                                            data-toggle="select">
                                                            <option value="">請選擇人員</option>
                                                            @foreach ($users ?? [] as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">次數</label>
                                                        <input type="number" class="form-control"
                                                            name="night_phone[0][count]" min="0" value="1"
                                                            onchange="calculateRowAmount(this, 'night_phone', 0)"
                                                            oninput="calculateRowAmount(this, 'night_phone', 0)">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">金額</label>
                                                        <input type="text" class="form-control text-end" id="night_phone_amount_0" readonly>
                                                    </div>
                                                    <div class="col-md-3 text-end text-md-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="removeNightPhone(this)">
                                                            <i class="fe-trash-2 me-1"></i>移除
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="addNightPhone()">
                                        <i class="fe-plus me-1"></i>新增電話人員
                                    </button>
                                </div>

                                <!-- 接件人員區塊 -->
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">
                                        <i class="fe-user-check me-1"></i>接件人員 <small
                                            class="text-muted">(固定${{ number_format($increaseSettings['night']->receive_bonus ?? 500, 0) }}/次)</small>
                                    </h6>
                                    <div class="row text-muted fw-bold d-none d-md-flex px-2 mb-1">
                                        <div class="col-md-5">人員</div>
                                        <div class="col-md-2">次數</div>
                                        <div class="col-md-2">金額</div>
                                        <div class="col-md-3 text-center">操作</div>
                                    </div>
                                    <div id="night-receive-container">
                                        @forelse($nightReceiveItems as $index => $item)
                                            <div class="person-row mb-2" data-night-receive-index="{{ $index }}">
                                                <div class="row align-items-center g-2">
                                                    <div class="col-md-5">
                                                        <label class="form-label d-md-none">人員</label>
                                                        <select class="form-control"
                                                            name="night_receive[{{ $index }}][person]"
                                                            data-toggle="select">
                                                            <option value="">請選擇人員</option>
                                                            @foreach ($users ?? [] as $user)
                                                                <option value="{{ $user->id }}"
                                                                    {{ $item->receive_person_id == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">次數</label>
                                                        <input type="number" class="form-control"
                                                            name="night_receive[{{ $index }}][count]"
                                                            min="0" value="{{ $item->count ?? 1 }}"
                                                            onchange="calculateRowAmount(this, 'night_receive', {{ $index }})"
                                                            oninput="calculateRowAmount(this, 'night_receive', {{ $index }})">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">金額</label>
                                                        <input type="text" class="form-control text-end" id="night_receive_amount_{{ $index }}" readonly>
                                                    </div>
                                                    <div class="col-md-3 text-end text-md-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="removeNightReceive(this)">
                                                            <i class="fe-trash-2 me-1"></i>移除
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="person-row mb-2" data-night-receive-index="0">
                                                <div class="row align-items-center g-2">
                                                    <div class="col-md-5">
                                                        <label class="form-label d-md-none">人員</label>
                                                        <select class="form-control" name="night_receive[0][person]"
                                                            data-toggle="select">
                                                            <option value="">請選擇人員</option>
                                                            @foreach ($users ?? [] as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">次數</label>
                                                        <input type="number" class="form-control"
                                                            name="night_receive[0][count]" min="0" value="1"
                                                            onchange="calculateRowAmount(this, 'night_receive', 0)"
                                                            oninput="calculateRowAmount(this, 'night_receive', 0)">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label d-md-none">金額</label>
                                                        <input type="text" class="form-control text-end" id="night_receive_amount_0" readonly>
                                                    </div>
                                                    <div class="col-md-3 text-end text-md-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="removeNightReceive(this)">
                                                            <i class="fe-trash-2 me-1"></i>移除
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="addNightReceive()">
                                        <i class="fe-plus me-1"></i>新增接件人員
                                    </button>
                                </div>
                            </div>

                            </div>

                                <!-- 夜間開爐頁籤 -->
                                <div class="tab-pane fade" id="tab-furnace" role="tabpanel" aria-labelledby="tab-furnace-tab">
                            <div class="category-section">
                                <h5 class="category-title">
                                    <i class="fe-thermometer me-2"></i>夜間開爐
                                </h5>
                                <div id="furnace-container">
                                    @foreach ($furnaceItems as $index => $item)
                                        <div class="person-row" data-furnace-index="{{ $index }}">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="form-label">夜間開爐時段</label>
                                                    <select class="form-control"
                                                        name="furnace[{{ $index }}][time_slot_id]"
                                                        id="furnace_time_slot_{{ $index }}"
                                                        onchange="calculateFurnacePrice({{ $index }})">
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
                                                    <select class="form-control"
                                                        name="furnace[{{ $index }}][furnace_person]"
                                                        data-toggle="select">
                                                        <option value="">請選擇人員</option>
                                                        @foreach ($users ?? [] as $user)
                                                            <option value="{{ $user->id }}"
                                                                {{ $item->furnace_person_id == $user->id ? 'selected' : '' }}>
                                                                {{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">計算價格</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="text" class="form-control"
                                                            id="furnace_calculated_price_{{ $index }}"
                                                            value="{{ $item->total_amount }}" readonly>
                                                    </div>
                                                    <small class="text-muted">根據時段自動計算</small>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-12">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger remove-furnace"
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

                            </div>

                            <!-- 3. 加班費區段 -->
                                <div class="tab-pane fade" id="tab-overtime" role="tabpanel" aria-labelledby="tab-overtime-tab">
                            <div class="category-section">
                                <h5 class="category-title">
                                    <i class="fe-clock me-2"></i>加班費
                                </h5>
                                <div class="mb-4">
                                    <h6 class="text-muted mb-2">
                                        <i class="fe-users me-1"></i>當日出勤情況
                                    </h6>
                                    <div id="day-worklog-container" class="bg-white border rounded p-3">
                                        <div class="day-worklog-placeholder"><i class="fe-info me-2"></i>請先選擇加成日期以載入出勤資料。</div>
                                    </div>
                                </div>
                                <div id="overtime-container">
                                    @foreach($overtimeItems as $index => $item)
                                        <div class="person-row" data-overtime-index="{{ $index }}">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label class="form-label">加班記錄選擇</label>
                                                    <div id="overtime-records-container-{{ $index }}">
                                                        <div class="alert alert-info">
                                                            <i class="fe-loader me-2"></i>載入中...
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div id="overtime_edit_section_{{ $index }}"
                                                        style="{{ $item->overtime_record_id ? 'display: block;' : 'display: none;' }}">
                                                        <label class="form-label">加班詳細資料</label>
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="row mb-2">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label small">加班分鐘</label>
                                                                        <input type="number" class="form-control form-control-sm"
                                                                            name="overtime[{{ $index }}][minutes]"
                                                                            id="overtime_minutes_field_{{ $index }}" min="1"
                                                                            step="1"
                                                                            value="{{ $item->overtimeRecord->minutes ?? '' }}"
                                                                            onchange="calculateOvertimePayFromMinutes({{ $index }})">
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label class="form-label small">事由</label>
                                                                        <input type="text" class="form-control form-control-sm"
                                                                            name="overtime[{{ $index }}][reason]"
                                                                            id="overtime_reason_field_{{ $index }}"
                                                                            value="{{ $item->overtimeRecord->reason ?? '' }}"
                                                                            placeholder="請輸入加班事由">
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label class="form-label small">加班倍數統計</label>
                                                                        <div class="card bg-light">
                                                                            <div class="card-body p-2">
                                                                                <div class="row">
                                                                                    <div class="col-6">
                                                                                        <small class="text-primary">1.34倍：</small>
                                                                                        <span id="overtime_134_hours_{{ $index }}"
                                                                                            class="fw-bold">{{ $item->overtimeRecord->first_two_hours ?? 0 }}小時</span>
                                                                                    </div>
                                                                                    <div class="col-6">
                                                                                        <small class="text-success">1.67倍：</small>
                                                                                        <span id="overtime_167_hours_{{ $index }}"
                                                                                            class="fw-bold">{{ $item->overtimeRecord->remaining_hours ?? 0 }}小時</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <small class="text-muted">可自行調整加班資料</small>
                                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                                        onclick="saveOvertimeRecord({{ $index }})">
                                                                        <i class="fe-save me-1"></i>儲存變更
                                                                    </button>
                                                                </div>
                                                                @if ($item->overtimeRecord && $item->overtimeRecord->creator)
                                                                    <div class="mt-2">
                                                                        <small class="text-muted">由 <span
                                                                                class="fw-bold text-info">{{ $item->overtimeRecord->creator->name }}</span>
                                                                            新增</small>
                                                                    </div>
                                                                @endif
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
                                    @endforeach
                                </div>
                                <div class="d-flex gap-2">
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
                                                    <label class="form-label">加班人員<span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" id="manual_overtime_user"
                                                        name="manual_overtime_user">
                                                        <option value="">請選擇人員</option>
                                                        @foreach ($users ?? [] as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">加班分鐘<span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control"
                                                        id="manual_overtime_minutes" name="manual_overtime_minutes"
                                                        min="1" step="1" onchange="calculateManualOvertimeAmount()"
                                                        oninput="calculateManualOvertimeAmount()">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">事由</label>
                                                    <input type="text" class="form-control"
                                                        id="manual_overtime_reason" name="manual_overtime_reason"
                                                        placeholder="請輸入加班事由">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">加班倍數統計</label>
                                                    <div class="card bg-light">
                                                        <div class="card-body p-2">
                                                            <div class="row">
                                                                <div class="col-12 mb-1">
                                                                    <small class="text-primary">1.34倍：</small>
                                                                    <span id="manual_overtime_134_hours"
                                                                        class="fw-bold">0小時</span>
                                                                </div>
                                                                <div class="col-12">
                                                                    <small class="text-success">1.67倍：</small>
                                                                    <span id="manual_overtime_167_hours"
                                                                        class="fw-bold">0小時</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" class="btn btn-success me-2"
                                                        onclick="saveManualOvertimeRecord()">
                                                        <i class="fe-save me-1"></i>儲存
                                                    </button>
                                                    <button type="button" class="btn btn-secondary"
                                                        onclick="cancelManualOvertimeForm()">
                                                        <i class="fe-x me-1"></i>取消
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            </div> <!-- end tab-content -->

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
        // ========== 金額計算函數 ==========
        
        // 計算每行的金額
        function calculateRowAmount(input, category, index) {
            const count = parseInt(input.value) || 0;
            let unitPrice = 0;
            
            // 取得天災和過年的勾選狀態
            let isTyphoon = false;
            let isNewyear = false;
            
            if (category === 'day_phone' || category === 'day_receive') {
                const typhoonCheckbox = document.getElementById('day_is_typhoon');
                const newyearCheckbox = document.getElementById('day_is_newyear');
                isTyphoon = typhoonCheckbox ? typhoonCheckbox.checked : false;
                isNewyear = newyearCheckbox ? newyearCheckbox.checked : false;
            } else if (category === 'evening_phone' || category === 'evening_receive') {
                const typhoonCheckbox = document.getElementById('evening_is_typhoon');
                const newyearCheckbox = document.getElementById('evening_is_newyear');
                isTyphoon = typhoonCheckbox ? typhoonCheckbox.checked : false;
                isNewyear = newyearCheckbox ? newyearCheckbox.checked : false;
            } else if (category === 'night_phone' || category === 'night_receive') {
                const typhoonCheckbox = document.getElementById('night_is_typhoon');
                const newyearCheckbox = document.getElementById('night_is_newyear');
                isTyphoon = typhoonCheckbox ? typhoonCheckbox.checked : false;
                isNewyear = newyearCheckbox ? newyearCheckbox.checked : false;
            }
            
            // 計算單價（基礎金額 + 天災 + 過年）
            const increaseSettings = @json($increaseSettings);
            
            if (category === 'day_phone') {
                unitPrice = (increaseSettings.day && increaseSettings.day.phone_bonus) ? Number(increaseSettings.day.phone_bonus) : 0;
                if (isTyphoon) unitPrice += (increaseSettings.typhoon && increaseSettings.typhoon.phone_bonus) ? Number(increaseSettings.typhoon.phone_bonus) : 100;
                if (isNewyear) unitPrice += (increaseSettings.newyear && increaseSettings.newyear.phone_bonus) ? Number(increaseSettings.newyear.phone_bonus) : 100;
            } else if (category === 'day_receive') {
                unitPrice = (increaseSettings.day && increaseSettings.day.receive_bonus) ? Number(increaseSettings.day.receive_bonus) : 0;
                if (isTyphoon) unitPrice += (increaseSettings.typhoon && increaseSettings.typhoon.receive_bonus) ? Number(increaseSettings.typhoon.receive_bonus) : 500;
                if (isNewyear) unitPrice += (increaseSettings.newyear && increaseSettings.newyear.receive_bonus) ? Number(increaseSettings.newyear.receive_bonus) : 500;
            } else if (category === 'evening_phone') {
                unitPrice = (increaseSettings.evening && increaseSettings.evening.phone_bonus) ? Number(increaseSettings.evening.phone_bonus) : 50;
                if (isTyphoon) unitPrice += (increaseSettings.typhoon && increaseSettings.typhoon.phone_bonus) ? Number(increaseSettings.typhoon.phone_bonus) : 100;
                if (isNewyear) unitPrice += (increaseSettings.newyear && increaseSettings.newyear.phone_bonus) ? Number(increaseSettings.newyear.phone_bonus) : 100;
            } else if (category === 'evening_receive') {
                unitPrice = (increaseSettings.evening && increaseSettings.evening.receive_bonus) ? Number(increaseSettings.evening.receive_bonus) : 250;
                if (isTyphoon) unitPrice += (increaseSettings.typhoon && increaseSettings.typhoon.receive_bonus) ? Number(increaseSettings.typhoon.receive_bonus) : 500;
                if (isNewyear) unitPrice += (increaseSettings.newyear && increaseSettings.newyear.receive_bonus) ? Number(increaseSettings.newyear.receive_bonus) : 500;
            } else if (category === 'night_phone') {
                unitPrice = (increaseSettings.night && increaseSettings.night.phone_bonus) ? Number(increaseSettings.night.phone_bonus) : 100;
                if (isTyphoon) unitPrice += (increaseSettings.typhoon && increaseSettings.typhoon.phone_bonus) ? Number(increaseSettings.typhoon.phone_bonus) : 100;
                if (isNewyear) unitPrice += (increaseSettings.newyear && increaseSettings.newyear.phone_bonus) ? Number(increaseSettings.newyear.phone_bonus) : 100;
            } else if (category === 'night_receive') {
                unitPrice = (increaseSettings.night && increaseSettings.night.receive_bonus) ? Number(increaseSettings.night.receive_bonus) : 500;
                if (isTyphoon) unitPrice += (increaseSettings.typhoon && increaseSettings.typhoon.receive_bonus) ? Number(increaseSettings.typhoon.receive_bonus) : 500;
                if (isNewyear) unitPrice += (increaseSettings.newyear && increaseSettings.newyear.receive_bonus) ? Number(increaseSettings.newyear.receive_bonus) : 500;
            }
            
            // 計算總金額
            const totalAmount = unitPrice * count;
            
            // 更新金額顯示
            const amountField = document.getElementById(`${category}_amount_${index}`);
            if (amountField) {
                amountField.value = totalAmount;
            }
        }
        
        // 當天災/過年勾選改變時，重新計算所有行的金額
        function updateAllAmounts() {
            // 更新所有白天加成 - 電話人員
            document.querySelectorAll('[data-day-phone-index]').forEach((row) => {
                const index = row.getAttribute('data-day-phone-index');
                const input = row.querySelector('input[type="number"]');
                if (input) calculateRowAmount(input, 'day_phone', index);
            });
            
            // 更新所有白天加成 - 接件人員
            document.querySelectorAll('[data-day-receive-index]').forEach((row) => {
                const index = row.getAttribute('data-day-receive-index');
                const input = row.querySelector('input[type="number"]');
                if (input) calculateRowAmount(input, 'day_receive', index);
            });
            
            // 更新所有晚間加成 - 電話人員
            document.querySelectorAll('[data-evening-phone-index]').forEach((row) => {
                const index = row.getAttribute('data-evening-phone-index');
                const input = row.querySelector('input[type="number"]');
                if (input) calculateRowAmount(input, 'evening_phone', index);
            });
            
            // 更新所有晚間加成 - 接件人員
            document.querySelectorAll('[data-evening-receive-index]').forEach((row) => {
                const index = row.getAttribute('data-evening-receive-index');
                const input = row.querySelector('input[type="number"]');
                if (input) calculateRowAmount(input, 'evening_receive', index);
            });
            
            // 更新所有夜間加成 - 電話人員
            document.querySelectorAll('[data-night-phone-index]').forEach((row) => {
                const index = row.getAttribute('data-night-phone-index');
                const input = row.querySelector('input[type="number"]');
                if (input) calculateRowAmount(input, 'night_phone', index);
            });
            
            // 更新所有夜間加成 - 接件人員
            document.querySelectorAll('[data-night-receive-index]').forEach((row) => {
                const index = row.getAttribute('data-night-receive-index');
                const input = row.querySelector('input[type="number"]');
                if (input) calculateRowAmount(input, 'night_receive', index);
            });
        }

        // ========== 白天加成 ==========

        function addDayPhone() {
            const container = document.getElementById('day-phone-container');
            const existingRows = container.querySelectorAll('.person-row');
            const newIndex = existingRows.length ?
                Math.max(...Array.from(existingRows).map(row => parseInt(row.getAttribute('data-day-phone-index')) || 0)) + 1 :
                0;

            const newRow = document.createElement('div');
            newRow.className = 'person-row mb-2';
            newRow.setAttribute('data-day-phone-index', newIndex);

            newRow.innerHTML = `
                <div class="row align-items-center g-2">
                    <div class="col-md-5">
                        <label class="form-label d-md-none">人員</label>
                        <select class="form-control" name="day_phone[${newIndex}][person]" data-toggle="select">
                            <option value="">請選擇人員</option>
                            @foreach ($users ?? [] as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-md-none">次數</label>
                        <input type="number" class="form-control" name="day_phone[${newIndex}][count]" min="0" value="1"
                               onchange="calculateRowAmount(this, 'day_phone', ${newIndex})"
                               oninput="calculateRowAmount(this, 'day_phone', ${newIndex})">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-md-none">金額</label>
                        <input type="text" class="form-control text-end" id="day_phone_amount_${newIndex}" readonly>
                    </div>
                    <div class="col-md-3 text-end text-md-center">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDayPhone(this)">
                            <i class="fe-trash-2 me-1"></i>移除
                        </button>
                    </div>
                </div>
            `;

            container.appendChild(newRow);
            $(newRow).find('select[data-toggle="select"]').select2();

            setTimeout(() => {
                const input = newRow.querySelector('input[type="number"]');
                if (input) calculateRowAmount(input, 'day_phone', newIndex);
            }, 50);
        }

        function removeDayPhone(button) {
            button.closest('.person-row')?.remove();
        }

        function addDayReceive() {
            const container = document.getElementById('day-receive-container');
            const existingRows = container.querySelectorAll('.person-row');
            const newIndex = existingRows.length ?
                Math.max(...Array.from(existingRows).map(row => parseInt(row.getAttribute('data-day-receive-index')) || 0)) + 1 :
                0;

            const newRow = document.createElement('div');
            newRow.className = 'person-row mb-2';
            newRow.setAttribute('data-day-receive-index', newIndex);

            newRow.innerHTML = `
                <div class="row align-items-center g-2">
                    <div class="col-md-5">
                        <label class="form-label d-md-none">人員</label>
                        <select class="form-control" name="day_receive[${newIndex}][person]" data-toggle="select">
                            <option value="">請選擇人員</option>
                            @foreach ($users ?? [] as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-md-none">次數</label>
                        <input type="number" class="form-control" name="day_receive[${newIndex}][count]" min="0" value="1"
                               onchange="calculateRowAmount(this, 'day_receive', ${newIndex})"
                               oninput="calculateRowAmount(this, 'day_receive', ${newIndex})">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-md-none">金額</label>
                        <input type="text" class="form-control text-end" id="day_receive_amount_${newIndex}" readonly>
                    </div>
                    <div class="col-md-3 text-end text-md-center">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDayReceive(this)">
                            <i class="fe-trash-2 me-1"></i>移除
                        </button>
                    </div>
                </div>
            `;

            container.appendChild(newRow);
            $(newRow).find('select[data-toggle="select"]').select2();

            setTimeout(() => {
                const input = newRow.querySelector('input[type="number"]');
                if (input) calculateRowAmount(input, 'day_receive', newIndex);
            }, 50);
        }

        function removeDayReceive(button) {
            button.closest('.person-row')?.remove();
        }

        // ========== 晚間加成 - 電話人員 ==========

        function addEveningPhone() {
            const container = document.getElementById('evening-phone-container');
            const existingRows = container.querySelectorAll('.person-row');
            const newIndex = existingRows.length ?
                Math.max(...Array.from(existingRows).map(row => parseInt(row.getAttribute('data-evening-phone-index')) ||
                    0)) + 1 :
                0;

            const newRow = document.createElement('div');
            newRow.className = 'person-row mb-2';
            newRow.setAttribute('data-evening-phone-index', newIndex);

            newRow.innerHTML = `
                <div class="row align-items-center g-2">
                    <div class="col-md-5">
                        <label class="form-label d-md-none">人員</label>
                        <select class="form-control" name="evening_phone[${newIndex}][person]" data-toggle="select">
                            <option value="">請選擇人員</option>
                            @foreach ($users ?? [] as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-md-none">次數</label>
                        <input type="number" class="form-control" name="evening_phone[${newIndex}][count]" min="0" value="1" 
                               onchange="calculateRowAmount(this, 'evening_phone', ${newIndex})"
                               oninput="calculateRowAmount(this, 'evening_phone', ${newIndex})">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-md-none">金額</label>
                        <input type="text" class="form-control text-end" id="evening_phone_amount_${newIndex}" readonly>
                    </div>
                    <div class="col-md-3 text-end text-md-center">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEveningPhone(this)">
                            <i class="fe-trash-2 me-1"></i>移除
                        </button>
                    </div>
                </div>
            `;

            container.appendChild(newRow);
            $(newRow).find('select[data-toggle="select"]').select2();
            
            // 計算新行的金額
            setTimeout(() => {
                const input = newRow.querySelector('input[type="number"]');
                if (input) calculateRowAmount(input, 'evening_phone', newIndex);
            }, 50);
        }

        function removeEveningPhone(button) {
            button.closest('.person-row')?.remove();
        }

        // ========== 晚間加成 - 接件人員 ==========

        function addEveningReceive() {
            const container = document.getElementById('evening-receive-container');
            const existingRows = container.querySelectorAll('.person-row');
            const newIndex = existingRows.length ?
                Math.max(...Array.from(existingRows).map(row => parseInt(row.getAttribute('data-evening-receive-index')) ||
                    0)) + 1 :
                0;

            const newRow = document.createElement('div');
            newRow.className = 'person-row mb-2';
            newRow.setAttribute('data-evening-receive-index', newIndex);

            newRow.innerHTML = `
                <div class="row align-items-center g-2">
                    <div class="col-md-5">
                        <label class="form-label d-md-none">人員</label>
                        <select class="form-control" name="evening_receive[${newIndex}][person]" data-toggle="select">
                            <option value="">請選擇人員</option>
                            @foreach ($users ?? [] as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-md-none">次數</label>
                        <input type="number" class="form-control" name="evening_receive[${newIndex}][count]" min="0" value="1"
                               onchange="calculateRowAmount(this, 'evening_receive', ${newIndex})"
                               oninput="calculateRowAmount(this, 'evening_receive', ${newIndex})">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-md-none">金額</label>
                        <input type="text" class="form-control text-end" id="evening_receive_amount_${newIndex}" readonly>
                    </div>
                    <div class="col-md-3 text-end text-md-center">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEveningReceive(this)">
                            <i class="fe-trash-2 me-1"></i>移除
                        </button>
                    </div>
                </div>
            `;

            container.appendChild(newRow);
            $(newRow).find('select[data-toggle="select"]').select2();
            
            // 計算新行的金額
            setTimeout(() => {
                const input = newRow.querySelector('input[type="number"]');
                if (input) calculateRowAmount(input, 'evening_receive', newIndex);
            }, 50);
        }

        function removeEveningReceive(button) {
            button.closest('.person-row')?.remove();
        }

        // ========== 夜間加成 - 電話人員 ==========

        function addNightPhone() {
            const container = document.getElementById('night-phone-container');
            const existingRows = container.querySelectorAll('.person-row');
            const newIndex = existingRows.length ?
                Math.max(...Array.from(existingRows).map(row => parseInt(row.getAttribute('data-night-phone-index')) ||
                0)) + 1 :
                0;

            const newRow = document.createElement('div');
            newRow.className = 'person-row mb-2';
            newRow.setAttribute('data-night-phone-index', newIndex);

            newRow.innerHTML = `
                <div class="row align-items-center g-2">
                    <div class="col-md-5">
                        <label class="form-label d-md-none">人員</label>
                        <select class="form-control" name="night_phone[${newIndex}][person]" data-toggle="select">
                            <option value="">請選擇人員</option>
                            @foreach ($users ?? [] as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-md-none">次數</label>
                        <input type="number" class="form-control" name="night_phone[${newIndex}][count]" min="0" value="1"
                               onchange="calculateRowAmount(this, 'night_phone', ${newIndex})"
                               oninput="calculateRowAmount(this, 'night_phone', ${newIndex})">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-md-none">金額</label>
                        <input type="text" class="form-control text-end" id="night_phone_amount_${newIndex}" readonly>
                    </div>
                    <div class="col-md-3 text-end text-md-center">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeNightPhone(this)">
                            <i class="fe-trash-2 me-1"></i>移除
                        </button>
                    </div>
                </div>
            `;

            container.appendChild(newRow);
            $(newRow).find('select[data-toggle="select"]').select2();
            
            // 計算新行的金額
            setTimeout(() => {
                const input = newRow.querySelector('input[type="number"]');
                if (input) calculateRowAmount(input, 'night_phone', newIndex);
            }, 50);
        }

        function removeNightPhone(button) {
            button.closest('.person-row')?.remove();
        }

        // ========== 夜間加成 - 接件人員 ==========

        function addNightReceive() {
            const container = document.getElementById('night-receive-container');
            const existingRows = container.querySelectorAll('.person-row');
            const newIndex = existingRows.length ?
                Math.max(...Array.from(existingRows).map(row => parseInt(row.getAttribute('data-night-receive-index')) ||
                    0)) + 1 :
                0;

            const newRow = document.createElement('div');
            newRow.className = 'person-row mb-2';
            newRow.setAttribute('data-night-receive-index', newIndex);

            newRow.innerHTML = `
                <div class="row align-items-center g-2">
                    <div class="col-md-5">
                        <label class="form-label d-md-none">人員</label>
                        <select class="form-control" name="night_receive[${newIndex}][person]" data-toggle="select">
                            <option value="">請選擇人員</option>
                            @foreach ($users ?? [] as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-md-none">次數</label>
                        <input type="number" class="form-control" name="night_receive[${newIndex}][count]" min="0" value="1"
                               onchange="calculateRowAmount(this, 'night_receive', ${newIndex})"
                               oninput="calculateRowAmount(this, 'night_receive', ${newIndex})">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-md-none">金額</label>
                        <input type="text" class="form-control text-end" id="night_receive_amount_${newIndex}" readonly>
                    </div>
                    <div class="col-md-3 text-end text-md-center">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeNightReceive(this)">
                            <i class="fe-trash-2 me-1"></i>移除
                        </button>
                    </div>
                </div>
            `;

            container.appendChild(newRow);
            $(newRow).find('select[data-toggle="select"]').select2();
            
            // 計算新行的金額
            setTimeout(() => {
                const input = newRow.querySelector('input[type="number"]');
                if (input) calculateRowAmount(input, 'night_receive', newIndex);
            }, 50);
        }

        function removeNightReceive(button) {
            button.closest('.person-row')?.remove();
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
                                     <label class="form-label small">加班倍數統計</label>
                                     <div class="card bg-light">
                                         <div class="card-body p-2">
                                             <div class="row">
                                                 <div class="col-6">
                                                     <small class="text-primary">1.34倍：</small>
                                                     <span id="overtime_134_hours_${newIndex}" class="fw-bold">0小時</span>
                                                 </div>
                                                 <div class="col-6">
                                                     <small class="text-success">1.67倍：</small>
                                                     <span id="overtime_167_hours_${newIndex}" class="fw-bold">0小時</span>
                                                 </div>
                                             </div>
                                         </div>
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
                             <div class="mt-2" id="overtime_created_by_${newIndex}" style="display: none;">
                                 <small class="text-muted">由 <span class="fw-bold text-info" id="overtime_created_by_name_${newIndex}"></span> 新增</small>
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


        // 移除夜間開爐
        function removeFurnace(button) {
            const container = document.getElementById('furnace-container');
            button.closest('.person-row')?.remove();
            const rows = container.querySelectorAll('.person-row');
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

        // 移除加班費
        function removeOvertime(button) {
            const container = document.getElementById('overtime-container');
            button.closest('.person-row')?.remove();
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
                        onchange="toggleOvertimeEditSection(${index})">
                    <option value="">選擇加班人員</option>`;

            records.forEach((record, recordIndex) => {
                html += `
                    <option value="${record.id}" 
                            data-formatted-hours="${record.formatted_hours}"
                            data-user-name="${record.user_name}"
                            data-minutes="${record.minutes}"
                            data-reason="${record.reason || ''}"
                            data-created-by-name="${record.created_by_name || '未知人員'}">
                        ${record.user_name} - ${record.formatted_hours}
                    </option>`;
            });

            html += `</select>`;

            container.innerHTML = html;
        }

        // 切換加班費編輯區段
        function toggleOvertimeEditSection(index) {
            const selectElement = document.getElementById(`overtime_record_select_${index}`);
            const editSection = document.getElementById(`overtime_edit_section_${index}`);

            if (selectElement && selectElement.value) {
                // 有選擇記錄時，顯示編輯區段
                if (editSection) {
                    editSection.style.display = 'block';
                }

                // 取得選中的選項資料並填入表單
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const minutes = selectedOption.getAttribute('data-minutes');
                const reason = selectedOption.getAttribute('data-reason');
                const createdByName = selectedOption.getAttribute('data-created-by-name');

                // 填入表單資料
                const minutesField = document.getElementById(`overtime_minutes_field_${index}`);
                const reasonField = document.getElementById(`overtime_reason_field_${index}`);

                if (minutesField) {
                    minutesField.value = minutes;
                    minutesField.setAttribute('data-original-minutes', minutes);
                    // 觸發小時數計算
                    calculateOvertimePayFromMinutes(index);
                }
                if (reasonField) {
                    reasonField.value = reason;
                    reasonField.setAttribute('data-original-reason', reason);
                }

                // 顯示「由誰新增」資訊
                const createdByDiv = document.getElementById(`overtime_created_by_${index}`);
                const createdByNameSpan = document.getElementById(`overtime_created_by_name_${index}`);
                if (createdByDiv && createdByNameSpan && createdByName) {
                    createdByNameSpan.textContent = createdByName;
                    createdByDiv.style.display = 'block';
                }
            } else {
                // 沒有選擇記錄時，隱藏編輯區段
                if (editSection) {
                    editSection.style.display = 'none';
                }

                // 清空表單資料
                const minutesField = document.getElementById(`overtime_minutes_field_${index}`);
                const reasonField = document.getElementById(`overtime_reason_field_${index}`);

                if (minutesField) {
                    minutesField.value = '';
                }
                if (reasonField) {
                    reasonField.value = '';
                }

                // 隱藏「由誰新增」資訊
                const createdByDiv = document.getElementById(`overtime_created_by_${index}`);
                if (createdByDiv) {
                    createdByDiv.style.display = 'none';
                }

                // 重置小時數顯示
                updateOvertimeHoursDisplay(index, 0, 0);
            }
        }

        // 從分鐘計算加班小時數統計
        function calculateOvertimePayFromMinutes(index) {
            const minutesField = document.getElementById(`overtime_minutes_field_${index}`);

            if (!minutesField) return;

            const minutes = parseInt(minutesField.value) || 0;
            if (minutes <= 0) {
                updateOvertimeHoursDisplay(index, 0, 0);
                return;
            }

            // 計算加班小時數（使用與 OvertimeRecord 模型相同的邏輯）
            const totalHours = minutes / 60;
            let firstTwoHours = 0;
            let remainingHours = 0;

            if (totalHours <= 2) {
                firstTwoHours = totalHours;
            } else {
                firstTwoHours = 2;
                remainingHours = totalHours - 2;
            }

            updateOvertimeHoursDisplay(index, firstTwoHours, remainingHours);
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
                        selectedOption.textContent =
                            `${selectedOption.getAttribute('data-user-name')} - ${formattedHours} ($${overtimePay})`;

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

        // 更新加班小時數顯示
        function updateOvertimeHoursDisplay(index, firstTwoHours, remainingHours) {
            const hours134Element = document.getElementById(`overtime_134_hours_${index}`);
            const hours167Element = document.getElementById(`overtime_167_hours_${index}`);

            if (hours134Element) {
                hours134Element.textContent = formatHours(firstTwoHours);
            }
            if (hours167Element) {
                hours167Element.textContent = formatHours(remainingHours);
            }
        }

        // 格式化小時顯示
        function formatHours(hours) {
            if (hours === 0) return '0小時';

            const wholeHours = Math.floor(hours);
            const minutes = Math.round((hours - wholeHours) * 60);

            if (minutes === 0) {
                return `${wholeHours}小時`;
            } else {
                return `${wholeHours}小時${minutes}分鐘`;
            }
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
                updateManualOvertimeHoursDisplay(0, 0);
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
            updateManualOvertimeHoursDisplay(0, 0);
        }

        // 計算手動加班記錄的小時數統計
        function calculateManualOvertimeAmount() {
            const minutesInput = document.getElementById('manual_overtime_minutes');

            if (!minutesInput) return;

            const minutes = parseInt(minutesInput.value) || 0;
            if (minutes <= 0) {
                updateManualOvertimeHoursDisplay(0, 0);
                return;
            }

            // 計算加班小時數（使用與 OvertimeRecord 模型相同的邏輯）
            const totalHours = minutes / 60;
            let firstTwoHours = 0;
            let remainingHours = 0;

            if (totalHours <= 2) {
                firstTwoHours = totalHours;
            } else {
                firstTwoHours = 2;
                remainingHours = totalHours - 2;
            }

            updateManualOvertimeHoursDisplay(firstTwoHours, remainingHours);
        }

        // 更新手動加班小時數顯示
        function updateManualOvertimeHoursDisplay(firstTwoHours, remainingHours) {
            const hours134Element = document.getElementById('manual_overtime_134_hours');
            const hours167Element = document.getElementById('manual_overtime_167_hours');

            if (hours134Element) {
                hours134Element.textContent = formatHours(firstTwoHours);
            }
            if (hours167Element) {
                hours167Element.textContent = formatHours(remainingHours);
            }
        }

        // 儲存手動新增加班記錄
        function saveManualOvertimeRecord() {
            const userId = document.getElementById('manual_overtime_user').value;
            const minutes = document.getElementById('manual_overtime_minutes').value;
            const reason = document.getElementById('manual_overtime_reason').value;
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
                        reason: reason
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
            addOvertime();

            const containers = document.querySelectorAll('[id^="overtime-records-container-"]');
            const lastIndex = containers.length - 1;
            addRecordToOvertimeSection(recordData, lastIndex);
        }

        // 將記錄添加到指定的加班費區段
        function addRecordToOvertimeSection(recordData, index, attempt = 0) {
            const selectElement = document.getElementById(`overtime_record_select_${index}`);

            if (!selectElement) {
                if (attempt > 20) {
                    console.error(`無法在區段 ${index} 建立加班記錄下拉選單。`);
                    return;
                }
                setTimeout(() => addRecordToOvertimeSection(recordData, index, attempt + 1), 150);
                return;
            }

            let option = selectElement.querySelector(`option[value="${recordData.id}"]`);
            if (!option) {
                option = document.createElement('option');
                option.value = recordData.id;
                selectElement.appendChild(option);
            }

            option.setAttribute('data-formatted-hours', recordData.formatted_hours);
            option.setAttribute('data-user-name', recordData.user_name);
            option.setAttribute('data-minutes', recordData.minutes);
            option.setAttribute('data-reason', recordData.reason || '');
            option.setAttribute('data-created-by-name', recordData.created_by_name || '未知人員');
            option.textContent = `${recordData.user_name} - ${recordData.formatted_hours}`;

            selectElement.value = recordData.id;

            const recordField = document.getElementById(`overtime_record_field_${index}`);
            if (recordField) {
                recordField.value = recordData.id;
            }

            toggleOvertimeEditSection(index);
        }

        // 監聽日期變化
        document.querySelector('input[name="increase_date"]').addEventListener('change', function() {
            // 載入所有加班費區段的加班記錄
            loadAllOvertimeRecords();
            loadDayWorkLogs();
        });

        // 載入所有加班費區段的加班記錄
        function loadAllOvertimeRecords() {
            const overtimeContainers = document.querySelectorAll('[id^="overtime-records-container-"]');
            overtimeContainers.forEach(container => {
                const index = container.id.match(/overtime-records-container-(\d+)/)[1];
                loadOvertimeRecordsForIndex(index);
            });
        }

        // 載入加班記錄並選中現有記錄
        function loadOvertimeRecordsForIndexWithSelection(index, selectedRecordId) {
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

                        // 如果有指定的記錄ID，選中它
                        if (selectedRecordId) {
                            setTimeout(() => {
                                const selectElement = document.getElementById(
                                `overtime_record_select_${index}`);
                                if (selectElement) {
                                    selectElement.value = selectedRecordId;
                                    toggleOvertimeEditSection(index);
                                }
                            }, 100);
                        }
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

        // 初始化頁面載入時的 select2
        document.addEventListener('DOMContentLoaded', function() {
            // 初始化所有預設的 select2 下拉選單
            $('select[data-toggle="select"]').select2();

            // 初始化所有金額顯示
            updateAllAmounts();

            // 初始化所有現有夜間開爐項目的價格計算
            document.querySelectorAll('#furnace-container .person-row').forEach((row, index) => {
                calculateFurnacePrice(index);
            });

            // 載入所有加班費區段的加班記錄並選中現有記錄
            const increaseDate = document.querySelector('input[name="increase_date"]').value;
            if (increaseDate) {
                // 載入現有的加班費項目
                @foreach ($overtimeItems as $index => $item)
                    @if ($item->overtime_record_id)
                        loadOvertimeRecordsForIndexWithSelection({{ $index }},
                            {{ $item->overtime_record_id }});
                    @else
                        loadOvertimeRecordsForIndex({{ $index }});
                    @endif
                @endforeach
                loadDayWorkLogs();
            }
        });

        // 表單驗證
        document.getElementById('increaseForm').addEventListener('submit', function(e) {
            // 這裡可以添加表單驗證邏輯
            console.log('表單提交');
        });

        const workLogContainer = document.getElementById('day-worklog-container');

        function loadDayWorkLogs() {
            if (!workLogContainer) {
                return;
            }

            const increaseDate = document.querySelector('input[name="increase_date"]').value;

            if (!increaseDate) {
                workLogContainer.innerHTML =
                    '<div class="day-worklog-placeholder"><i class="fe-info me-2"></i>請先選擇加成日期以載入出勤資料。</div>';
                return;
            }

            workLogContainer.innerHTML = '<div class="day-worklog-loading"><i class="fe-loader me-2"></i>資料載入中...</div>';

            fetch(`/increase/day-works/${increaseDate}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderDayWorkLogs(data.records || []);
                    } else {
                        workLogContainer.innerHTML =
                            `<div class="day-worklog-error"><i class="fe-alert-triangle me-2"></i>${data.message || '取得出勤資料時發生錯誤'}</div>`;
                    }
                })
                .catch(error => {
                    console.error('loadDayWorkLogs error:', error);
                    workLogContainer.innerHTML =
                        '<div class="day-worklog-error"><i class="fe-alert-triangle me-2"></i>取得出勤資料失敗，請稍後再試。</div>';
                });
        }

        function renderDayWorkLogs(records) {
            if (!records.length) {
                workLogContainer.innerHTML =
                    '<div class="day-worklog-placeholder"><i class="fe-info me-2"></i>當日尚無出勤紀錄。</div>';
                return;
            }

            let html = '<div class="day-worklog-grid">';
            records.forEach(record => {
                const worktime = record.worktime_formatted ?
                    `<span class="badge bg-success text-white day-worklog-badge">${record.worktime_formatted}</span>` :
                    '<span class="badge bg-warning text-dark day-worklog-badge">未打卡</span>';
                const dutytime = record.dutytime_formatted ?
                    `<span class="badge bg-primary text-white day-worklog-badge">${record.dutytime_formatted}</span>` :
                    '<span class="badge bg-warning text-dark day-worklog-badge">未打卡</span>';

                html += `
                    <div class="day-worklog-card">
                        <div class="day-worklog-name">${record.user_name}</div>
                        <div class="day-worklog-row">
                            <span class="day-worklog-label">上班</span>
                            ${worktime}
                        </div>
                        <div class="day-worklog-row">
                            <span class="day-worklog-label">下班</span>
                            ${dutytime}
                        </div>
                    </div>
                `;
            });
            html += '</div>';

            workLogContainer.innerHTML = html;
        }
    </script>
@endsection

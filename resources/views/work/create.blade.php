@extends('layouts.vertical', ['page_title' => '批次新增出勤記錄'])

@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
<!-- third party css end -->
<style>
    .work-record-row {
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }
    .work-record-row:last-child {
        border-bottom: none;
    }
    .remove-row-btn {
        min-width: 100px;
    }
    .duplicate-warning {
        border: 2px solid #dc3545 !important;
        background-color: #fff5f5 !important;
    }
    .db-exists-warning {
        border: 2px solid #ff9800 !important;
        background-color: #fff8e1 !important;
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">用戶管理</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.work.index', $user->id) }}">出勤列表</a></li>
                        <li class="breadcrumb-item active">批次新增出勤記錄</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ $user->name }} - 批次新增出勤記錄</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information-outline me-2"></i>
                        <strong>提示：</strong>您可以一次新增多筆出勤記錄，系統會自動檢測重複的日期。
                    </div>

                    <form action="{{ route('user.work.batch.store', $user->id) }}" method="POST" id="batchWorkForm">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">

                        <div id="workRecordsContainer">
                            <!-- 初始記錄行 -->
                            <div class="work-record-row" data-row-index="0">
                                <div class="row align-items-end">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">日期<span class="text-danger">*</span></label>
                                            <input type="date" class="form-control work-date" name="records[0][date]" required data-user-id="{{ $user->id }}">
                                            <small class="text-danger date-error" style="display: none;"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label class="form-label">上班時間<span class="text-danger">*</span></label>
                                            <input type="time" class="form-control work-start-time" name="records[0][worktime]" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label class="form-label">下班時間<span class="text-danger">*</span></label>
                                            <input type="time" class="form-control work-end-time" name="records[0][dutytime]" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">備註</label>
                                            <input type="text" class="form-control" name="records[0][remark]" placeholder="選填">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="mb-3">
                                            <button type="button" class="btn btn-danger btn-sm remove-row-btn" onclick="removeRow(this)" style="display: none;">
                                                <i class="mdi mdi-trash-can-outline"></i> 刪除
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="records[0][status]" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2 mb-3">
                            <div class="col-12">
                                <button type="button" class="btn btn-success waves-effect waves-light" onclick="addNewRow()">
                                    <i class="mdi mdi-plus-circle me-1"></i>新增一筆記錄
                                </button>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-warning" id="duplicateAlert" style="display: none;">
                                    <i class="mdi mdi-alert me-2"></i>
                                    <strong>警告：</strong><span id="duplicateMessage"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary waves-effect waves-light m-1" id="submitBtn">
                                    <i class="fe-check-circle me-1"></i>批次新增
                                </button>
                                <a href="{{ route('user.work.index', $user->id) }}" class="btn btn-secondary waves-effect waves-light m-1">
                                    <i class="fe-x me-1"></i>取消返回
                                </a>
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
<script src="{{asset('assets/libs/select2/select2.min.js')}}"></script>
<script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>
<!-- third party js ends -->

<script>
let rowIndex = 1;
const userId = {{ $user->id }};

// 新增記錄行
function addNewRow() {
    const container = document.getElementById('workRecordsContainer');
    const newRow = document.createElement('div');
    newRow.className = 'work-record-row';
    newRow.setAttribute('data-row-index', rowIndex);
    
    newRow.innerHTML = `
        <div class="row align-items-end">
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">日期<span class="text-danger">*</span></label>
                    <input type="date" class="form-control work-date" name="records[${rowIndex}][date]" required data-user-id="${userId}">
                    <small class="text-danger date-error" style="display: none;"></small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">上班時間<span class="text-danger">*</span></label>
                    <input type="time" class="form-control work-start-time" name="records[${rowIndex}][worktime]" required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">下班時間<span class="text-danger">*</span></label>
                    <input type="time" class="form-control work-end-time" name="records[${rowIndex}][dutytime]" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">備註</label>
                    <input type="text" class="form-control" name="records[${rowIndex}][remark]" placeholder="選填">
                </div>
            </div>
            <div class="col-md-1">
                <div class="mb-3">
                    <button type="button" class="btn btn-danger btn-sm remove-row-btn" onclick="removeRow(this)">
                        <i class="mdi mdi-trash-can-outline"></i> 刪除
                    </button>
                </div>
            </div>
            <input type="hidden" name="records[${rowIndex}][status]" value="0">
        </div>
    `;
    
    container.appendChild(newRow);
    rowIndex++;
    
    // 更新刪除按鈕顯示
    updateRemoveButtons();
    
    // 為新增的日期輸入框添加事件監聽
    const dateInputs = newRow.querySelectorAll('.work-date');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            checkDateExists(this);
        });
    });
}

// 刪除記錄行
function removeRow(button) {
    const row = button.closest('.work-record-row');
    row.remove();
    updateRemoveButtons();
    checkAllDates();
}

// 更新刪除按鈕的顯示狀態
function updateRemoveButtons() {
    const rows = document.querySelectorAll('.work-record-row');
    const removeButtons = document.querySelectorAll('.remove-row-btn');
    
    if (rows.length === 1) {
        removeButtons[0].style.display = 'none';
    } else {
        removeButtons.forEach(btn => {
            btn.style.display = 'block';
        });
    }
}

// 檢查單一日期是否存在（AJAX檢查資料庫 + 前端重複檢查）
async function checkDateExists(inputElement) {
    const date = inputElement.value;
    const userId = inputElement.getAttribute('data-user-id');
    const errorMsg = inputElement.parentElement.querySelector('.date-error');
    
    // 清除之前的錯誤標記
    inputElement.classList.remove('duplicate-warning', 'db-exists-warning');
    errorMsg.style.display = 'none';
    errorMsg.textContent = '';
    
    if (!date) {
        checkAllDates();
        return;
    }
    
    // 1. 先檢查前端是否有重複
    const allDateInputs = document.querySelectorAll('.work-date');
    let duplicateCount = 0;
    allDateInputs.forEach(input => {
        if (input.value === date) {
            duplicateCount++;
        }
    });
    
    if (duplicateCount > 1) {
        inputElement.classList.add('duplicate-warning');
        errorMsg.textContent = '此日期在表單中重複';
        errorMsg.style.display = 'block';
        checkAllDates();
        return;
    }
    
    // 2. 再檢查資料庫是否已存在
    try {
        const response = await fetch(`/work/check-date?user_id=${userId}&date=${date}`);
        const data = await response.json();
        
        if (data.exists) {
            inputElement.classList.add('db-exists-warning');
            errorMsg.textContent = '此日期已存在於資料庫中';
            errorMsg.style.display = 'block';
        }
    } catch (error) {
        console.error('檢查日期時發生錯誤:', error);
    }
    
    checkAllDates();
}

// 檢查所有日期並更新提交按鈕狀態
function checkAllDates() {
    const dateInputs = document.querySelectorAll('.work-date');
    let hasDuplicate = false;
    let hasDbExists = false;
    const duplicateMessage = document.getElementById('duplicateMessage');
    const duplicateAlert = document.getElementById('duplicateAlert');
    const submitBtn = document.getElementById('submitBtn');
    
    dateInputs.forEach(input => {
        if (input.classList.contains('duplicate-warning')) {
            hasDuplicate = true;
        }
        if (input.classList.contains('db-exists-warning')) {
            hasDbExists = true;
        }
    });
    
    if (hasDuplicate || hasDbExists) {
        let messages = [];
        if (hasDuplicate) messages.push('偵測到重複的日期');
        if (hasDbExists) messages.push('偵測到已存在於資料庫的日期');
        
        duplicateMessage.textContent = messages.join('，') + '，請修改後再提交。';
        duplicateAlert.style.display = 'block';
        submitBtn.disabled = true;
    } else {
        duplicateAlert.style.display = 'none';
        submitBtn.disabled = false;
    }
}

// 表單提交驗證
document.getElementById('batchWorkForm').addEventListener('submit', function(e) {
    // 檢查是否有重複或已存在的日期
    const dateInputs = document.querySelectorAll('.work-date');
    let hasError = false;
    
    dateInputs.forEach(input => {
        if (input.classList.contains('duplicate-warning') || input.classList.contains('db-exists-warning')) {
            hasError = true;
        }
    });
    
    if (hasError) {
        e.preventDefault();
        alert('請修正重複或已存在的日期後再提交！');
        return false;
    }
    
    // 驗證上下班時間
    const rows = document.querySelectorAll('.work-record-row');
    
    rows.forEach(row => {
        const startTime = row.querySelector('.work-start-time').value;
        const endTime = row.querySelector('.work-end-time').value;
        const date = row.querySelector('.work-date').value;
        
        if (date && startTime && endTime) {
            const start = new Date(`${date} ${startTime}`);
            const end = new Date(`${date} ${endTime}`);
            
            // 如果下班時間早於上班時間，可能是跨日
            if (end < start) {
                const confirmMsg = `日期 ${date} 的下班時間早於上班時間，這可能是跨日班次。確定要繼續嗎？`;
                if (!confirm(confirmMsg)) {
                    hasError = true;
                }
            }
        }
    });
    
    if (hasError) {
        e.preventDefault();
        return false;
    }
});

// 為所有日期輸入框添加事件監聽
document.addEventListener('DOMContentLoaded', function() {
    const dateInputs = document.querySelectorAll('.work-date');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            checkDateExists(this);
        });
    });
});
</script>
@endsection

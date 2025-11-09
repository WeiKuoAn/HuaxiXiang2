@extends('layouts.vertical', ['page_title' => '新增流程'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">人事管理</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('flow.index') }}">流程管理</a></li>
                            <li class="breadcrumb-item active">新增流程</li>
                        </ol>
                    </div>
                    <h4 class="page-title">新增流程</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('flow.store') }}" method="POST" id="workflow-form">
                            @csrf
                            
                            <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">基本資訊</h5>
                            
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="mb-3">
                                        <label class="form-label">流程類別 <span class="text-danger">*</span></label>
                                        <select class="form-select" name="category" required onchange="updateCategoryInfo()">
                                            <option value="">請選擇流程類別</option>
                                            <option value="leave" {{ old('category') == 'leave' ? 'selected' : '' }}>
                                                <i class="mdi mdi-calendar-clock me-1"></i>請假管理
                                            </option>
                                            <option value="discipline" {{ old('category') == 'discipline' ? 'selected' : '' }}>
                                                <i class="mdi mdi-account-alert me-1"></i>懲處管理
                                            </option>
                                        </select>
                                        @error('category')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="mb-3">
                                        <label class="form-label">適用職稱 <span class="text-danger">*</span></label>
                                        <select class="form-select" name="job_id" required onchange="updateJobInfo()">
                                            <option value="">請選擇職稱</option>
                                            @foreach ($jobs as $job)
                                                <option value="{{ $job->id }}" {{ old('job_id') == $job->id ? 'selected' : '' }}>
                                                    {{ $job->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('job_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="mb-3">
                                        <label class="form-label">流程名稱 <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" 
                                               value="{{ old('name') }}" placeholder="請輸入流程名稱" required>
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <label class="form-label">流程描述</label>
                                        <textarea class="form-control" name="description" rows="3" 
                                                  placeholder="請輸入流程描述">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <label class="form-label">狀態 <span class="text-danger">*</span></label>
                                        <select class="form-select" name="is_active" required>
                                            <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>啟用</option>
                                            <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>停用</option>
                                        </select>
                                        @error('is_active')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <h5 class="text-uppercase bg-light p-2 mt-4 mb-3">流程關卡設定</h5>
                            
                            <div id="steps-container">
                                <div class="step-item border p-3 mb-3 rounded">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">審核人員 <span class="text-danger">*</span></label>
                                                <select class="form-select" name="steps[0][approver_user_id]" required>
                                                    <option value="">請選擇審核人員</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}" {{ old('steps.0.approver_user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('steps.0.approver_user_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">順序 <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="steps[0][step_order]" 
                                                       value="{{ old('steps.0.step_order', 1) }}" min="1" required>
                                                @error('steps.0.step_order')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="mb-3">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="button" class="btn btn-danger btn-sm d-block" 
                                                        onclick="removeStep(this)">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-info waves-effect waves-light" 
                                            onclick="addStep()">
                                        <i class="mdi mdi-plus me-1"></i>新增關卡
                                    </button>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-2">
                                        <i class="mdi mdi-content-save me-1"></i>儲存流程
                                    </button>
                                    <a href="{{ route('flow.index') }}" 
                                       class="btn btn-secondary waves-effect waves-light">
                                        <i class="mdi mdi-arrow-left me-1"></i>返回列表
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">設定說明</h5>
                        
                        <div class="alert alert-info">
                            <h6><i class="mdi mdi-information me-2"></i>流程設定說明</h6>
                            <ul class="mb-0">
                                <li>流程類別：選擇請假管理或懲處管理</li>
                                <li>適用職稱：選擇此流程適用的職稱</li>
                                <li>流程名稱：建議使用清楚易懂的名稱</li>
                                <li>關卡順序：數字越小越先審核</li>
                                <li>審核人員：每個關卡需要指定一位審核人</li>
                                <li>狀態：啟用後該職稱的申請才會套用此流程</li>
                            </ul>
                        </div>

                        <div class="alert alert-warning">
                            <h6><i class="mdi mdi-alert me-2"></i>注意事項</h6>
                            <ul class="mb-0">
                                <li>每個職稱的每個類別只能設定一個審核流程</li>
                                <li>至少需要設定一個審核關卡</li>
                                <li>關卡順序不能重複</li>
                                <li>修改流程會影響該職稱的新申請</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- container -->
@endsection

@section('script')
<script>
let stepIndex = 1;

// 表單提交前的檢查
document.getElementById('workflow-form').addEventListener('submit', function(e) {
    console.log('表單提交檢查開始');
    
    // 檢查是否有選擇審核人員
    const approverSelects = document.querySelectorAll('select[name*="[approver_user_id]"]');
    let hasEmptyApprover = false;
    
    approverSelects.forEach(function(select, index) {
        if (!select.value) {
            hasEmptyApprover = true;
            select.style.borderColor = 'red';
            alert('請為第 ' + (index + 1) + ' 個關卡選擇審核人員');
        } else {
            select.style.borderColor = '';
        }
    });
    
    if (hasEmptyApprover) {
        e.preventDefault();
        console.log('審核人員檢查失敗');
        return false;
    }
    
    // 檢查順序是否重複
    const orderInputs = document.querySelectorAll('input[name*="[step_order]"]');
    const orders = Array.from(orderInputs).map(input => parseInt(input.value));
    const uniqueOrders = [...new Set(orders)];
    
    if (orders.length !== uniqueOrders.length) {
        e.preventDefault();
        alert('關卡順序不能重複，請檢查順序設定');
        console.log('順序檢查失敗');
        return false;
    }
    
    console.log('表單檢查通過，準備提交');
});

function addStep() {
    const container = document.getElementById('steps-container');
    const stepHtml = `
        <div class="step-item border p-3 mb-3 rounded">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">審核人員 <span class="text-danger">*</span></label>
                        <select class="form-select" name="steps[${stepIndex}][approver_user_id]" required>
                            <option value="">請選擇審核人員</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">順序 <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="steps[${stepIndex}][step_order]" 
                               value="${stepIndex + 1}" min="1" required>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm d-block" 
                                onclick="removeStep(this)">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', stepHtml);
    stepIndex++;
}

function removeStep(button) {
    const stepItem = button.closest('.step-item');
    stepItem.remove();
    
    // 重新計算順序
    updateStepOrder();
}

function updateStepOrder() {
    const stepItems = document.querySelectorAll('.step-item');
    stepItems.forEach((item, index) => {
        const orderInput = item.querySelector('input[name*="[step_order]"]');
        if (orderInput) {
            orderInput.value = index + 1;
        }
    });
}

function updateCategoryInfo() {
    const categorySelect = document.querySelector('select[name="category"]');
    const jobSelect = document.querySelector('select[name="job_id"]');
    const selectedCategory = categorySelect.value;
    const selectedJobName = jobSelect.options[jobSelect.selectedIndex] ? jobSelect.options[jobSelect.selectedIndex].text : '';
    
    updateFormFields(selectedCategory, selectedJobName);
}

function updateJobInfo() {
    const categorySelect = document.querySelector('select[name="category"]');
    const jobSelect = document.querySelector('select[name="job_id"]');
    const selectedCategory = categorySelect.value;
    const selectedJobName = jobSelect.options[jobSelect.selectedIndex] ? jobSelect.options[jobSelect.selectedIndex].text : '';
    
    updateFormFields(selectedCategory, selectedJobName);
}

function updateFormFields(category, jobName) {
    const nameInput = document.querySelector('input[name="name"]');
    const descriptionInput = document.querySelector('textarea[name="description"]');
    
    if (category && jobName) {
        let categoryText = '';
        let descriptionText = '';
        
        if (category === 'leave') {
            categoryText = '請假';
            descriptionText = '請假申請審核流程';
        } else if (category === 'discipline') {
            categoryText = '懲處';
            descriptionText = '懲處申請審核流程';
        }
        
        // 總是更新欄位值，不管原本是否有值
        nameInput.value = jobName + ' ' + categoryText + '審核流程';
        descriptionInput.value = jobName + ' ' + descriptionText;
    }
}
</script>
@endsection

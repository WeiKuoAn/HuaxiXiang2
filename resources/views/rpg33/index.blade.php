@extends('layouts.vertical', ["page_title"=> "多法會客戶查詢"])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">報表管理</a></li>
                        <li class="breadcrumb-item active">多法會客戶查詢</li>
                    </ol>
                </div>
                <h4 class="page-title">多法會客戶查詢</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <form class="d-flex flex-wrap align-items-center" id="myForm" action="{{ route('rpg33') }}" method="GET">
                                <div class="me-3">
                                    <label class="form-label">選擇法會（可複選）</label>
                                    <p class="text-muted small">請選擇要查詢的法會，系統會找出同時報名所有選中法會的客戶</p>
                                </div>
                                <div class="me-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAllPujas()">全選</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearAllPujas()">清除</button>
                                </div>
                                <div class="me-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                </div>
                                <div class="me-3">
                                    <a href="{{ route('rpg33.export',request()->input()) }}">
                                         <button type="button" class="btn btn-primary waves-effect waves-light me-1"><i class="fe-download me-1"></i>匯出</button>
                                     </a>
                                </div>
                                
                                <!-- 隱藏的 checkbox 容器 -->
                                <div id="checkboxContainer" style="display: none;">
                                    @foreach($pujasByYear as $year => $pujas)
                                        @foreach($pujas as $puja)
                                            <input type="checkbox" name="puja_ids[]" value="{{ $puja->id }}" id="hidden_puja_{{ $puja->id }}">
                                        @endforeach
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </div> <!-- end row -->
                </div>
            </div> <!-- end card -->
        </div> <!-- end col-->
    </div>

    <!-- 法會選擇區域 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">法會列表（按年份分組）</h5>
                    <div class="row">
                        @foreach($pujasByYear as $year => $pujas)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">{{ $year }} 年</h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($pujas as $puja)
                                            <div class="form-check">
                                                                                                 <input class="form-check-input" type="checkbox" 
                                                        name="puja_ids[]" 
                                                        value="{{ $puja->id }}" 
                                                        id="puja_{{ $puja->id }}"
                                                        @if($request->has('puja_ids') && in_array($puja->id, $request->puja_ids)) checked @endif
                                                        onchange="saveCheckboxState()">
                                                <label class="form-check-label" for="puja_{{ $puja->id }}">
                                                    {{ $puja->name }} ({{ $puja->date }})
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 搜尋結果 -->
    @if($request->has('puja_ids') && !empty($request->puja_ids))
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">搜尋結果</h5>
                        <p class="text-muted">同時報名以下法會的客戶：</p>
                        <ul class="list-unstyled">
                            @foreach($request->puja_ids as $puja_id)
                                @php
                                    $puja = \App\Models\Puja::find($puja_id);
                                @endphp
                                @if($puja)
                                    <li><i class="mdi mdi-check-circle text-success me-2"></i>{{ $puja->name }} ({{ $puja->date }})</li>
                                @endif
                            @endforeach
                        </ul>
                        
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead class="table-light">
                                    <tr align="center">
                                        <th scope="col">編號</th>
                                        <th scope="col">客戶姓名</th>
                                        <th scope="col">寶貝名稱</th>
                                        <th scope="col">客戶電話</th>
                                        {{-- <th scope="col">客戶地址</th> --}}
                                        {{-- <th scope="col">報名法會數量</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($customers->count() > 0)
                                        @foreach($customers as $key => $customer)
                                            <tr align="center">
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $customer->name }}</td>
                                                <td>
                                                    @if($customerPets->has($customer->id) && $customerPets->get($customer->id)->count() > 0)
                                                        {{ $customerPets->get($customer->id)->implode('、') }}
                                                    @else
                                                        <span class="text-muted">無</span>
                                                    @endif
                                                </td>
                                                <td>{{ $customer->mobile }}</td>
                                                {{-- <td>{{ $customer->address }}</td> --}}
                                                {{-- <td>{{ count($request->puja_ids) }}</td> --}}
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">沒有找到同時報名所有選中法會的客戶</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div> <!-- container -->

<script>
// 保存 checkbox 狀態到 localStorage
function saveCheckboxState() {
    var checkboxes = document.querySelectorAll('input[name="puja_ids[]"]');
    var checkedValues = [];
    
    checkboxes.forEach(function(checkbox) {
        if (checkbox.checked) {
            checkedValues.push(checkbox.value);
        }
    });
    
    localStorage.setItem('rpg33_puja_ids', JSON.stringify(checkedValues));
    
    // 同步隱藏 checkbox 的狀態
    var hiddenCheckboxes = document.querySelectorAll('#checkboxContainer input[name="puja_ids[]"]');
    hiddenCheckboxes.forEach(function(checkbox) {
        checkbox.checked = checkedValues.includes(checkbox.value);
    });
}

// 從 localStorage 載入 checkbox 狀態
function loadCheckboxState() {
    var savedState = localStorage.getItem('rpg33_puja_ids');
    if (savedState) {
        var checkedValues = JSON.parse(savedState);
        var checkboxes = document.querySelectorAll('input[name="puja_ids[]"]');
        
        checkboxes.forEach(function(checkbox) {
            if (checkedValues.includes(checkbox.value)) {
                checkbox.checked = true;
            }
        });
        
        // 同步隱藏 checkbox 的狀態
        var hiddenCheckboxes = document.querySelectorAll('#checkboxContainer input[name="puja_ids[]"]');
        hiddenCheckboxes.forEach(function(checkbox) {
            checkbox.checked = checkedValues.includes(checkbox.value);
        });
    }
}

// 清除保存的 checkbox 狀態
function clearSavedState() {
    localStorage.removeItem('rpg33_puja_ids');
}

function selectAllPujas() {
    var checkboxes = document.querySelectorAll('input[name="puja_ids[]"]');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = true;
    });
    saveCheckboxState(); // 保存狀態
}

function clearAllPujas() {
    var checkboxes = document.querySelectorAll('input[name="puja_ids[]"]');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = false;
    });
    clearSavedState(); // 清除保存的狀態
}

// 同步 checkbox 狀態
document.addEventListener('DOMContentLoaded', function() {
    // 載入之前保存的狀態
    loadCheckboxState();
    
    // 為每個可見的 checkbox 添加事件監聽器
    var visibleCheckboxes = document.querySelectorAll('.form-check-input');
    visibleCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var hiddenCheckbox = document.getElementById('hidden_' + this.id);
            if (hiddenCheckbox) {
                hiddenCheckbox.checked = this.checked;
            }
        });
    });
    
    // 初始化隱藏 checkbox 的狀態
    visibleCheckboxes.forEach(function(checkbox) {
        var hiddenCheckbox = document.getElementById('hidden_' + this.id);
        if (hiddenCheckbox) {
            hiddenCheckbox.checked = checkbox.checked;
        }
    });
    
    // 為表單提交添加事件監聽器，確保提交時保存狀態
    var form = document.getElementById('myForm');
    if (form) {
        form.addEventListener('submit', function() {
            saveCheckboxState();
        });
    }
});
</script>
@endsection

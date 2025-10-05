@extends('layouts.vertical', ['page_title' => '紀念日管理'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">紀念日管理</a></li>
                            <li class="breadcrumb-item active">紀念日列表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">紀念日管理</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('memorial.dates') }}" method="GET">
                            <div class="row g-3">
                                <!-- 第一行：基本搜尋 -->
                                <div class="col-md-4">
                                    <label for="customer_name" class="form-label">客戶名稱</label>
                                    <input type="search" class="form-control" id="customer_name"
                                        name="customer_name" placeholder="請輸入客戶名稱" value="{{ $request->customer_name }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="pet_name" class="form-label">寶貝名稱</label>
                                    <input type="search" class="form-control" id="pet_name"
                                        name="pet_name" placeholder="請輸入寶貝名稱" value="{{ $request->pet_name }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="sale_on" class="form-label">業務單號</label>
                                    <input type="search" class="form-control" id="sale_on"
                                        name="sale_on" placeholder="請輸入業務單號" value="{{ $request->sale_on }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="memorial_type" class="form-label">紀念日類型</label>
                                    <select class="form-control" id="memorial_type" name="memorial_type">
                                        <option value="">全部紀念日</option>
                                        <option value="seventh" {{ $request->memorial_type == 'seventh' ? 'selected' : '' }}>頭七</option>
                                        <option value="forty_ninth" {{ $request->memorial_type == 'forty_ninth' ? 'selected' : '' }}>四十九日</option>
                                        <option value="hundredth" {{ $request->memorial_type == 'hundredth' ? 'selected' : '' }}>百日</option>
                                        <option value="anniversary" {{ $request->memorial_type == 'anniversary' ? 'selected' : '' }}>對年</option>
                                    </select>
                                </div>
                                
                                <!-- 第二行：日期範圍和預約狀態 -->
                                <div class="col-md-4">
                                    <label for="date_from" class="form-label">開始搜尋日期</label>
                                    <input type="date" class="form-control" id="date_from"
                                        name="date_from" value="{{ $request->date_from }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="date_to" class="form-label">結束搜尋日期</label>
                                    <input type="date" class="form-control" id="date_to"
                                        name="date_to" value="{{ $request->date_to }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="reservation_status" class="form-label">曾經預約過</label>
                                    <select class="form-control" id="reservation_status" name="reservation_status">
                                        <option value="">全部狀態</option>
                                        <option value="reserved" {{ $request->reservation_status == 'reserved' ? 'selected' : '' }}>已預約</option>
                                        <option value="not_reserved" {{ $request->reservation_status == 'not_reserved' ? 'selected' : '' }}>未預約</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="btn-group w-100" role="group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="mdi mdi-magnify"></i> 搜尋
                                        </button>
                                        <a href="{{ route('memorial.dates') }}" class="btn btn-outline-secondary">
                                            <i class="mdi mdi-refresh"></i> 重置
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($memorialDates->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th class="text-center" style="width: 120px;">業務單號</th>
                                            <th class="text-center" style="width: 100px;">客戶名稱</th>
                                            <th class="text-center" style="width: 100px;">寶貝名稱</th>
                                            <th class="text-center" style="width: 100px;">往生日期</th>
                                            <th class="text-center" style="width: 100px;">頭七</th>
                                            <th class="text-center" style="width: 100px;">四十九日</th>
                                            <th class="text-center" style="width: 100px;">百日</th>
                                            <th class="text-center" style="width: 100px;">對年</th>
                                            <th class="text-center" style="width: 150px;">預約狀態</th>
                                            <th class="text-center" style="width: 120px;">備註</th>
                                            <th class="text-center" style="width: 100px;">軌跡</th>
                                            <th class="text-center" style="width: 80px;">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($memorialDates as $memorial)
                                            <tr>
                                                <td class="text-center fw-medium">{{ $memorial->sale->sale_on ?? '-' }}</td>
                                                <td class="text-center">{{ $memorial->sale->cust_name->name ?? '-' }}</td>
                                                <td class="text-center">{{ $memorial->sale->pet_name ?? '-' }}</td>
                                                <td class="text-center">{{ $memorial->sale->death_date ? \Carbon\Carbon::parse($memorial->sale->death_date)->format('Y/m/d') : '-' }}</td>
                                                <td class="text-center">
                                                    @if($memorial->seventh_day)
                                                        <span class="badge bg-info text-white">{{ \Carbon\Carbon::parse($memorial->seventh_day)->format('Y/m/d') }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary text-white">{{ \Carbon\Carbon::parse($memorial->forty_ninth_day)->format('Y/m/d') }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning text-dark">{{ \Carbon\Carbon::parse($memorial->hundredth_day)->format('Y/m/d') }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if($memorial->anniversary_day)
                                                        <span class="badge bg-danger text-white">{{ \Carbon\Carbon::parse($memorial->anniversary_day)->format('Y/m/d') }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $reservations = [];
                                                        if($memorial->seventh_reserved && $memorial->seventh_reserved_at) {
                                                            $reservations[] = [
                                                                'name' => '頭七',
                                                                'date' => $memorial->seventh_reserved_at->format('Y/m/d')
                                                            ];
                                                        }
                                                        if($memorial->forty_ninth_reserved && $memorial->forty_ninth_reserved_at) {
                                                            $reservations[] = [
                                                                'name' => '四十九日',
                                                                'date' => $memorial->forty_ninth_reserved_at->format('Y/m/d')
                                                            ];
                                                        }
                                                        if($memorial->hundredth_reserved && $memorial->hundredth_reserved_at) {
                                                            $reservations[] = [
                                                                'name' => '百日',
                                                                'date' => $memorial->hundredth_reserved_at->format('Y/m/d')
                                                            ];
                                                        }
                                                        if($memorial->anniversary_reserved && $memorial->anniversary_reserved_at) {
                                                            $reservations[] = [
                                                                'name' => '對年',
                                                                'date' => $memorial->anniversary_reserved_at->format('Y/m/d')
                                                            ];
                                                        }
                                                    @endphp
                                                    @if(count($reservations) > 0)
                                                        <div class="d-flex flex-wrap gap-1 justify-content-center">
                                                            @foreach($reservations as $reservation)
                                                                <span class="badge bg-success text-white reservation-badge" 
                                                                      style="font-size: 0.7rem; padding: 0.25rem 0.5rem; cursor: help;"
                                                                      data-bs-toggle="tooltip" 
                                                                      data-bs-placement="top" 
                                                                      title="預約日期：{{ $reservation['date'] }}">
                                                                    {{ $reservation['name'] }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="badge bg-light text-muted border">未預約</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($memorial->notes)
                                                        <span class="text-truncate d-inline-block" style="max-width: 100px;" title="{{ $memorial->notes }}">
                                                            {{ $memorial->notes }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        // 只計算有實際變更的記錄
                                                        $logsWithChanges = $memorial->logs->filter(function($log) {
                                                            if (!$log->changes) return false;
                                                            $changes = is_string($log->changes) ? json_decode($log->changes, true) : $log->changes;
                                                            if (!is_array($changes)) return false;
                                                            
                                                            // 處理舊格式 {"after": {...}, "before": {...}}
                                                            if (isset($changes['after']) && isset($changes['before'])) {
                                                                $after = $changes['after'];
                                                                $before = $changes['before'];
                                                                
                                                                // 定義需要記錄的欄位
                                                                $trackableFields = [
                                                                    'seventh_day', 'seventh_reserved', 'seventh_reserved_at',
                                                                    'forty_ninth_day', 'forty_ninth_reserved', 'forty_ninth_reserved_at',
                                                                    'hundredth_day', 'hundredth_reserved', 'hundredth_reserved_at',
                                                                    'anniversary_day', 'anniversary_reserved', 'anniversary_reserved_at',
                                                                    'notes', 'general_notes'
                                                                ];
                                                                
                                                                // 檢查是否有實際變更
                                                                foreach ($trackableFields as $field) {
                                                                    if (isset($after[$field]) && isset($before[$field])) {
                                                                        $oldValue = $before[$field];
                                                                        $newValue = $after[$field];
                                                                        
                                                                        // 處理日期格式的比較
                                                                        if (strpos($field, '_day') !== false || strpos($field, '_at') !== false) {
                                                                            // 日期欄位：轉換為字串格式進行比較
                                                                            $oldValue = $oldValue ? \Carbon\Carbon::parse($oldValue)->format('Y-m-d') : null;
                                                                            $newValue = $newValue ? \Carbon\Carbon::parse($newValue)->format('Y-m-d') : null;
                                                                        }
                                                                        
                                                                        // 只有當值真正不同時才認為有變更
                                                                        if ($oldValue !== $newValue) {
                                                                            return true;
                                                                        }
                                                                    }
                                                                }
                                                                return false;
                                                            }
                                                            
                                                            // 處理新格式 {"field_name": {"old": "...", "new": "..."}}
                                                            $systemFields = ['created_at', 'updated_at', 'id', 'memorial_date_id'];
                                                            $filteredChanges = array_filter($changes, function($key) use ($systemFields) {
                                                                return !in_array($key, $systemFields);
                                                            }, ARRAY_FILTER_USE_KEY);
                                                            
                                                            return count($filteredChanges) > 0;
                                                        });
                                                    @endphp
                                                    @if($logsWithChanges->count() > 0)
                                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#logsModal{{ $memorial->id }}"
                                                                title="查看軌跡">
                                                            <i class="mdi mdi-history"></i>
                                                            <span class="badge bg-primary ms-1">{{ $logsWithChanges->count() }}</span>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">無</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('memorial.dates.edit', $memorial->id) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="編輯紀念日">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- 分頁 -->
                            <ul class="pagination pagination-rounded justify-content-end mb-0">
                                {{ $memorialDates->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
                            </ul>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="mdi mdi-calendar-heart-outline" style="font-size: 64px; color: #dee2e6;"></i>
                                </div>
                                <h4 class="text-muted mb-3">沒有找到符合條件的紀念日記錄</h4>
                                <p class="text-muted mb-4">請調整搜尋條件後重新搜尋，或檢查是否有相關的紀念日資料</p>
                                <a href="{{ route('memorial.dates') }}" class="btn btn-outline-primary">
                                    <i class="mdi mdi-refresh"></i> 重新載入
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- container -->

    <!-- 軌跡 Modal -->
    @foreach($memorialDates as $memorial)
        @if($memorial->logs->count() > 0)
            <div class="modal fade" id="logsModal{{ $memorial->id }}" tabindex="-1" aria-labelledby="logsModalLabel{{ $memorial->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="logsModalLabel{{ $memorial->id }}">
                                <i class="mdi mdi-history"></i> 軌跡記錄 - {{ $memorial->sale->cust_name->name ?? '未知客戶' }} ({{ $memorial->sale->pet_name ?? '未知寶貝' }})
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width: 120px;" class="text-center">時間</th>
                                            <th style="width: 120px;" class="text-center">人員</th>
                                            <th style="width: 80px;" class="text-center">操作</th>
                                            <th class="text-center">內容</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            // 只顯示有實際變更的記錄
                                            $filteredLogs = $memorial->logs->filter(function($log) {
                                                if (!$log->changes) return false;
                                                $changes = is_string($log->changes) ? json_decode($log->changes, true) : $log->changes;
                                                if (!is_array($changes)) return false;
                                                
                                                // 處理舊格式 {"after": {...}, "before": {...}}
                                                if (isset($changes['after']) && isset($changes['before'])) {
                                                    $after = $changes['after'];
                                                    $before = $changes['before'];
                                                    
                                                    // 定義需要記錄的欄位
                                                    $trackableFields = [
                                                        'seventh_day', 'seventh_reserved', 'seventh_reserved_at',
                                                        'forty_ninth_day', 'forty_ninth_reserved', 'forty_ninth_reserved_at',
                                                        'hundredth_day', 'hundredth_reserved', 'hundredth_reserved_at',
                                                        'anniversary_day', 'anniversary_reserved', 'anniversary_reserved_at',
                                                        'notes', 'general_notes'
                                                    ];
                                                    
                                                    // 檢查是否有實際變更
                                                    foreach ($trackableFields as $field) {
                                                        if (isset($after[$field]) && isset($before[$field])) {
                                                            $oldValue = $before[$field];
                                                            $newValue = $after[$field];
                                                            
                                                            // 處理日期格式的比較
                                                            if (strpos($field, '_day') !== false || strpos($field, '_at') !== false) {
                                                                // 日期欄位：轉換為字串格式進行比較
                                                                $oldValue = $oldValue ? \Carbon\Carbon::parse($oldValue)->format('Y-m-d') : null;
                                                                $newValue = $newValue ? \Carbon\Carbon::parse($newValue)->format('Y-m-d') : null;
                                                            }
                                                            
                                                            // 只有當值真正不同時才認為有變更
                                                            if ($oldValue !== $newValue) {
                                                                return true;
                                                            }
                                                        }
                                                    }
                                                    return false;
                                                }
                                                
                                                // 處理新格式 {"field_name": {"old": "...", "new": "..."}}
                                                $systemFields = ['created_at', 'updated_at', 'id', 'memorial_date_id'];
                                                $filteredChanges = array_filter($changes, function($key) use ($systemFields) {
                                                    return !in_array($key, $systemFields);
                                                }, ARRAY_FILTER_USE_KEY);
                                                
                                                return count($filteredChanges) > 0;
                                            })->sortByDesc('created_at');
                                        @endphp
                                        @foreach($filteredLogs as $log)
                                            <tr>
                                                <td class="text-center">
                                                    <div class="log-time">
                                                        <i class="mdi mdi-clock-outline text-muted"></i>
                                                        <br>
                                                        <small>{{ $log->created_at->format('Y/m/d') }}</small>
                                                        <br>
                                                        <small class="text-muted">{{ $log->created_at->format('H:i') }}</small>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="log-user">
                                                        <i class="mdi mdi-account text-primary"></i>
                                                        <br>
                                                        <small>
                                                            @if($log->user_id)
                                                                {{ $log->user->name ?? '使用者ID: ' . $log->user_id }}
                                                            @else
                                                                系統
                                                            @endif
                                                        </small>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $actionNames = [
                                                            'create' => '新增',
                                                            'update' => '編輯',
                                                            'delete' => '刪除'
                                                        ];
                                                        $actionName = $actionNames[$log->action] ?? ucfirst($log->action);
                                                    @endphp
                                                    <span class="badge bg-{{ $log->action === 'create' ? 'success' : ($log->action === 'update' ? 'primary' : 'danger') }}">
                                                        {{ $actionName }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($log->changes)
                                                        @php
                                                            $changes = is_string($log->changes) ? json_decode($log->changes, true) : $log->changes;
                                                            $filteredChanges = [];
                                                            
                                                            // 處理舊格式 {"after": {...}, "before": {...}}
                                                            if (isset($changes['after']) && isset($changes['before'])) {
                                                                $after = $changes['after'];
                                                                $before = $changes['before'];
                                                                
                                                                // 定義需要記錄的欄位
                                                                $trackableFields = [
                                                                    'seventh_day', 'seventh_reserved', 'seventh_reserved_at',
                                                                    'forty_ninth_day', 'forty_ninth_reserved', 'forty_ninth_reserved_at',
                                                                    'hundredth_day', 'hundredth_reserved', 'hundredth_reserved_at',
                                                                    'anniversary_day', 'anniversary_reserved', 'anniversary_reserved_at',
                                                                    'notes', 'general_notes'
                                                                ];
                                                                
                                                                // 轉換為新格式
                                                                foreach ($trackableFields as $field) {
                                                                    if (isset($after[$field]) && isset($before[$field])) {
                                                                        $oldValue = $before[$field];
                                                                        $newValue = $after[$field];
                                                                        
                                                                        // 處理日期格式的比較
                                                                        if (strpos($field, '_day') !== false || strpos($field, '_at') !== false) {
                                                                            // 日期欄位：轉換為字串格式進行比較
                                                                            $oldValue = $oldValue ? \Carbon\Carbon::parse($oldValue)->format('Y-m-d') : null;
                                                                            $newValue = $newValue ? \Carbon\Carbon::parse($newValue)->format('Y-m-d') : null;
                                                                        }
                                                                        
                                                                        // 只有當值真正不同時才記錄變更
                                                                        if ($oldValue !== $newValue) {
                                                                            $filteredChanges[$field] = [
                                                                                'old' => $before[$field],
                                                                                'new' => $after[$field]
                                                                            ];
                                                                        }
                                                                    }
                                                                }
                                                            } else {
                                                                // 處理新格式 {"field_name": {"old": "...", "new": "..."}}
                                                                $systemFields = ['created_at', 'updated_at', 'id', 'memorial_date_id'];
                                                                $filteredChanges = array_filter($changes, function($key) use ($systemFields) {
                                                                    return !in_array($key, $systemFields);
                                                                }, ARRAY_FILTER_USE_KEY);
                                                            }
                                                        @endphp
                                                        @if(count($filteredChanges) > 0)
                                                            <div class="changes-content">
                                                                @php
                                                                    $fieldNames = [
                                                                        'seventh_day' => '頭七日期',
                                                                        'seventh_reserved' => '頭七預約',
                                                                        'seventh_reserved_at' => '頭七預約日期',
                                                                        'forty_ninth_day' => '四十九日',
                                                                        'forty_ninth_reserved' => '四十九日預約',
                                                                        'forty_ninth_reserved_at' => '四十九日預約日期',
                                                                        'hundredth_day' => '百日',
                                                                        'hundredth_reserved' => '百日預約',
                                                                        'hundredth_reserved_at' => '百日預約日期',
                                                                        'anniversary_day' => '對年',
                                                                        'anniversary_reserved' => '對年預約',
                                                                        'anniversary_reserved_at' => '對年預約日期',
                                                                        'notes' => '備註',
                                                                        'general_notes' => '總備註'
                                                                    ];
                                                                @endphp
                                                                @foreach($filteredChanges as $field => $change)
                                                                    <div class="change-item">
                                                                        <span class="field-name">{{ $fieldNames[$field] ?? $field }}：</span>
                                                                        @if(isset($change['old']) && isset($change['new']) && $change['old'] !== null && $change['new'] !== null)
                                                                            @php
                                                                                $oldValue = $change['old'];
                                                                                if ($oldValue === null) $oldValue = '無';
                                                                                elseif ($oldValue === true) $oldValue = '是';
                                                                                elseif ($oldValue === false) $oldValue = '否';
                                                                                elseif (strpos($field, '_day') !== false || strpos($field, '_at') !== false) {
                                                                                    try {
                                                                                        $oldValue = \Carbon\Carbon::parse($oldValue)->format('Y/m/d');
                                                                                    } catch (\Exception $e) {
                                                                                        // 保持原值
                                                                                    }
                                                                                }
                                                                                
                                                                                $newValue = $change['new'];
                                                                                if ($newValue === null) $newValue = '無';
                                                                                elseif ($newValue === true) $newValue = '是';
                                                                                elseif ($newValue === false) $newValue = '否';
                                                                                elseif (strpos($field, '_day') !== false || strpos($field, '_at') !== false) {
                                                                                    try {
                                                                                        $newValue = \Carbon\Carbon::parse($newValue)->format('Y/m/d');
                                                                                    } catch (\Exception $e) {
                                                                                        // 保持原值
                                                                                    }
                                                                                }
                                                                            @endphp
                                                                            <span class="value-change">
                                                                                <span class="old-value">{{ $oldValue }}</span>
                                                                                <i class="mdi mdi-arrow-right mx-1 text-muted"></i>
                                                                                <span class="new-value">{{ $newValue }}</span>
                                                                            </span>
                                                                        @elseif(isset($change['new']) && $change['new'] !== null)
                                                                            @php
                                                                                $newValue = $change['new'];
                                                                                if ($newValue === null) $newValue = '無';
                                                                                elseif ($newValue === true) $newValue = '是';
                                                                                elseif ($newValue === false) $newValue = '否';
                                                                                elseif (strpos($field, '_day') !== false || strpos($field, '_at') !== false) {
                                                                                    try {
                                                                                        $newValue = \Carbon\Carbon::parse($newValue)->format('Y/m/d');
                                                                                    } catch (\Exception $e) {
                                                                                        // 保持原值
                                                                                    }
                                                                                }
                                                                            @endphp
                                                                            <span class="value-added">
                                                                                <i class="mdi mdi-plus text-success"></i>
                                                                                <span class="new-value">{{ $newValue }}</span>
                                                                            </span>
                                                                        @elseif(isset($change['old']) && $change['old'] !== null)
                                                                            @php
                                                                                $oldValue = $change['old'];
                                                                                if ($oldValue === null) $oldValue = '無';
                                                                                elseif ($oldValue === true) $oldValue = '是';
                                                                                elseif ($oldValue === false) $oldValue = '否';
                                                                                elseif (strpos($field, '_day') !== false || strpos($field, '_at') !== false) {
                                                                                    try {
                                                                                        $oldValue = \Carbon\Carbon::parse($oldValue)->format('Y/m/d');
                                                                                    } catch (\Exception $e) {
                                                                                        // 保持原值
                                                                                    }
                                                                                }
                                                                            @endphp
                                                                            <span class="value-removed">
                                                                                <i class="mdi mdi-minus text-danger"></i>
                                                                                <span class="old-value">{{ $oldValue }}</span>
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    @endif
                                                    
                                                    @if($log->notes)
                                                        <div class="log-notes mt-2">
                                                            <i class="mdi mdi-note-text text-warning"></i>
                                                            <small class="text-muted">{{ $log->notes }}</small>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection

@push('styles')
<style>
/* 分頁樣式 - 純 Bootstrap 樣式 */
.pagination {
    margin: 0;
}

.pagination .page-link {
    color: #007bff;
    background-color: #fff;
    border: 1px solid #dee2e6;
    padding: 0.5rem 0.75rem;
    margin: 0 2px;
    border-radius: 0.375rem;
    text-decoration: none;
}

.pagination .page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: #fff;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
    cursor: not-allowed;
}

/* 軌跡 Modal 樣式 - 表格設計 */
.log-time, .log-user {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}

.log-time i, .log-user i {
    font-size: 1.2rem;
}

.changes-content {
    font-size: 0.85rem;
    line-height: 1.4;
}

.change-item {
    margin-bottom: 6px;
    padding: 3px 0;
    border-bottom: 1px solid #f1f3f4;
}

.change-item:last-child {
    border-bottom: none;
}

.field-name {
    color: #495057;
    font-weight: 600;
    margin-right: 6px;
    display: inline-block;
    min-width: 80px;
}

.value-change {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.value-added, .value-removed {
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

.old-value {
    background-color: #f8d7da;
    color: #721c24;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.8rem;
    font-weight: 500;
}

.new-value {
    background-color: #d4edda;
    color: #155724;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.8rem;
    font-weight: 500;
}

.log-notes {
    margin-top: 8px;
    padding: 6px 10px;
    background: #fff3cd;
    border-radius: 4px;
    font-size: 0.8rem;
    border-left: 3px solid #ffc107;
}

/* 表格樣式優化 */
.table-responsive {
    max-height: 500px;
    overflow-y: auto;
}

.table th {
    font-size: 0.9rem;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    padding: 12px 8px;
}

.table td {
    vertical-align: top;
    font-size: 0.85rem;
    padding: 12px 8px;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.table-sm th, .table-sm td {
    padding: 8px;
}

/* 徽章樣式 */
.badge {
    font-size: 0.75rem;
    padding: 4px 8px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // 初始化 Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // 自動提交表單（可選）
    // $('#customer_name, #pet_name, #sale_on').on('input', function() {
    //     $(this).closest('form').submit();
    // });
});
</script>
@endpush

@extends('layouts.vertical', ['page_title' => '請假流程詳情'])

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
                            <li class="breadcrumb-item"><a href="{{ route('leaveworkflow.index') }}">請假流程管理</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('leaveworkflow.status') }}">流程狀態總覽</a></li>
                            <li class="breadcrumb-item active">流程詳情</li>
                        </ol>
                    </div>
                    <h4 class="page-title">請假流程詳情</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-8">
                <!-- 假單基本資訊 -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">假單資訊</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">申請人</label>
                                    <p class="form-control-plaintext">
                                        <i class="mdi mdi-account me-2 text-primary"></i>
                                        {{ $leaveRequest->user->name ?? '未知' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">假別</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $leaveRequest->leave->name ?? '未知假別' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">請假開始時間</label>
                                    <p class="form-control-plaintext">
                                        <i class="mdi mdi-calendar-start me-2 text-success"></i>
                                        {{ $leaveRequest->start_datetime }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">請假結束時間</label>
                                    <p class="form-control-plaintext">
                                        <i class="mdi mdi-calendar-end me-2 text-danger"></i>
                                        {{ $leaveRequest->end_datetime }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">請假時數</label>
                                    <p class="form-control-plaintext">
                                        {{ $leaveRequest->total }}
                                        @switch($leaveRequest->unit)
                                            @case('hour')
                                                小時
                                                @break
                                            @case('day')
                                                天
                                                @break
                                            @case('week')
                                                週
                                                @break
                                            @case('month')
                                                月
                                                @break
                                            @default
                                                {{ $leaveRequest->unit }}
                                        @endswitch
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">申請時間</label>
                                    <p class="form-control-plaintext">
                                        <i class="mdi mdi-clock-outline me-2 text-info"></i>
                                        {{ date('Y-m-d H:i:s', strtotime($leaveRequest->created_at)) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($leaveRequest->comment)
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">備註</label>
                                        <p class="form-control-plaintext">{{ $leaveRequest->comment }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($leaveRequest->file)
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">附件</label>
                                        <p class="form-control-plaintext">
                                            <a href="{{ $leaveRequest->file }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                <i class="mdi mdi-file-document me-1"></i>查看附件
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 流程進度 -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">流程進度</h5>
                        
                        @if($workflowSteps->count() > 0)
                            <div class="timeline">
                                @foreach($workflowSteps as $index => $step)
                                    @php
                                        $stepCheck = $leaveRequest->checks->where('check_user_id', $step->approver_user_id)->first();
                                        $isCompleted = $stepCheck && $stepCheck->state == 9;
                                        $isCurrent = !$isCompleted && $stepCheck && $stepCheck->state == 2;
                                        $isPending = !$stepCheck || ($stepCheck->state == 2 && !$isCurrent);
                                    @endphp
                                    
                                    <div class="timeline-item {{ $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'pending') }}">
                                        <div class="timeline-marker">
                                            @if($isCompleted)
                                                <i class="mdi mdi-check-circle text-success"></i>
                                            @elseif($isCurrent)
                                                <i class="mdi mdi-clock-outline text-warning"></i>
                                            @else
                                                <i class="mdi mdi-circle-outline text-muted"></i>
                                            @endif
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">
                                                {{ $step->step_name }}
                                                @if($isCompleted)
                                                    <span class="badge bg-success ms-2">已完成</span>
                                                @elseif($isCurrent)
                                                    <span class="badge bg-warning ms-2">進行中</span>
                                                @else
                                                    <span class="badge bg-secondary ms-2">待處理</span>
                                                @endif
                                            </h6>
                                            <p class="timeline-text">
                                                審核人：{{ $step->approver->name ?? '未指定' }}
                                            </p>
                                            @if($stepCheck)
                                                <div class="timeline-details">
                                                    <small class="text-muted">
                                                        審核時間：{{ $stepCheck->check_day ? date('Y-m-d H:i:s', strtotime($stepCheck->check_day)) : '未審核' }}
                                                    </small>
                                                    @if($stepCheck->comment)
                                                        <div class="mt-1">
                                                            <strong>審核意見：</strong>{{ $stepCheck->comment }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="mdi mdi-information-outline me-2"></i>
                                    此假單尚未套用任何流程
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <!-- 狀態資訊 -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">狀態資訊</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">目前狀態</label>
                            <p>
                                @switch($leaveRequest->state)
                                    @case(1)
                                        <span class="badge bg-secondary">草稿</span>
                                        @break
                                    @case(2)
                                        <span class="badge bg-warning">待審核</span>
                                        @break
                                    @case(9)
                                        <span class="badge bg-success">已核准</span>
                                        @break
                                    @case(3)
                                        <span class="badge bg-danger">已駁回</span>
                                        @break
                                    @case(4)
                                        <span class="badge bg-secondary">已撤銷</span>
                                        @break
                                    @default
                                        <span class="badge bg-light text-dark">未知</span>
                                @endswitch
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">流程類型</label>
                            <p class="form-control-plaintext">{{ $leaveRequest->workflow->name ?? '未指定' }}</p>
                        </div>

                        @if($leaveRequest->current_step)
                            <div class="mb-3">
                                <label class="form-label">目前關卡</label>
                                <p class="form-control-plaintext">
                                    {{ $leaveRequest->current_step->step_name }}
                                    <br>
                                    <small class="text-muted">審核人：{{ $leaveRequest->current_step->approver->name ?? '未指定' }}</small>
                                </p>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">進度</label>
                            <div class="progress">
                                @php
                                    $completedSteps = $workflowSteps->filter(function($step) use ($leaveRequest) {
                                        $stepCheck = $leaveRequest->checks->where('check_user_id', $step->approver_user_id)->first();
                                        return $stepCheck && $stepCheck->state == 9;
                                    })->count();
                                    $totalSteps = $workflowSteps->count();
                                    $progress = $totalSteps > 0 ? ($completedSteps / $totalSteps) * 100 : 0;
                                @endphp
                                <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%">
                                    {{ $completedSteps }}/{{ $totalSteps }}
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            @if($leaveRequest->state == 2)
                                <a href="{{ route('leave_day.check', $leaveRequest->id) }}" 
                                   class="btn btn-warning">
                                    <i class="mdi mdi-check me-1"></i>進行審核
                                </a>
                            @endif
                            
                            <a href="{{ route('leaveworkflow.status') }}" 
                               class="btn btn-outline-secondary">
                                <i class="mdi mdi-arrow-left me-1"></i>返回列表
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 審核歷史 -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">審核歷史</h5>
                        
                        @if($leaveRequest->checks->count() > 0)
                            <div class="timeline-vertical">
                                @foreach($leaveRequest->checks->sortBy('created_at') as $check)
                                    <div class="timeline-item-vertical">
                                        <div class="timeline-marker-vertical">
                                            @if($check->state == 9)
                                                <i class="mdi mdi-check-circle text-success"></i>
                                            @elseif($check->state == 3)
                                                <i class="mdi mdi-close-circle text-danger"></i>
                                            @else
                                                <i class="mdi mdi-clock-outline text-warning"></i>
                                            @endif
                                        </div>
                                        <div class="timeline-content-vertical">
                                            <h6 class="timeline-title-vertical">
                                                {{ $check->user->name ?? '未知' }}
                                                <span class="badge bg-{{ $check->state == 9 ? 'success' : ($check->state == 3 ? 'danger' : 'warning') }} ms-2">
                                                    {{ $check->state == 9 ? '核准' : ($check->state == 3 ? '駁回' : '待審核') }}
                                                </span>
                                            </h6>
                                            <p class="timeline-text-vertical">
                                                {{ date('Y-m-d H:i:s', strtotime($check->created_at)) }}
                                            </p>
                                            @if($check->comment)
                                                <div class="timeline-details-vertical">
                                                    <strong>意見：</strong>{{ $check->comment }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3">
                                <div class="text-muted">
                                    <i class="mdi mdi-information-outline me-2"></i>
                                    尚無審核記錄
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- container -->
@endsection

@section('css')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -22px;
    top: 8px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #e9ecef;
}

.timeline-item.completed::before {
    background: #28a745;
    border-color: #28a745;
}

.timeline-item.current::before {
    background: #ffc107;
    border-color: #ffc107;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 5px;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #e9ecef;
}

.timeline-item.completed .timeline-content {
    border-left-color: #28a745;
}

.timeline-item.current .timeline-content {
    border-left-color: #ffc107;
}

.timeline-title {
    margin-bottom: 8px;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 8px;
    color: #6c757d;
}

.timeline-details {
    font-size: 0.875rem;
}

.timeline-vertical .timeline-item-vertical {
    display: flex;
    margin-bottom: 20px;
}

.timeline-marker-vertical {
    margin-right: 15px;
    margin-top: 5px;
}

.timeline-content-vertical {
    flex: 1;
}

.timeline-title-vertical {
    margin-bottom: 5px;
    font-size: 0.9rem;
    font-weight: 600;
}

.timeline-text-vertical {
    margin-bottom: 5px;
    font-size: 0.8rem;
    color: #6c757d;
}

.timeline-details-vertical {
    font-size: 0.8rem;
}
</style>
@endsection

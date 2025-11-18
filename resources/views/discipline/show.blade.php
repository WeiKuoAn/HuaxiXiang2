@extends('layouts.vertical', ['page_title' => '懲戒詳情'])

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
                            <li class="breadcrumb-item active">懲戒詳情</li>
                        </ol>
                    </div>
                    <h4 class="page-title">懲戒詳情</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
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
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">基本資訊</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">案件編號</th>
                                        <td>{{ $discipline->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>受懲處人</th>
                                        <td>{{ $discipline->user->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>提出人</th>
                                        <td>{{ $discipline->proposer->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>嚴重性</th>
                                        <td>
                                            @if($discipline->severity == '輕度')
                                                <span class="badge bg-success">輕度</span>
                                            @elseif($discipline->severity == '中度')
                                                <span class="badge bg-warning">中度</span>
                                            @else
                                                <span class="badge bg-danger">重度</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>懲處金額</th>
                                        <td>NT$ {{ number_format($discipline->amount, 0) }}</td>
                                    </tr>
                                    <tr>
                                        <th>獎金扣除</th>
                                        <td>{{ $discipline->bonus_deduction ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>狀態</th>
                                        <td>
                                            @if($discipline->status == '待審核')
                                                <span class="badge bg-secondary">待審核</span>
                                            @elseif($discipline->status == '審核中')
                                                <span class="badge bg-info">審核中</span>
                                            @elseif($discipline->status == '已通過')
                                                <span class="badge bg-success">已通過</span>
                                            @else
                                                <span class="badge bg-danger">已拒絕</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>建立時間</th>
                                        <td>{{ $discipline->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    @if($discipline->approved_at)
                                        <tr>
                                            <th>審核完成時間</th>
                                            <td>{{ $discipline->approved_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h5 class="mb-3">發生日期</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>日期</th>
                                            <th>備註</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($discipline->dates as $date)
                                            <tr>
                                                <td>{{ $date->incident_date->format('Y-m-d') }}</td>
                                                <td>{{ $date->note ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="mb-3">事由</h5>
                                <div class="alert alert-light">
                                    {{ $discipline->reason }}
                                </div>
                            </div>
                        </div>

                        @if($discipline->resolution)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5 class="mb-3">決議內容</h5>
                                    <div class="alert alert-light">
                                        {{ $discipline->resolution }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="mb-3">審核狀態</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>審核人</th>
                                                <th>狀態</th>
                                                <th>審核意見</th>
                                                <th>審核時間</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($discipline->approvals as $approval)
                                                <tr>
                                                    <td>{{ $approval->approver->name ?? '-' }}</td>
                                                    <td>
                                                        @if($approval->status == '待審核')
                                                            <span class="badge bg-secondary">待審核</span>
                                                        @elseif($approval->status == '同意')
                                                            <span class="badge bg-success">同意</span>
                                                        @else
                                                            <span class="badge bg-danger">拒絕</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $approval->comment ?? '-' }}</td>
                                                    <td>{{ $approval->approved_at ? $approval->approved_at->format('Y-m-d H:i:s') : '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @php
                            $myApproval = $discipline->approvals->where('approver_id', Auth::id())->first();
                        @endphp

                        @if($myApproval && $myApproval->status == '待審核')
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h5 class="card-title">我的審核</h5>
                                            <form action="{{ route('disciplines.approve', $discipline->id) }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label class="form-label">審核決議 <span class="text-danger">*</span></label>
                                                    <div>
                                                        <div class="form-check form-check-inline">
                                                            <input type="radio" name="status" value="同意" 
                                                                   class="form-check-input" id="approve_yes" required>
                                                            <label class="form-check-label" for="approve_yes">同意</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input type="radio" name="status" value="拒絕" 
                                                                   class="form-check-input" id="approve_no" required>
                                                            <label class="form-check-label" for="approve_no">拒絕</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="comment" class="form-label">審核意見</label>
                                                    <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="選填"></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="mdi mdi-check"></i> 提交審核
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="text-end mt-4">
                            <a href="{{ route('disciplines.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> 返回列表
                            </a>
                            @if($discipline->status == '待審核')
                                <a href="{{ route('disciplines.edit', $discipline->id) }}" class="btn btn-warning">
                                    <i class="mdi mdi-pencil"></i> 編輯
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- container -->

@endsection




















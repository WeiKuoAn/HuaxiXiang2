@extends('layouts.vertical', ['page_title' => '懲戒管理'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">懲戒管理</a></li>
                            <li class="breadcrumb-item active">懲戒列表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">懲戒管理</h4>
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
                        <div class="row justify-content-between">
                            <div class="col-auto">
                                <form class="d-flex flex-wrap align-items-center" action="{{ route('disciplines.index') }}"
                                    method="GET">
                                    <div class="me-3">
                                        <label for="user_id" class="form-label">受懲處人</label>
                                        <select name="user_id" class="form-select">
                                            <option value="">全部</option>
                                            @foreach($punish_users as $punish_user)
                                                <option value="{{ $punish_user->id }}" {{ request('user_id') == $punish_user->id ? 'selected' : '' }}>
                                                    {{ $punish_user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="me-3">
                                        <label for="severity" class="form-label">嚴重性</label>
                                        <select name="severity" class="form-select">
                                            <option value="">全部</option>
                                            <option value="輕度" {{ request('severity') == '輕度' ? 'selected' : '' }}>輕度</option>
                                            <option value="中度" {{ request('severity') == '中度' ? 'selected' : '' }}>中度</option>
                                            <option value="重度" {{ request('severity') == '重度' ? 'selected' : '' }}>重度</option>
                                        </select>
                                    </div>
                                    <div class="me-3">
                                        <label for="status" class="form-label">狀態</label>
                                        <select name="status" class="form-select">
                                            <option value="">全部</option>
                                            <option value="待審核" {{ request('status') == '待審核' ? 'selected' : '' }}>待審核</option>
                                            <option value="審核中" {{ request('status') == '審核中' ? 'selected' : '' }}>審核中</option>
                                            <option value="已通過" {{ request('status') == '已通過' ? 'selected' : '' }}>已通過</option>
                                            <option value="已拒絕" {{ request('status') == '已拒絕' ? 'selected' : '' }}>已拒絕</option>
                                        </select>
                                    </div>
                                    <div class="me-3">
                                        <label for="start_date" class="form-label">開始日期</label>
                                        <input type="date" name="start_date" class="form-control"
                                            value="{{ request('start_date') }}">
                                    </div>
                                    <div class="me-3">
                                        <label for="end_date" class="form-label">結束日期</label>
                                        <input type="date" name="end_date" class="form-control"
                                            value="{{ request('end_date') }}">
                                    </div>
                                    <div class="me-3 mt-4">
                                        <button type="submit" class="btn btn-success waves-effect waves-light me-1">
                                            <i class="fe-search me-1"></i>搜尋
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-auto">
                                <div class="text-lg-end my-1 my-lg-0">
                                    <a href="{{ route('disciplines.my-approvals') }}" class="btn btn-info waves-effect waves-light me-1">
                                        <i class="mdi mdi-check-circle me-1"></i>待我審核
                                    </a>
                                    <a href="{{ route('disciplines.create') }}" class="btn btn-danger waves-effect waves-light">
                                        <i class="mdi mdi-plus-circle me-1"></i>新增懲戒
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-centered table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>編號</th>
                                        <th>提出人</th>
                                        <th>受懲處人</th>
                                        <th>發生日期</th>
                                        <th>嚴重性</th>
                                        <th>懲處金額</th>
                                        <th>狀態</th>
                                        <th>建立時間</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($disciplines as $index => $discipline)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $discipline->proposer->name ?? '-' }}</td>
                                            <td>{{ $discipline->user->name ?? '-' }}</td>
                                            <td>
                                                @foreach($discipline->dates as $date)
                                                    <span class="badge bg-info">{{ $date->incident_date->format('Y-m-d') }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if($discipline->severity == '輕度')
                                                    <span class="badge bg-success">輕度</span>
                                                @elseif($discipline->severity == '中度')
                                                    <span class="badge bg-warning">中度</span>
                                                @else
                                                    <span class="badge bg-danger">重度</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($discipline->bonus_deduction)
                                                    {{ $discipline->bonus_deduction }}
                                                @elseif($discipline->amount > 0)
                                                    {{ number_format($discipline->amount) }} 元
                                                @else
                                                    -
                                                @endif
                                            </td>
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
                                            <td>{{ $discipline->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                @php
                                                    $myApproval = $discipline->approvals->where('approver_id', Auth::id())->first();
                                                    $isApprover = $myApproval !== null;
                                                    $hasApproved = $isApprover && $myApproval->status !== '待審核';
                                                @endphp
                                                
                                                @if($isApprover && !$hasApproved)
                                                    {{-- 審核者且還沒審核，顯示審核按鈕 --}}
                                                    <a href="{{ route('disciplines.show', $discipline->id) }}" 
                                                       class="btn btn-sm btn-success">
                                                        <i class="mdi mdi-check-circle me-1"></i>審核
                                                    </a>
                                                @else
                                                    {{-- 已審核或非審核者，顯示預覽按鈕 --}}
                                                    <a href="{{ route('disciplines.show', $discipline->id) }}" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                @endif
                                                
                                                @if($discipline->status == '待審核' && $discipline->proposer_id == Auth::id())
                                                    <a href="{{ route('disciplines.edit', $discipline->id) }}" 
                                                       class="btn btn-sm btn-warning">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('disciplines.destroy', $discipline->id) }}" 
                                                          method="POST" 
                                                          style="display: inline-block;"
                                                          onsubmit="return confirm('確定要刪除此懲戒案件嗎？');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">暫無資料</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                {{ $disciplines->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- container -->

@endsection


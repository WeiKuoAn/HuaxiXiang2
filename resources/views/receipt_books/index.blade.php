@extends('layouts.vertical', ['page_title' => '跳單管理'])

@section('css')
    <style>
        .book-card {
            transition: all 0.3s ease;
        }
        
        .book-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .stat-box {
            padding: 15px;
            border-radius: 8px;
            background: #f8f9fa;
        }

        .progress-thin {
            height: 10px;
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">跳單管理</a></li>
                        <li class="breadcrumb-item active">單本列表</li>
                    </ol>
                </div>
                <h4 class="page-title">跳單管理</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <!-- 成功訊息 -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 搜尋和新增 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <form class="d-flex flex-wrap align-items-end" action="{{ route('receipt-books.index') }}" method="GET">
                                <div class="me-3">
                                    <label for="number" class="form-label">搜尋單號</label>
                                    <input type="number" class="form-control" id="number" name="number" placeholder="輸入任一單號"
                                           value="{{ request('number') }}">
                                    @if(isset($computedRange) && $computedRange)
                                        <small class="text-muted">自動換算區間：{{ $computedRange[0] }} ~ {{ $computedRange[1] }}</small>
                                    @endif
                                </div>
                                <div class="me-3">
                                    <label for="holder_id" class="form-label">保管人</label>
                                    <select class="form-select" name="holder_id" id="holder_id">
                                        <option value="">全部</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('holder_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="me-3">
                                    <label for="book_status" class="form-label">狀態</label>
                                    <select class="form-select" name="book_status" id="book_status">
                                        <option value="">全部</option>
                                        <option value="unused" {{ request('book_status') == 'unused' ? 'selected' : '' }}>未使用</option>
                                        <option value="active" {{ request('book_status') == 'active' ? 'selected' : '' }}>使用中</option>
                                        <option value="returned" {{ request('book_status') == 'returned' ? 'selected' : '' }}>已繳回</option>
                                        <option value="cancelled" {{ request('book_status') == 'cancelled' ? 'selected' : '' }}>已取消</option>
                                    </select>
                                </div>
                                <div class="me-3">
                                    <label for="date_from" class="form-label">發放日期（起）</label>
                                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                                </div>
                                <div class="me-3">
                                    <label for="date_to" class="form-label">發放日期（迄）</label>
                                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                                </div>
                                <div class="me-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1">
                                        <i class="fe-search me-1"></i>搜尋
                                    </button>
                                    <a href="{{ route('receipt-books.index') }}" class="btn btn-secondary waves-effect waves-light">
                                        <i class="fe-refresh-cw me-1"></i>重置
                                    </a>
                                </div>
                            </form>
                        </div>
                        <div class="col-auto">
                            <div class="text-lg-end my-1 my-lg-0 mt-4">
                                <a href="{{ route('receipt-books.create') }}" class="btn btn-danger waves-effect waves-light">
                                    <i class="mdi mdi-plus-circle me-1"></i>新增單本
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 單本列表 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($receiptBooks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-centered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>號碼區間</th>
                                        <th>保管人</th>
                                        <th>發放日期</th>
                                        <th>狀態</th>
                                        <th>使用情況</th>
                                        <th>繳回日期</th>
                                        <th>備註</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($receiptBooks as $book)
                                        <tr>
                                            <td>
                                                <strong class="text-primary">{{ $book->start_number }} ~ {{ $book->end_number }}</strong>
                                                <br>
                                                <small class="text-muted">共 {{ $book->statistics['total'] }} 張</small>
                                            </td>
                                            <td>
                                                <i class="mdi mdi-account me-1"></i>{{ optional($book->holder)->name ?? '-' }}
                                            </td>
                                            <td>{{ $book->issue_date ? $book->issue_date->format('Y-m-d') : '-' }}</td>
                                            <td>
                                                @if($book->status == 'unused')
                                                    <span class="badge bg-info">未使用</span>
                                                @elseif($book->status == 'active')
                                                    <span class="badge bg-success">使用中</span>
                                                @elseif($book->status == 'returned')
                                                    <span class="badge bg-secondary">已繳回</span>
                                                @else
                                                    <span class="badge bg-danger">已取消</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="mb-1">
                                                    <small class="text-muted">已使用：</small>
                                                    <strong class="text-success">{{ $book->statistics['used'] }}</strong>
                                                    /
                                                    <small class="text-muted">未使用：</small>
                                                    <strong class="text-danger">{{ $book->statistics['missing'] }}</strong>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: {{ $book->statistics['usage_rate'] }}%" 
                                                         aria-valuenow="{{ $book->statistics['usage_rate'] }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">使用率：{{ $book->statistics['usage_rate'] }}%</small>
                                            </td>
                                            <td>
                                                @if($book->returned_at)
                                                    {{ $book->returned_at->format('Y-m-d') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($book->note)
                                                    <small>{{ Str::limit($book->note, 20) }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('receipt-books.show', $book->id) }}" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       title="查看詳情">
                                                        <i class="fe-eye"></i>
                                                    </a>
                                                    <a href="{{ route('receipt-books.edit', $book->id) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="編輯">
                                                        <i class="fe-edit"></i>
                                                    </a>
                                                    @if($book->status == 'active')
                                                        <form action="{{ route('receipt-books.mark-returned', $book->id) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('確定要標記為已繳回嗎？');">
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-outline-success" 
                                                                    title="標記繳回">
                                                                <i class="fe-check-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form action="{{ route('receipt-books.destroy', $book->id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('確定要刪除此單本嗎？此操作無法復原。');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="刪除">
                                                            <i class="fe-trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- 分頁 -->
                        <div class="mt-3">
                            {{ $receiptBooks->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fe-inbox" style="font-size: 48px; color: #ccc;"></i>
                            <p class="text-muted mt-2">目前沒有單本記錄</p>
                            <a href="{{ route('receipt-books.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus-circle me-1"></i>新增第一本
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div> <!-- container -->
@endsection


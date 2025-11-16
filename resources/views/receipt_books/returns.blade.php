@extends('layouts.vertical', ['page_title' => '我的收據繳回'])

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
                        <li class="breadcrumb-item"><a href="{{ route('receipt-books.index') }}">跳單管理</a></li>
                        <li class="breadcrumb-item active">我的收據繳回</li>
                    </ol>
                </div>
                <h4 class="page-title">我的收據繳回</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle-outline me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fe-user-check me-2"></i>我持有的收據（可繳回）
                        </h5>
                        <div>
                            <a href="{{ route('receipt-books.claimable') }}" class="btn btn-outline-primary me-2">
                                <i class="fe-plus-circle me-1"></i>前往認領收據
                            </a>
                            <a href="{{ route('receipt-books.index') }}" class="btn btn-secondary">
                                <i class="fe-arrow-left me-1"></i>返回列表
                            </a>
                        </div>
                    </div>

                    @if($myBooks->count() === 0)
                        <div class="alert alert-info mb-0">
                            你目前沒有可繳回的收據。
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>號碼範圍</th>
                                        <th>發放日期</th>
                                        <th>狀態</th>
                                        <th class="text-end">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($myBooks as $book)
                                        <tr>
                                            <td>
            <span class="badge bg-primary">{{ $book->start_number }} ~ {{ $book->end_number }}</span>
                                            </td>
                                            <td>{{ optional($book->issue_date)->format('Y-m-d') ?: '-' }}</td>
                                            <td>
                                                @if($book->status === 'returned')
                                                    <span class="badge bg-secondary">已繳回</span>
                                                @elseif($book->status === 'active')
                                                    <span class="badge bg-success">使用中</span>
                                                @elseif($book->status === 'unused')
                                                    <span class="badge bg-info">未使用</span>
                                                @else
                                                    <span class="badge bg-danger">已取消</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($book->status !== 'returned')
                                                    <form action="{{ route('receipt-books.mark-returned', $book->id) }}" method="POST" class="d-inline" onsubmit="return confirm('確定要繳回這一本收據嗎？');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-success btn-sm">
                                                            <i class="fe-check-circle me-1"></i>繳回
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $myBooks->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div> <!-- container -->
@endsection



@extends('layouts.vertical', ['page_title' => '認領單本'])

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
                        <li class="breadcrumb-item active">認領單本</li>
                    </ol>
                </div>
                <h4 class="page-title">認領單本</h4>
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
                            <i class="fe-inbox me-2"></i>未指派之單本
                        </h5>
                        <a href="{{ route('receipt-books.index') }}" class="btn btn-secondary">
                            <i class="fe-arrow-left me-1"></i>返回列表
                        </a>
                    </div>

                    @if($receiptBooks->count() === 0)
                        <div class="alert alert-info mb-0">
                            目前沒有可認領的單本。
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>號碼範圍</th>
                                        <th>發放日期</th>
                                        <th>備註</th>
                                        <th class="text-end">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($receiptBooks as $book)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $book->start_number }} ~ {{ $book->end_number }}
                                                </span>
                                            </td>
                                            <td>{{ optional($book->issue_date)->format('Y-m-d') }}</td>
                                            <td>{{ $book->note ?: '-' }}</td>
                                            <td class="text-end">
                                                <form action="{{ route('receipt-books.claim', $book->id) }}" method="POST" class="d-inline" onsubmit="return confirm('確定要認領這一本嗎？');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fe-check me-1"></i>認領
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $receiptBooks->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div> <!-- container -->
@endsection



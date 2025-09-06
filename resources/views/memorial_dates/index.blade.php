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
                        <div class="row justify-content-between">
                            <div class="col-auto">
                                <form class="d-flex flex-wrap align-items-center" action="{{ route('memorial.dates') }}" method="GET">
                                    <div class="me-3">
                                        <input type="search" class="form-control my-1 my-lg-0" id="customer_name"
                                            name="customer_name" placeholder="客戶名稱" value="{{ $request->customer_name }}">
                                    </div>
                                    <div class="me-3">
                                        <input type="search" class="form-control my-1 my-lg-0" id="pet_name"
                                            name="pet_name" placeholder="寶貝名稱" value="{{ $request->pet_name }}">
                                    </div>
                                    <div class="me-3">
                                        <input type="search" class="form-control my-1 my-lg-0" id="sale_on"
                                            name="sale_on" placeholder="業務單號" value="{{ $request->sale_on }}">
                                    </div>
                                    <div class="me-3">
                                        <input type="date" class="form-control my-1 my-lg-0" id="date_from"
                                            name="date_from" value="{{ $request->date_from }}">
                                    </div>
                                    <div class="me-3">
                                        <input type="date" class="form-control my-1 my-lg-0" id="date_to"
                                            name="date_to" value="{{ $request->date_to }}">
                                    </div>
                                    <div class="me-sm-3">
                                        <button type="submit" class="btn btn-primary my-1 my-lg-0">
                                            <i class="mdi mdi-magnify"></i> 搜尋
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('memorial.dates') }}" class="btn btn-secondary my-1 my-lg-0">
                                    <i class="mdi mdi-refresh"></i> 重置
                                </a>
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
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($memorialDates->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>業務單號</th>
                                            <th>客戶名稱</th>
                                            <th>寶貝名稱</th>
                                            <th>往生日期</th>
                                            <th>頭七</th>
                                            <th>四十九日</th>
                                            <th>百日</th>
                                            <th>對年</th>
                                            <th>備註</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($memorialDates as $memorial)
                                            <tr>
                                                <td>{{ $memorial->sale->sale_on ?? '-' }}</td>
                                                <td>{{ $memorial->sale->cust_name->name ?? '-' }}</td>
                                                <td>{{ $memorial->sale->pet_name ?? '-' }}</td>
                                                <td>{{ $memorial->sale->death_date ? \Carbon\Carbon::parse($memorial->sale->death_date)->format('Y/m/d') : '-' }}</td>
                                                <td>
                                                    @if($memorial->seventh_day)
                                                        {{ \Carbon\Carbon::parse($memorial->seventh_day)->format('Y/m/d') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($memorial->forty_ninth_day)->format('Y/m/d') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($memorial->hundredth_day)->format('Y/m/d') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($memorial->anniversary_day)->format('Y/m/d') }}</td>
                                                <td>
                                                    @if($memorial->notes)
                                                        <span class="text-truncate d-inline-block" style="max-width: 100px;" title="{{ $memorial->notes }}">
                                                            {{ $memorial->notes }}
                                                        </span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('memorial.dates.edit', $memorial->id) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="mdi mdi-pencil"></i> 編輯
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- 分頁 -->
                            <div class="d-flex justify-content-center">
                                {{ $memorialDates->appends($request->query())->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="mdi mdi-information-outline" style="font-size: 48px; color: #6c757d;"></i>
                                <h5 class="mt-2">沒有找到符合條件的紀念日記錄</h5>
                                <p class="text-muted">請調整搜尋條件後重新搜尋</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- container -->
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // 自動提交表單（可選）
    // $('#customer_name, #pet_name, #sale_on').on('input', function() {
    //     $(this).closest('form').submit();
    // });
});
</script>
@endpush

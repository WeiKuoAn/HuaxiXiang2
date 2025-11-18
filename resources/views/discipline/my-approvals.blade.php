@extends('layouts.vertical', ['page_title' => '待我審核的懲戒案件'])

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
                            <li class="breadcrumb-item active">待我審核</li>
                        </ol>
                    </div>
                    <h4 class="page-title">待我審核的懲戒案件</h4>
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
                        <div class="text-end mb-3">
                            <a href="{{ route('disciplines.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> 返回懲戒列表
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-centered table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>案件編號</th>
                                        <th>受懲處人</th>
                                        <th>提出人</th>
                                        <th>發生日期</th>
                                        <th>嚴重性</th>
                                        <th>懲處金額</th>
                                        <th>事由</th>
                                        <th>建立時間</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($approvals as $approval)
                                        @php
                                            $discipline = $approval->discipline;
                                        @endphp
                                        <tr>
                                            <td>{{ $discipline->id }}</td>
                                            <td>{{ $discipline->user->name ?? '-' }}</td>
                                            <td>{{ $discipline->proposer->name ?? '-' }}</td>
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
                                            <td>NT$ {{ number_format($discipline->amount, 0) }}</td>
                                            <td>
                                                <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    {{ $discipline->reason }}
                                                </div>
                                            </td>
                                            <td>{{ $discipline->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="{{ route('disciplines.show', $discipline->id) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="mdi mdi-eye"></i> 查看並審核
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">目前沒有待審核的案件</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                {{ $approvals->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- container -->

@endsection




















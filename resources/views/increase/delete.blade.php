@extends('layouts.vertical', ['page_title' => '刪除加成'])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">加成管理</a></li>
                        <li class="breadcrumb-item active">刪除加成</li>
                    </ol>
                </div>
                <h4 class="page-title">刪除加成</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-danger">
                                <h5><i class="fe-alert-triangle me-2"></i>確認刪除</h5>
                                <p class="mb-0">您確定要刪除以下加成記錄嗎？此操作無法復原。</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th width="150">加成日期</th>
                                            <td>{{ $increase->increase_date->format('Y-m-d') }}</td>
                                        </tr>
                                        <tr>
                                            <th>備註</th>
                                            <td>{{ $increase->comment ?: '無' }}</td>
                                        </tr>
                                        <tr>
                                            <th>建立者</th>
                                            <td>{{ $increase->creator->name ?? '未知' }}</td>
                                        </tr>
                                        <tr>
                                            <th>建立時間</th>
                                            <td>{{ $increase->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($increase->items->count() > 0)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>加成項目明細：</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>人員</th>
                                            <th>類型</th>
                                            <th>加成類別</th>
                                            <th>夜間加成</th>
                                            <th>晚間加成</th>
                                            <th>颱風加成</th>
                                            <th>夜間開爐</th>
                                            <th>加班費</th>
                                            <th>總金額</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($increase->items as $item)
                                            @if($item->item_type === 'traditional')
                                                @if($item->phone_person_id)
                                                <tr>
                                                    <td>
                                                        {{ $item->phonePerson->name ?? '未指定' }}
                                                        @if($item->phone_exclude_bonus)
                                                            <br><small class="text-muted">(不計入獎金)</small>
                                                        @endif
                                                    </td>
                                                    <td><span class="badge bg-primary">傳統加成</span></td>
                                                    <td>
                                                        @php
                                                            $categories = [];
                                                            if ($item->night_phone_amount > 0 || $item->night_receive_amount > 0) $categories[] = '夜間';
                                                            if ($item->evening_phone_amount > 0 || $item->evening_receive_amount > 0) $categories[] = '晚間';
                                                            if ($item->typhoon_phone_amount > 0 || $item->typhoon_receive_amount > 0) $categories[] = '颱風';
                                                        @endphp
                                                        @foreach($categories as $category)
                                                            @php
                                                                $badgeClass = match($category) {
                                                                    '夜間' => 'bg-primary',
                                                                    '晚間' => 'bg-success',
                                                                    '颱風' => 'bg-warning',
                                                                    default => 'bg-light text-dark'
                                                                };
                                                            @endphp
                                                            <span class="badge {{ $badgeClass }} me-1">{{ $category }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td>${{ number_format($item->night_phone_amount, 0) }}</td>
                                                    <td>${{ number_format($item->evening_phone_amount, 0) }}</td>
                                                    <td>${{ number_format($item->typhoon_phone_amount, 0) }}</td>
                                                    <td>$0</td>
                                                    <td>$0</td>
                                                    <td>${{ number_format($item->total_phone_amount, 0) }}</td>
                                                </tr>
                                                @endif
                                                @if($item->receive_person_id)
                                                <tr>
                                                    <td>{{ $item->receivePerson->name ?? '未指定' }}</td>
                                                    <td><span class="badge bg-primary">傳統加成</span></td>
                                                    <td>
                                                        @php
                                                            $categories = [];
                                                            if ($item->night_receive_amount > 0) $categories[] = '夜間';
                                                            if ($item->evening_receive_amount > 0) $categories[] = '晚間';
                                                            if ($item->typhoon_receive_amount > 0) $categories[] = '颱風';
                                                        @endphp
                                                        @foreach($categories as $category)
                                                            @php
                                                                $badgeClass = match($category) {
                                                                    '夜間' => 'bg-primary',
                                                                    '晚間' => 'bg-success',
                                                                    '颱風' => 'bg-warning',
                                                                    default => 'bg-light text-dark'
                                                                };
                                                            @endphp
                                                            <span class="badge {{ $badgeClass }} me-1">{{ $category }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td>${{ number_format($item->night_receive_amount, 0) }}</td>
                                                    <td>${{ number_format($item->evening_receive_amount, 0) }}</td>
                                                    <td>${{ number_format($item->typhoon_receive_amount, 0) }}</td>
                                                    <td>$0</td>
                                                    <td>$0</td>
                                                    <td>${{ number_format($item->total_receive_amount, 0) }}</td>
                                                </tr>
                                                @endif
                                            @elseif($item->item_type === 'furnace')
                                            <tr>
                                                <td>{{ $item->furnacePerson->name ?? '未指定' }}</td>
                                                <td><span class="badge bg-secondary">夜間開爐</span></td>
                                                <td><span class="badge bg-secondary">夜間開爐</span></td>
                                                <td>$0</td>
                                                <td>$0</td>
                                                <td>$0</td>
                                                <td>${{ number_format($item->total_amount, 0) }}</td>
                                                <td>$0</td>
                                                <td>${{ number_format($item->total_amount, 0) }}</td>
                                            </tr>
                                            @elseif($item->item_type === 'overtime')
                                            <tr>
                                                <td>{{ $item->overtimeRecord->user->name ?? '未指定' }}</td>
                                                <td><span class="badge bg-info">加班費</span></td>
                                                <td><span class="badge bg-info">加班費</span></td>
                                                <td>$0</td>
                                                <td>$0</td>
                                                <td>$0</td>
                                                <td>$0</td>
                                                <td>${{ number_format($item->custom_amount ?? $item->total_amount, 0) }}</td>
                                                <td>${{ number_format($item->custom_amount ?? $item->total_amount, 0) }}</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="8" class="text-end">總計：</th>
                                            <th>${{ number_format($increase->items->sum('total_amount'), 0) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <form action="{{ route('increase.del.data', $increase->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger waves-effect waves-light m-1">
                                    <i class="fe-trash-2 me-1"></i>確認刪除
                                </button>
                            </form>
                            <a href="{{ route('increase.index') }}" class="btn btn-secondary waves-effect waves-light m-1">
                                <i class="fe-x me-1"></i>取消
                            </a>
                        </div>
                    </div>
                </div> <!-- end card-body -->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>
    <!-- end row-->

</div> <!-- container -->
@endsection

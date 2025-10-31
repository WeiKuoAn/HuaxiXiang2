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
                                            <th>類別標記</th>
                                            <td>
                                                @if($increase->evening_is_typhoon || $increase->evening_is_newyear)
                                                    <strong>晚間加成：</strong>
                                                    @if($increase->evening_is_typhoon)
                                                        <span class="badge bg-warning text-dark">颱風</span>
                                                    @endif
                                                    @if($increase->evening_is_newyear)
                                                        <span class="badge bg-danger text-white">過年</span>
                                                    @endif
                                                    <br>
                                                @endif
                                                @if($increase->night_is_typhoon || $increase->night_is_newyear)
                                                    <strong>夜間加成：</strong>
                                                    @if($increase->night_is_typhoon)
                                                        <span class="badge bg-warning text-dark">颱風</span>
                                                    @endif
                                                    @if($increase->night_is_newyear)
                                                        <span class="badge bg-danger text-white">過年</span>
                                                    @endif
                                                @endif
                                                @if(!$increase->evening_is_typhoon && !$increase->evening_is_newyear && !$increase->night_is_typhoon && !$increase->night_is_newyear)
                                                    <span class="text-muted">無特殊標記</span>
                                                @endif
                                            </td>
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
                                            <th>類別</th>
                                            <th>角色</th>
                                            <th>次數</th>
                                            <th>單價</th>
                                            <th>金額</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($increase->items as $item)
                                            @if($item->category === 'evening' && $item->role === 'phone')
                                            <tr>
                                                <td>{{ $item->phonePerson->name ?? '未指定' }}</td>
                                                <td>
                                                    <span class="badge bg-info">晚間加成</span>
                                                    @if($increase->evening_is_typhoon)
                                                        <span class="badge bg-warning text-dark">颱風</span>
                                                    @endif
                                                    @if($increase->evening_is_newyear)
                                                        <span class="badge bg-danger text-white">過年</span>
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-primary">電話人員</span></td>
                                                <td>{{ $item->count ?? 1 }}</td>
                                                <td>${{ number_format($item->unit_price ?? 0, 0) }}</td>
                                                <td>${{ number_format($item->total_amount, 0) }}</td>
                                            </tr>
                                            @elseif($item->category === 'evening' && $item->role === 'receive')
                                            <tr>
                                                <td>{{ $item->receivePerson->name ?? '未指定' }}</td>
                                                <td>
                                                    <span class="badge bg-info">晚間加成</span>
                                                    @if($increase->evening_is_typhoon)
                                                        <span class="badge bg-warning text-dark">颱風</span>
                                                    @endif
                                                    @if($increase->evening_is_newyear)
                                                        <span class="badge bg-danger text-white">過年</span>
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-success">接件人員</span></td>
                                                <td>{{ $item->count ?? 1 }}</td>
                                                <td>${{ number_format($item->unit_price ?? 0, 0) }}</td>
                                                <td>${{ number_format($item->total_amount, 0) }}</td>
                                            </tr>
                                            @elseif($item->category === 'night' && $item->role === 'phone')
                                            <tr>
                                                <td>{{ $item->phonePerson->name ?? '未指定' }}</td>
                                                <td>
                                                    <span class="badge bg-dark">夜間加成</span>
                                                    @if($increase->night_is_typhoon)
                                                        <span class="badge bg-warning text-dark">颱風</span>
                                                    @endif
                                                    @if($increase->night_is_newyear)
                                                        <span class="badge bg-danger text-white">過年</span>
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-primary">電話人員</span></td>
                                                <td>{{ $item->count ?? 1 }}</td>
                                                <td>${{ number_format($item->unit_price ?? 0, 0) }}</td>
                                                <td>${{ number_format($item->total_amount, 0) }}</td>
                                            </tr>
                                            @elseif($item->category === 'night' && $item->role === 'receive')
                                            <tr>
                                                <td>{{ $item->receivePerson->name ?? '未指定' }}</td>
                                                <td>
                                                    <span class="badge bg-dark">夜間加成</span>
                                                    @if($increase->night_is_typhoon)
                                                        <span class="badge bg-warning text-dark">颱風</span>
                                                    @endif
                                                    @if($increase->night_is_newyear)
                                                        <span class="badge bg-danger text-white">過年</span>
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-success">接件人員</span></td>
                                                <td>{{ $item->count ?? 1 }}</td>
                                                <td>${{ number_format($item->unit_price ?? 0, 0) }}</td>
                                                <td>${{ number_format($item->total_amount, 0) }}</td>
                                            </tr>
                                            @elseif($item->item_type === 'furnace')
                                            <tr>
                                                <td>{{ $item->furnacePerson->name ?? '未指定' }}</td>
                                                <td><span class="badge bg-secondary">夜間開爐</span></td>
                                                <td><span class="badge bg-secondary">開爐人員</span></td>
                                                <td>1</td>
                                                <td>${{ number_format($item->total_amount, 0) }}</td>
                                                <td>${{ number_format($item->total_amount, 0) }}</td>
                                            </tr>
                                            @elseif($item->item_type === 'overtime')
                                            <tr>
                                                <td>
                                                    {{ $item->overtimeRecord->user->name ?? '未指定' }}
                                                    @if($item->overtimeRecord)
                                                        <br><small class="text-muted">
                                                            {{ $item->overtimeRecord->formatted_hours }}
                                                            @if($item->overtimeRecord->reason)
                                                                - {{ $item->overtimeRecord->reason }}
                                                            @endif
                                                        </small>
                                                        @if($item->overtimeRecord->creator)
                                                            <br><small class="text-info">由 {{ $item->overtimeRecord->creator->name }} 新增</small>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-warning text-dark">加班費</span></td>
                                                <td colspan="3">
                                                    @if($item->overtimeRecord)
                                                        <div class="small">
                                                            <div class="text-primary">1.34倍：{{ number_format($item->overtimeRecord->first_two_hours, 1) }}小時</div>
                                                            <div class="text-success">1.67倍：{{ number_format($item->overtimeRecord->remaining_hours, 1) }}小時</div>
                                                        </div>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>-</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="5" class="text-end">總計：</th>
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

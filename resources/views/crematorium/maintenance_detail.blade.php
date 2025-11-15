@extends('layouts.vertical', ['page_title' => '檢查記錄詳情'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">火化爐管理</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('crematorium.maintenance') }}">檢查記錄</a></li>
                            <li class="breadcrumb-item active">檢查記錄詳情</li>
                        </ol>
                    </div>
                    <h4 class="page-title">檢查記錄詳情</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- 基本資訊 -->
                        @php
                            $details = $maintenance->maintenanceDetails;
                            $statusMap = [
                                'good' => ['label' => '正常', 'class' => 'success'],
                                'problem' => ['label' => '異常', 'class' => 'danger'],
                                'not_checked' => ['label' => '未檢查', 'class' => 'secondary'],
                            ];
                            $actionMap = [
                                'repair' => '維修',
                                'replace' => '更換',
                            ];
                            $replacementMap = [
                                'new' => '全新',
                                'usable' => '堪用',
                            ];
                            $summary = [
                                'total' => $details->count(),
                                'problem' => $details->where('status', 'problem')->count(),
                                'not_checked' => $details->where('status', 'not_checked')->count(),
                                'fixed' => $details->where('action', 'replace')->sum('quantity'),
                            ];
                        @endphp

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <h5 class="mb-3">任務資訊</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="140"><strong>檢查單號：</strong></td>
                                        <td>{{ $maintenance->maintenance_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>檢查日期：</strong></td>
                                        <td>
                                            @if($maintenance->maintenance_date)
                                                {{ \Illuminate\Support\Carbon::parse($maintenance->maintenance_date)->format('Y-m-d') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>檢查人員：</strong></td>
                                        <td>{{ $maintenance->inspectorUser->name ?? '未指派' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>保養人員：</strong></td>
                                        <td>{{ $maintenance->maintainerUser->name ?? '未指派' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>狀態：</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $maintenance->status_color ?? 'secondary' }} fs-6">
                                                {{ $maintenance->status_text ?? '未知' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h5 class="mb-3">檢查統計</h5>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="card border-0 bg-success bg-opacity-10 mb-2">
                                            <div class="card-body text-center py-3">
                                                <div class="text-success fs-4 fw-bold">{{ $summary['total'] }}</div>
                                                <div class="text-muted">檢查設備數</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card border-0 bg-danger bg-opacity-10 mb-2">
                                            <div class="card-body text-center py-3">
                                                <div class="text-danger fs-4 fw-bold">{{ $summary['problem'] }}</div>
                                                <div class="text-muted">異常項目</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card border-0 bg-secondary bg-opacity-10">
                                            <div class="card-body text-center py-3">
                                                <div class="text-secondary fs-4 fw-bold">{{ $summary['not_checked'] }}</div>
                                                <div class="text-muted">未檢查</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card border-0 bg-warning bg-opacity-10">
                                            <div class="card-body text-center py-3">
                                                <div class="text-warning fs-4 fw-bold">{{ $summary['fixed'] }}</div>
                                                <div class="text-muted">更換數量</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($maintenance->notes)
                            <div class="alert alert-info">
                                <strong>備註：</strong> {{ $maintenance->notes }}
                            </div>
                        @endif

                        <div class="mb-4">
                            <h5 class="mb-3">設備檢查明細</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="18%">設備 / 位置</th>
                                            <th width="12%">類別</th>
                                            <th width="10%">狀態</th>
                                            <th>問題描述</th>
                                            <th width="12%">處理方式</th>
                                            <th width="10%">更換數量</th>
                                            <th width="10%">更換類型</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($details as $detail)
                                            @php
                                                $instance = $detail->equipmentInstance;
                                                $type = $instance?->equipmentType;
                                                $statusMeta = $statusMap[$detail->status] ?? ['label' => '未知', 'class' => 'secondary'];
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $type->name ?? '未指定' }}</div>
                                                    <div class="text-muted small">{{ $instance?->full_location }}</div>
                                                </td>
                                                <td>{{ $instance?->category_text ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $statusMeta['class'] }}">
                                                        {{ $statusMeta['label'] }}
                                                    </span>
                                                </td>
                                                <td>{{ $detail->problem_description ?: '—' }}</td>
                                                <td>{{ $actionMap[$detail->action] ?? '未處理' }}</td>
                                                <td>{{ $detail->quantity ?: '-' }}</td>
                                                <td>{{ $replacementMap[$detail->replacement_type] ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    尚未紀錄任何設備明細
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('crematorium.maintenance') }}" class="btn btn-secondary me-2">返回列表</a>
                            <a href="#" class="btn btn-primary" onclick="editRecord({{ $maintenance->id }})">編輯記錄</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- container -->

    <script>
        function editRecord(id) {
            // 這裡可以實現編輯功能
            alert('編輯功能：記錄 ID ' + id);
        }
    </script>
@endsection

@extends('layouts.vertical', ['page_title' => '達標列表'])

@section('content')
    <div class="container-fluid">

        <!-- Start Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Huaxixiang</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">達標管理</a></li>
                            <li class="breadcrumb-item active">達標列表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">達標列表</h4>
                </div>
            </div>
        </div>
        <!-- End Page Title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-sm-8"></div>
                            <div class="col-sm-4 text-sm-end">
                                <a href="{{ route('target.create') }}" class="btn btn-danger">
                                    <i class="mdi mdi-plus-circle me-1"></i> 新增達標
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-centered table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>達標類別</th>
                                        <th>對象</th>
                                        <th>頻率</th>
                                        <th>達標設定</th>
                                        <th>應達標數</th>
                                        <th>實際標數</th>
                                        <th>動作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $key => $data)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td width="9%">{{ $data->category_name->name }}</td>
                                            <td width="9%">
                                                @foreach ($data->job_id as $jobId)
                                                    {{ $jobs[$jobId] ?? '職稱不存在' }}<br>
                                                @endforeach
                                            </td>
                                            <td width="5%">{{ $data->frequency }}</td>
                                            <td width="10%">{{ $data->target_condition }}</td>
                                            <td width="12%">
                                                @if ($data->target_condition == '金額')
                                                    {{ $data->target_amount }}
                                                @elseif($data->target_condition == '數量')
                                                    {{ $data->target_quantity }}
                                                @elseif($data->target_condition == '金額+數量')
                                                    金額：{{ $data->target_amount }}<br>數量：{{ $data->target_quantity }}
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap">
                                                    @foreach ($data->items as $index => $item)
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary m-1 w-5"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="達標數：{{ $item->manual_achieved }} | 狀態：{{ $item->status }} | 獎勵：{{ $item->gift }}"
                                                            data-bs-target="#editTargetModal{{ $item->id }}"
                                                            onclick="openModal({{ $item->id }})">
                                                            {{ \Carbon\Carbon::parse($item->end_date)->format('Y-m') }}
                                                        </button>

                                                        <!-- Modal 彈跳視窗 -->
                                                        <div class="modal fade" id="editTargetModal{{ $item->id }}"
                                                            tabindex="-1" aria-labelledby="modalLabel{{ $item->id }}"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="modalLabel{{ $item->id }}">編輯達標數據</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form
                                                                            action="{{ route('target_item.update', $item->id) }}"
                                                                            method="POST">
                                                                            @csrf
                                                                            @method('PUT')

                                                                            <div class="mb-3">
                                                                                <label class="form-label">達標狀態</label>
                                                                                <select class="form-control" name="status"
                                                                                    required>
                                                                                    <option value="進行中"
                                                                                        {{ $item->status == '進行中' ? 'selected' : '' }}>
                                                                                        進行中</option>
                                                                                    <option value="已完成"
                                                                                        {{ $item->status == '已完成' ? 'selected' : '' }}>
                                                                                        已完成</option>
                                                                                    <option value="未達標"
                                                                                        {{ $item->status == '未達標' ? 'selected' : '' }}>
                                                                                        未達標</option>
                                                                                </select>
                                                                            </div>

                                                                            <div class="mb-3">
                                                                                <label class="form-label">達標數</label>
                                                                                <input type="number" class="form-control"
                                                                                    name="manual_achieved"
                                                                                    value="{{ $item->manual_achieved }}"
                                                                                    required>
                                                                            </div>

                                                                            <div class="mb-3">
                                                                                <label class="form-label">達標獎勵</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="gift"
                                                                                    value="{{ $item->gift }}">
                                                                            </div>

                                                                            <div class="text-center">
                                                                                <button type="submit"
                                                                                    class="btn btn-success">儲存變更</button>
                                                                                <button type="button"
                                                                                    class="btn btn-secondary"
                                                                                    data-bs-dismiss="modal">取消</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- End Modal -->
                                                    @endforeach
                                                </div>
                                            </td>





                                            <td>
                                                <div class="btn-group dropdown">
                                                    <a href="javascript: void(0);"
                                                        class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect"
                                                        data-bs-toggle="dropdown" aria-expanded="false">動作 <i
                                                            class="mdi mdi-arrow-down-drop-circle"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item"
                                                            href="{{ route('target.edit', $data->id) }}"><i
                                                                class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯</a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('target.del', $data->id) }}"><i
                                                                class="mdi mdi-delete me-2 font-18 text-muted vertical-middle"></i>刪除</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col -->
        </div> <!-- end row -->

    </div> <!-- end container -->

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 啟用 Bootstrap Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // 按鈕點擊時，開啟對應的 Modal
        function openModal(itemId) {
            var modal = new bootstrap.Modal(document.getElementById('editTargetModal' + itemId));
            modal.show();
        }
    </script>
@endsection

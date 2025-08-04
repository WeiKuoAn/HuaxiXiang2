@extends('layouts.vertical', ['page_title' => '子選單管理'])

@section('css')
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .menu-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 20px;
        }
        .menu-card {
            display: flex;
            align-items: center;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            padding: 14px 18px;
            transition: box-shadow 0.2s;
            border-left: 4px solid #e3e3e3;
            position: relative;
        }
        .menu-card:hover {
            box-shadow: 0 6px 24px rgba(0,0,0,0.10);
            border-left: 4px solid #007bff;
        }
        .drag-handle {
            cursor: grab;
            margin-right: 18px;
            color: #adb5bd;
            font-size: 1.3rem;
        }
        .menu-type, .menu-url, .menu-sort, .menu-actions {
            margin-right: 18px;
            min-width: 80px;
        }
        .menu-name {
            font-weight: 500;
            flex: 1;
            display: flex;
            align-items: center;
        }
        .menu-actions a { margin-right: 8px; }
        @media (max-width: 768px) {
            .menu-card { flex-wrap: wrap; }
            .menu-type, .menu-url, .menu-sort, .menu-actions {
                min-width: unset;
                margin-right: 10px;
                margin-top: 6px;
            }
        }
        .sortable-ghost {
            opacity: 0.5;
            background: #f8f9fa;
        }
        .sortable-chosen {
            background: #e3f2fd;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">子選單管理 - {{ $parent->name }}</h4>
        <a href="{{ route('menu.index') }}" class="btn btn-secondary"><i class="mdi mdi-arrow-left"></i> 返回主選單</a>
        <a href="{{ route('menu.create') }}?parent_id={{ $parent->id }}" class="btn btn-danger"><i class="mdi mdi-plus-circle me-1"></i>新增子選單</a>
    </div>
    <div class="alert alert-info mb-2">
        拖曳子選單卡片來重新排序，調整好順序後請點擊 <b>儲存排序</b>。
        <button type="button" class="btn btn-primary btn-sm ms-3" onclick="saveOrder()">
            <i class="fe-save me-1"></i>儲存排序
        </button>
    </div>
    <div class="menu-list" id="sortable-menu">
        @foreach ($datas as $data)
            <div class="menu-card menu-item"
                 data-id="{{ $data->id }}"
                 data-parent="{{ $parent->id }}">
                <div class="drag-handle">☰</div>
                <div class="menu-name">
                    {{ $data->name }}
                    @if ($data->comment)
                        <b class="text-danger">　※{{ $data->comment }}</b>
                    @endif
                </div>
                <div class="menu-type">
                    <span class="badge bg-{{ $data->type == 'main' ? 'primary' : 'success' }}">
                        {{ $data->type == 'main' ? '主要' : 'Apps' }}
                    </span>
                </div>
                <div class="menu-url"><code>{{ $data->url }}</code></div>
                <div class="menu-sort">
                    <span class="badge bg-light text-dark">{{ $data->sort }}</span>
                </div>
                <div class="menu-actions">
                    <a href="{{ route('menu.edit', $data->id) }}" class="action-icon" title="編輯">
                        <i class="mdi mdi-square-edit-outline"></i>
                    </a>
                    <a href="javascript:void(0);" class="action-icon text-danger" title="刪除" onclick="deleteMenu({{ $data->id }})">
                        <i class="mdi mdi-delete"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
    <!-- 刪除確認 Modal ...同主選單頁-->
</div>
@endsection

@section('script')
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        let menuToDelete = null;
        document.addEventListener('DOMContentLoaded', function() {
            new Sortable(document.getElementById('sortable-menu'), {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag'
            });
        });
        function saveOrder() {
            const menuItems = document.querySelectorAll('#sortable-menu .menu-item');
            const orderData = [];
            menuItems.forEach((item, index) => {
                orderData.push({
                    id: item.dataset.id,
                    sort: index + 1,
                    parent_id: {{ $parent->id }}
                });
            });
            fetch('{{ route("menu.updateOrder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ order: orderData })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '排序已儲存',
                        text: '子選單排序已成功更新',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => { location.reload(); }, 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '儲存失敗',
                        text: data.message || '排序儲存失敗，請重試'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: '儲存失敗',
                    text: '網路錯誤，請重試'
                });
            });
        }
        function deleteMenu(id) {
            menuToDelete = id;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (menuToDelete) {
                fetch(`/menu/delete/${menuToDelete}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '刪除成功',
                            text: '子選單已成功刪除',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => { location.reload(); });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '刪除失敗',
                            text: data.message || '刪除失敗，請重試'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: '刪除失敗',
                        text: '網路錯誤，請重試'
                    });
                });
            }
            const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
            deleteModal.hide();
        });
    </script>
@endsection 
@extends('layouts.vertical', ['page_title' => '主選單管理'])

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
            <h4 class="mb-0">主選單管理</h4>
            <a href="{{ route('menu.create') }}" class="btn btn-danger"><i class="mdi mdi-plus-circle me-1"></i>新增主選單</a>
        </div>
        <div class="alert alert-info mb-2">
            拖曳主選單卡片來重新排序，調整好順序後請點擊 <b>儲存排序</b>。
            <button type="button" class="btn btn-primary" id="saveOrderBtn">儲存排序</button>
        </div>
        <div class="menu-list" id="sortable-menu">
            @foreach ($datas as $data)
                <div class="menu-card menu-item" data-id="{{ $data->id }}">
                    <div class="drag-handle">☰</div>
                    <div class="menu-name">
                        {{ $data->name }}
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
                        <a href="{{ route('menu.sub', $data->id) }}" class="btn btn-sm btn-info" title="管理子選單">
                            <i class="mdi mdi-format-list-bulleted"></i> 子選單
                        </a>
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
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">確認刪除</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        確定要刪除這個選單嗎？此操作無法復原。
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">確定刪除</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        let menuToDelete = null;
        let sortableInstance = null;

        // 全域函式：儲存排序
        function saveOrder() {
            const menuItems = document.querySelectorAll('#sortable-menu .menu-item');
            const orderData = [];
            
            menuItems.forEach((item, index) => {
                if (!item || !item.dataset.id) return;
                orderData.push({
                    id: parseInt(item.dataset.id),
                    sort: index + 1
                });
            });

            console.log('準備送出的排序資料:', orderData);

            // 取得 CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                Swal.fire({
                    icon: 'error',
                    title: 'CSRF Token 錯誤',
                    text: '無法取得 CSRF token，請重新整理頁面'
                });
                return;
            }

            // 顯示載入中
            Swal.fire({
                title: '儲存中...',
                text: '正在更新排序',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route('menu.updateOrder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ order: orderData })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '排序已儲存',
                        text: '排序已成功更新',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
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
                    text: '網路錯誤，請重試。錯誤詳情：' + error.message
                });
            });
        }

        // 全域函式：刪除選單
        function deleteMenu(id) {
            menuToDelete = id;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        // 頁面載入完成後初始化
        document.addEventListener('DOMContentLoaded', function() {
            // 初始化 Sortable
            const sortableElement = document.getElementById('sortable-menu');
            if (sortableElement) {
                sortableInstance = new Sortable(sortableElement, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    onEnd: function(evt) {
                        console.log('拖拉完成，新順序:', evt.newIndex, '舊順序:', evt.oldIndex);
                    }
                });
                console.log('Sortable 已初始化');
            }

            // 綁定儲存排序按鈕
            const saveOrderBtn = document.getElementById('saveOrderBtn');
            if (saveOrderBtn) {
                saveOrderBtn.addEventListener('click', saveOrder);
                console.log('儲存排序按鈕已綁定');
            }

            // 綁定刪除確認按鈕
            const confirmDeleteBtn = document.getElementById('confirmDelete');
            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', function() {
                    if (menuToDelete) {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        
                        fetch(`/menu/delete/${menuToDelete}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '刪除成功',
                                    text: '主選單已成功刪除',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
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
            }
        });
    </script>
@endsection
           
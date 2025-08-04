@extends('layouts.vertical', ['page_title' => '職稱選單配對'])

@section('content')
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('job.menu.create.data') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">職稱 <span class="text-danger">*</span></label>
                            <select class="form-control" name="job_id" required>
                                <option value="">請選擇...</option>
                                @foreach ($jobs as $job)
                                    <option value="{{ $job->id }}">{{ $job->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">選單權限 <span class="text-danger">*</span></label>
                            <div>
                                <ul class="list-unstyled">
                                    {{-- 第一層 --}}
                                    @foreach ($menus->where('parent_id', null) as $parent)
                                        <li>
                                            <div style="padding-left:0;" class="menu-item">
                                                <input type="checkbox" class="menu-checkbox" name="menu_ids[]"
                                                    value="{{ $parent->id }}" data-id="{{ $parent->id }}"
                                                    data-parent-id="{{ $parent->parent_id }}">
                                                <span class="menu-text" style="cursor: pointer;">
                                                    <b>{{ $parent->name }}</b>
                                                    @if(isset($parent->comment))<small>（{{ $parent->comment }}）</small>@endif
                                                </span>
                                            </div>
                                            {{-- 第二層 --}}
                                            @php $children = $menus->where('parent_id', $parent->id); @endphp
                                            @if ($children->count())
                                                <ul class="list-unstyled">
                                                    @foreach ($children as $child)
                                                        <li>
                                                            <div style="padding-left:25px;" class="menu-item">
                                                                <input type="checkbox" class="menu-checkbox"
                                                                    name="menu_ids[]" value="{{ $child->id }}"
                                                                    data-id="{{ $child->id }}"
                                                                    data-parent-id="{{ $child->parent_id }}">
                                                                <span class="menu-text" style="cursor: pointer;">
                                                                    {{ $child->name }}@if(isset($child->comment))<small>（{{ $child->comment }}）</small>@endif
                                                                </span>
                                                            </div>
                                                            {{-- 第三層 --}}
                                                            @php $grand = $menus->where('parent_id', $child->id); @endphp
                                                            @if ($grand->count())
                                                                <ul class="list-unstyled">
                                                                    @foreach ($grand as $g)
                                                                        <li>
                                                                            <div style="padding-left:50px;" class="menu-item">
                                                                                <input type="checkbox" class="menu-checkbox"
                                                                                    name="menu_ids[]"
                                                                                    value="{{ $g->id }}"
                                                                                    data-id="{{ $g->id }}"
                                                                                    data-parent-id="{{ $g->parent_id }}">
                                                                                <span class="menu-text" style="cursor: pointer;">
                                                                                    {{ $g->name }}@if(isset($g->comment))<small>（{{ $g->comment }}）</small>@endif
                                                                                </span>
                                                                            </div>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-success">新增</button>
                                <button type="reset" class="btn btn-secondary" onclick="history.go(-1)">回上一頁</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Cascade checkbox control --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // 文字點擊事件
            $('.menu-text').on('click', function() {
                let checkbox = $(this).siblings('.menu-checkbox');
                checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
            });

            // Checkbox 變更事件
            $('.menu-checkbox').on('change', function() {
                let id = $(this).data('id');
                let checked = $(this).prop('checked');
                // 向下：所有子節點一起勾/取消
                $('.menu-checkbox[data-parent-id="' + id + '"]').each(function() {
                    $(this).prop('checked', checked).trigger('change');
                });

                // 向上：勾選會自動打勾所有父層，取消則自動檢查父層要不要勾
                if (checked) {
                    let parentId = $(this).data('parent-id');
                    while (parentId) {
                        let parent = $('.menu-checkbox[data-id="' + parentId + '"]');
                        parent.prop('checked', true);
                        parentId = parent.data('parent-id');
                    }
                } else {
                    // 取消勾選，往上檢查有沒有其他兄弟沒勾，父層才能跟著取消
                    let parentId = $(this).data('parent-id');
                    while (parentId) {
                        let siblingsChecked = $('.menu-checkbox[data-parent-id="' + parentId + '"]:checked')
                            .length;
                        let parent = $('.menu-checkbox[data-id="' + parentId + '"]');
                        if (siblingsChecked === 0) {
                            parent.prop('checked', false);
                        }
                        parentId = parent.data('parent-id');
                    }
                }
            });
        });
    </script>
@endsection

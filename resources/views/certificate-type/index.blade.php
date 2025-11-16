@extends('layouts.vertical', ['page_title' => '證照類別管理'])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">證照管理</a></li>
                        <li class="breadcrumb-item active">證照類別管理</li>
                    </ol>
                </div>
                <h4 class="page-title">證照類別管理</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('certificate-type.index') }}" method="GET">
                                <div class="me-3">
                                    <label for="name" class="form-label">類別名稱</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ request('name') }}">
                                </div>
                                <div class="me-3 mt-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>查詢</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-auto" style="margin-top: 26px;">
                            <div class="text-lg-end my-1 my-lg-0">
                                <a href="{{ route('certificate-type.create') }}" class="btn btn-primary waves-effect waves-light">
                                    <i class="mdi mdi-plus-circle me-1"></i>新增證照類別
                                </a>
                            </div>
                        </div><!-- end col-->
                    </div> <!-- end row -->
                </div>
            </div> <!-- end card -->
        </div> <!-- end col-->
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>類別名稱</th>
                                    <th>狀態</th>
                                    <th>建立日期</th>
                                    <th>動作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($certificateTypes ?? [] as $type)
                                <tr>
                                    <td>{{ $type->name }}</td>
                                    <td>
                                        @if($type->status == 1)
                                            <span class="badge bg-success">啟用</span>
                                        @else
                                            <span class="badge bg-secondary">停用</span>
                                        @endif
                                    </td>
                                    <td>{{ $type->created_at ? $type->created_at->format('Y-m-d') : '-' }}</td>
                                    <td>
                                        <div class="btn-group dropdown">
                                            <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect" data-bs-toggle="dropdown" aria-expanded="false">動作 <i class="mdi mdi-arrow-down-drop-circle"></i></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="{{ route('certificate-type.edit', $type->id) }}"><i class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯</a>
                                                <a class="dropdown-item" href="#" onclick="deleteCertificateType({{ $type->id }})"><i class="mdi mdi-delete me-2 font-18 text-muted vertical-middle"></i>刪除</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">目前沒有證照類別資料</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>
    <!-- end row -->

</div> <!-- container -->
@endsection

@section('script')
<script>
function deleteCertificateType(id) {
    if (confirm('確定要刪除此證照類別嗎？')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/certificate-type/${id}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection

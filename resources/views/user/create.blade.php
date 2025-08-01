@extends('layouts.vertical', ['page_title' => 'Create Project'])

@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- third party css end -->
@endsection

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">用戶管理</a></li>
                            <li class="breadcrumb-item active">新增用戶</li>
                        </ol>
                    </div>
                    <h4 class="page-title">新增用戶</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('user.create.data') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label for="project-priority" class="form-label">分區<span
                                                class="text-danger">*</span></label>

                                        <select class="form-control" data-toggle="select" data-width="100%"
                                            name="branch_id">
                                            @foreach ($branchs as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="project-priority" class="form-label">職稱<span
                                                class="text-danger">*</span></label>

                                        <select class="form-control" data-toggle="select" data-width="100%" name="job_id">
                                            @foreach ($jobs as $job)
                                                <option value="{{ $job->id }}">{{ $job->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">入職時間<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" data-toggle="flatpicker"
                                                name="entry_date" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="projectname" class="form-label">排序</label>
                                        <input type="text" class="form-control" name="seq" value="0">
                                    </div>

                                </div> <!-- end col-->

                                <div class="col-xl-6">
                                    <div>
                                        <div class="mb-3">
                                            <label for="projectname" class="form-label">姓名<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="projectname" class="form-control" name="name"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="projectname" class="form-label">帳號<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="projectname" class="form-control" name="username"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="project-overview" class="form-label">密碼<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="projectname" class="form-control" name="password"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="project-budget" class="form-label">電話<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="project-budget" class="form-control" name="mobile"
                                                required>
                                        </div>
                                    </div> <!-- end col-->
                                </div>
                                <!-- end row -->

                                <div class="mb-3">
                                    <label for="project-budget" class="form-label">備註</label>
                                    <textarea name="comment" id="comment" class="form-control" rows="3"></textarea>
                                </div>


                                <div class="row mt-3">
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-success waves-effect waves-light m-1"><i
                                                class="fe-check-circle me-1"></i>建立</button>
                                        <button type="reset" class="btn btn-secondary waves-effect waves-light m-1"
                                            onclick="history.go(-1)"><i class="fe-x me-1"></i>回上一頁</button>
                                    </div>
                                </div>
                        </form>
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div>
        <!-- end row-->

    </div> <!-- container -->
@endsection

@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <!-- third party js ends -->

    <!-- demo app -->
    <script src="{{ asset('assets/js/pages/create-project.init.js') }}"></script>
    <!-- end demo js-->
    <script>
        function updateBranches() {
            const bankCode = document.getElementById('bank').value;
            const branchSelect = document.getElementById('branch');

            // 清空舊的分行選項
            branchSelect.innerHTML = '<option value="">載入中...</option>';

            if (bankCode) {
                fetch(`/api/banks/${bankCode}/branches`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log(data);
                        branchSelect.innerHTML = '<option value="">請選擇分行</option>';

                        // 確認數據格式
                        if (Array.isArray(data)) {
                            data.forEach(branch => {
                                const option = document.createElement('option');
                                option.value = branch['分支機構代號'];
                                option.textContent = `${branch['分支機構名稱']} (${branch['分支機構代號']})`;
                                branchSelect.appendChild(option);
                            });
                        } else {
                            console.error('Data format error:', data);
                            branchSelect.innerHTML = '<option value="">數據格式錯誤</option>';
                        }
                    })
                    .catch((error) => {
                        console.error('Fetch error:', error);
                        branchSelect.innerHTML = '<option value="">載入失敗</option>';
                    });
            } else {
                branchSelect.innerHTML = '<option value="">請先選擇銀行</option>';
            }
        }
    </script>
@endsection

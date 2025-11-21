@extends('layouts.vertical', ['page_title' => '編輯用戶'])

@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/quill/quill.min.css') }}" rel="stylesheet" type="text/css" />
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
                            <li class="breadcrumb-item active">編輯用戶</li>
                        </ol>
                    </div>
                    <h4 class="page-title">編輯用戶</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="row">
            <div class="col-lg-12">
                @if (Auth::user()->level == 0 || Auth::user()->level == 1)
                    @if (session('hint') == '1')
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            恭喜你修改個人資料成功！
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                @endif
            </div>
            <div class="col-lg-6">
                <div class="card">

                    <div class="card-body">

                        <form action="{{ route('user.edit.data', $user->id) }}" method="POST">
                            @csrf
                            <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">員工編號設定</h5>

                            <div class="mb-3">
                                <label class="form-label">員工編號<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="seq" value="{{ $user->seq }}">
                            </div>
                            <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">個人資訊</h5>

                            <div class="mb-3">
                                <label class="form-label">入職時間<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" data-toggle="flatpicker" name="entry_date"
                                    value="{{ $user->entry_date }}">
                            </div>

                            <div class="mb-3">
                                <label for="project-priority" class="form-label">分館<span
                                        class="text-danger">*</span></label>

                                <select class="form-control" data-toggle="select" data-width="100%" name="job_id">
                                    @foreach ($branchs as $branch)
                                        <option value="{{ $user->branch_id == $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="project-priority" class="form-label">職稱<span
                                        class="text-danger">*</span></label>

                                <select class="form-control" data-toggle="select" data-width="100%" name="job_id">
                                    <option value="" selected>請選擇</option>
                                    @foreach ($jobs as $job)
                                        <option value="{{ $job->id }}"
                                            @if ($user->job_id == $job->id) selected @endif>{{ $job->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">姓名<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ $user->name }}">
                            </div>

                            <div class="mb-3">
                                <label for="project-priority" class="form-label">生理性別<span
                                        class="text-danger">*</span></label>

                                <select class="form-control" data-toggle="select" data-width="100%" name="sex">
                                    <option value="" selected>請選擇</option>
                                    <option value="男生" @if ($user->sex == '男生') selected @endif>男生</option>
                                    <option value="女生" @if ($user->sex == '女生') selected @endif>女生</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">生日<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="birthday" value="{{ $user->birthday }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">身份證<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ic_card" value="{{ $user->ic_card }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">聯絡電話<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="mobile" value="{{ $user->mobile }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">信箱E-mail<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="email" value="{{ $user->email }}">
                            </div>

                            <div class="mb-3">
                                {{-- <div class="col-lg-6">
                            <label class="form-label">就讀學校/科系<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="education_school" value="{{ $user->education_school }}">
                        </div> --}}
                                <label class="form-label">學歷<span class="text-danger">*</span></label>
                                <select class="form-control" data-toggle="select" data-width="100%"
                                    name="education_level">
                                    <option value="請選擇" @if ($user->education_level == '請選擇') selected @endif>請選擇</option>
                                    <option value="國中" @if ($user->education_level == '國中') selected @endif>國中</option>
                                    <option value="高中" @if ($user->education_level == '高中') selected @endif>高中</option>
                                    <option value="大學" @if ($user->education_level == '大學') selected @endif>大學</option>
                                    <option value="碩士" @if ($user->education_level == '碩士') selected @endif>碩士</option>
                                    <option value="博士" @if ($user->education_level == '博士') selected @endif>博士</option>
                                </select>
                                {{-- <div class="col-lg-3">
                            <label class="form-label">狀態<span class="text-danger">*</span></label>
                            <select class="form-control" data-toggle="select" data-width="100%" name="is_graduated">
                                <option value="請選擇" @if ($user->is_graduated == '請選擇') selected @endif>請選擇</option>
                                <option value="畢業" @if ($user->is_graduated == '畢業') selected @endif>畢業</option>
                                <option value="肄業" @if ($user->is_graduated == '肄業') selected @endif>肄業</option>
                            </select>
                        </div> --}}
                            </div>

                            <div class="mb-3">
                                <label class="form-label">婚姻狀況<span class="text-danger">*</span></label>
                                <select class="form-control" data-toggle="select" data-width="100%" name="marriage">
                                    <option value="請選擇" @if ($user->marriage == '請選擇') selected @endif>請選擇</option>
                                    <option value="結婚" @if ($user->marriage == '結婚') selected @endif>結婚</option>
                                    <option value="未婚" @if ($user->marriage == '未婚') selected @endif>未婚</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">通訊地址<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="address" value="{{ $user->address }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">戶籍地址<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="census_address"
                                    value="{{ $user->census_address }}">
                            </div>

                            {{-- <div class="row">
                                <div class="col-lg-4">
                                    <!-- Date View -->
                                    <div class="mb-3">
                                        <label class="form-label">舊薪資帳戶<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="{{ $user->bank_id }}"
                                            placeholder="銀行代碼" name="bank_id">
                                    </div>

                                </div>
                                <div class="col-lg-8">
                                    <!-- Date View -->
                                    <div class="mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <input type="text" class="form-control"name="bank_number1"
                                            value="{{ $user->bank_number }}" placeholder="帳戶號碼">
                                    </div>
                                </div>
                            </div> --}}

                            <div class="row">
                                <div class="col-lg-3">
                                    <!-- Date View -->
                                    <div class="mb-3">
                                        <label for="bank">薪資帳戶</label>
                                        <select id="bank" name="bank" class="form-control"
                                            onchange="updateBranches()">
                                            <option value="">請選擇銀行</option>
                                            @foreach ($groupedBanks as $bankCode => $branches)
                                                <option value="{{ $bankCode }}"
                                                    @if ($user->bank == $bankCode) selected @endif>
                                                    {{ $branches->first()['金融機構名稱'] }}
                                                    ({{ $bankCode }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                                <div class="col-lg-3">
                                    <!-- Date View -->
                                    <div class="mb-3">
                                        <div class="form-group">
                                            <label for="branch">選擇分行</label>
                                            <select id="branch" name="branch" class="form-control">
                                                @if ($user->bank)
                                                    @foreach ($groupedBanks[$user->bank] as $branch)
                                                        <option value="{{ $branch['分支機構代號'] }}"
                                                            @if ($user->branch == $branch['分支機構代號']) selected @endif>
                                                            {{ $branch['分支機構名稱'] }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option value="">請先選擇銀行</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <!-- Date View -->
                                    <div class="mb-3">
                                        <label for="bank_number">帳戶號碼</label>
                                        <input type="text" class="form-control" name="bank_number"
                                            value="{{ $user->bank_number }}">
                                    </div>
                                </div>
                            </div>
                            @if (Auth::user()->level == '0' || Auth::user()->level == '1')
                                <br>
                                <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">權限設定</h5>
                                <div class="mb-3">
                                    <label class="mb-2">等級<span class="text-danger">*</span></label>
                                    <br />
                                    @if ($user->level != '0')
                                        <div class="radio form-check-inline">
                                            <input type="radio" id="level1" name="level" value="1"
                                                @if ($user->level == '1') checked @endif>
                                            <label for="level1"> 管理員 </label>
                                        </div>
                                        <div class="radio form-check-inline">
                                            <input type="radio" id="level2" name="level" value="2"
                                                @if ($user->level == '2') checked @endif>
                                            <label for="level2"> 一般員工 </label>
                                        </div>
                                    @else
                                        <div class="radio form-check-inline">
                                            <input type="radio" id="level3" name="level" value="0"
                                                @if ($user->level == '0') checked @endif>
                                            <label for="level3"><span class="text-danger">超級管理員</span></label>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="mb-2">權限<span class="text-danger">*</span></label>
                                    <br />
                                    <div class="radio form-check-inline">
                                        <input type="radio" id="status1" name="status" value="0"
                                            @if ($user->status == '0') checked @endif>
                                        <label for="status1"> 開通 </label>
                                    </div>
                                    <div class="radio form-check-inline">
                                        <input type="radio" id="status2" name="status" value="1"
                                            @if ($user->status == '1') checked @endif>
                                        <label for="status2"> 關閉 </label>
                                    </div>
                                </div>
                            @endif

                            
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col -->

            <div class="col-lg-6">

                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">緊急聯絡人</h5>

                        <div class="mb-3">
                            <label class="form-label">緊急聯絡人姓名<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="urgent_name"
                                value="{{ $user->urgent_name }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">緊急聯絡人關係<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="urgent_relation"
                                value="{{ $user->urgent_relation }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">緊急聯絡人電話<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="urgent_mobile"
                                value="{{ $user->urgent_mobile }}">
                        </div>
                    </div>
                </div> <!-- end card -->

                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">離職日期</h5>

                        <div class="mb-3">
                            <label class="form-label">離職時間<span class="text-danger">*</span></label>
                            <input type="date" class="form-control" data-toggle="flatpicker" name="resign_date"
                                value="{{ $user->resign_date }}">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">備註</h5>
                        <textarea name="comment" id="comment" class="form-control" rows="5">{{ $user->comment }}</textarea>
                    </div>
                </div>

            </div> <!-- end col-->

        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-12">
                <div class="text-center mb-3">
                    <button type="submit" class="btn w-sm btn-success waves-effect waves-light">修改</button>
                    <a href="{{ route('users') }}"><button type="button"
                            class="btn w-sm btn-secondary waves-effect">回上一頁</button></a>
                    {{-- <button type="submit" class="btn w-sm btn-danger waves-effect waves-light">Delete</button> --}}
                </div>
            </div> <!-- end col -->
        </div>
        </form>
        <!-- end row -->


        <!-- file preview template -->
        <div class="d-none" id="uploadPreviewTemplate">
            <div class="card mt-1 mb-0 shadow-none border">
                <div class="p-2">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <img data-dz-thumbnail src="#" class="avatar-sm rounded bg-light" alt="">
                        </div>
                        <div class="col ps-0">
                            <a href="javascript:void(0);" class="text-muted fw-bold" data-dz-name></a>
                            <p class="mb-0" data-dz-size></p>
                        </div>
                        <div class="col-auto">
                            <!-- Button -->
                            <a href="" class="btn btn-link btn-lg text-muted" data-dz-remove>
                                <i class="dripicons-cross"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div> <!-- container -->
@endsection

@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/libs/quill/quill.min.js') }}"></script>
    <!-- third party js ends -->

    <!-- demo app -->
    <script src="{{ asset('assets/js/pages/form-fileuploads.init.js') }}"></script>
    <script src="{{ asset('assets/js/pages/add-product.init.js') }}"></script>
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

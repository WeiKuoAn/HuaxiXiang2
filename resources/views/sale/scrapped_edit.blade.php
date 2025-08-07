@extends('layouts.vertical', ['page_title' => '編輯報廢單'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">業務管理</a></li>
                            <li class="breadcrumb-item active">編輯報廢單</li>
                        </ol>
                    </div>
                    <h4 class="page-title">編輯報廢單</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <form action="{{ route('sale.scrapped.update', $scrapped->id) }}" method="POST">
            @method('PUT')
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            @csrf
                            <div class="row">
                                <div class="mb-3">
                                    <label for="sale_date" class="form-label">日期<span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="sale_date" name="sale_date" value="{{ $scrapped->sale_date }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="sale_on" class="form-label">單號<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="sale_on" name="sale_on" value="{{ $scrapped->sale_on }}" required>
                                    <div id="sale_on_feedback"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">報廢原因</label>
                                    <select class="form-control" id="scrap_reason" name="comm" required>
                                        <option value="">請選擇報廢原因</option>
                                        <option value="寫錯" @if($scrapped->comm == '寫錯') selected @endif>寫錯</option>
                                        <option value="更改內容" @if($scrapped->comm == '更改內容') selected @endif>更改內容</option>
                                        <option value="跳號（未開立）" @if($scrapped->comm == '跳號（未開立）') selected @endif>跳號（未開立）</option>
                                        <option value="其他" @if(!in_array($scrapped->comm, ['寫錯', '更改內容', '跳號（未開立）', ''])) selected @endif>其他</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="other_reason_container" style="display: {{ !in_array($scrapped->comm, ['寫錯', '更改內容', '跳號（未開立）', '']) ? 'block' : 'none' }};">
                                    <label class="form-label">其他原因說明</label>
                                    <textarea class="form-control" rows="3" placeholder="請說明其他原因" id="other_reason" name="comm">{{ !in_array($scrapped->comm, ['寫錯', '更改內容', '跳號（未開立）', '']) ? $scrapped->comm : '' }}</textarea>
                                </div>
                            </div> <!-- end col-->
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-success waves-effect waves-light m-1"><i
                                        class="fe-check-circle me-1"></i>更新</button>
                                <button type="reset" class="btn btn-secondary waves-effect waves-light m-1"
                                    onclick="history.go(-1)"><i class="fe-x me-1"></i>回上一頁</button>
                            </div>
                        </div>
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </form>
    </div>
@endsection

@section('script')
    <!-- third party js -->

    <script src="{{ asset('assets/js/twzipcode-1.4.1-min.js') }}"></script>
    <script src="{{ asset('assets/js/twzipcode.js') }}"></script>
    <script src="{{ asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <!-- third party js ends -->

    <script>
        $(document).ready(function() {
            // 初始化時檢查是否為「其他」原因
            const initialValue = $('#scrap_reason').val();
            if (initialValue === '其他') {
                $('#other_reason_container').show();
                $('#other_reason').attr('name', 'comm');
                $('#scrap_reason').removeAttr('name');
            }

            // 處理報廢原因下拉選單
            $('#scrap_reason').change(function() {
                const selectedValue = $(this).val();
                const otherContainer = $('#other_reason_container');
                const otherTextarea = $('#other_reason');
                
                if (selectedValue === '其他') {
                    otherContainer.show();
                    otherTextarea.attr('name', 'comm');
                    $(this).removeAttr('name'); // 移除 select 的 name 屬性
                } else {
                    otherContainer.hide();
                    otherTextarea.removeAttr('name');
                    $(this).attr('name', 'comm'); // 恢復 select 的 name 屬性
                }
            });

            // 單號重複檢查（編輯時排除自己的單號）
            let saleOnCheckTimer;
            let isSaleOnValid = true; // 追蹤單號是否有效
            const originalSaleOn = '{{ $scrapped->sale_on }}';
            
            $('#sale_on').on('input', function() {
                const saleOn = $(this).val().trim();
                const feedback = $('#sale_on_feedback');
                
                // 清除之前的計時器
                clearTimeout(saleOnCheckTimer);
                
                // 清空之前的反饋
                feedback.html('').removeClass('text-danger text-success text-warning');
                
                // 如果輸入為空，不進行檢查
                if (!saleOn) {
                    isSaleOnValid = true;
                    return;
                }
                
                // 檢查單號格式（必須包含數字）
                if (!/\d/.test(saleOn)) {
                    feedback.html('<small class="text-warning">請輸入包含數字的單號</small>').addClass('text-warning');
                    isSaleOnValid = false;
                    return;
                }
                
                // 如果單號沒有改變，不需要檢查
                if (saleOn === originalSaleOn) {
                    feedback.html('<small class="text-success">✓ 單號未變更</small>').addClass('text-success');
                    isSaleOnValid = true;
                    return;
                }
                
                // 延遲 500ms 後進行檢查，避免頻繁請求
                saleOnCheckTimer = setTimeout(function() {
                    $.ajax({
                        type: 'GET',
                        url: '{{ route('sale.check_sale_on') }}',
                        data: {
                            'sale_on': saleOn
                        },
                        success: function(response) {
                            if (response.exists) {
                                feedback.html('<small class="text-danger">⚠️ ' + response.message + '</small>').addClass('text-danger');
                                isSaleOnValid = false;
                            } else {
                                feedback.html('<small class="text-success">✓ ' + response.message + '</small>').addClass('text-success');
                                isSaleOnValid = true;
                            }
                        },
                        error: function(xhr, status, error) {
                            feedback.html('<small class="text-danger">檢查單號時發生錯誤</small>').addClass('text-danger');
                            isSaleOnValid = false;
                            console.error('單號檢查錯誤:', error);
                        }
                    });
                }, 500);
            });

            // 表單提交檢查
            $('form').on('submit', function(e) {
                if (!isSaleOnValid) {
                    e.preventDefault();
                    alert('單號有重複，請檢查後再提交');
                    return false;
                }
            });
        });
    </script>

    <!-- demo app -->
    <script src="{{ asset('assets/js/pages/create-project.init.js') }}"></script>
    <!-- end demo js-->
@endsection

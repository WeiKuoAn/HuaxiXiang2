<!-- Modal 版本的報廢單確認對帳表單 - 只包含內容部分 -->
<form action="{{ route('sale.scrapped.check.data', $scrapped->id) }}" method="POST" id="modal-scrapped-form">
    @method('PUT')
    @csrf
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">報廢單資訊</h5>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="sale_date" class="form-label">日期<span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="sale_date" name="sale_date"
                                value="{{ $scrapped->sale_date }}" readonly>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="sale_on" class="form-label">單號<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sale_on" name="sale_on"
                                value="{{ $scrapped->sale_on }}" readonly>
                        </div>
                        <div class="mb-3 col-md-12">
                            <label class="form-label">報廢原因</label>
                            <div class="form-control" style="min-height: 38px; background-color: #f5f5f5;">
                                {{ $scrapped->comm }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="text-center mb-3">
                            @if (Auth::user()->level != '2' || Auth::user()->job_id == 9)
                                @if ($scrapped->status == '1')
                                    <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">取消</button>
                                    <button type="submit" class="btn w-sm btn-success waves-effect waves-light" value="check" name="admin_check" onclick="return confirm('是否已確定對帳，若要取消對帳，請進行撤回')">確定對帳</button>
                                @elseif($scrapped->status == '3')
                                    <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">取消</button>
                                    <button type="submit" class="btn w-sm btn-danger waves-effect" value="not_check" name="admin_check">撤回對帳</button>
                                    <button type="submit" class="btn w-sm btn-success waves-effect waves-light" value="check" name="admin_check" onclick="return confirm('是否已確定對帳，若要取消對帳，請進行撤回')">確定對帳</button>
                                @elseif($scrapped->status == '9')
                                    <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">取消</button>
                                    <button type="submit" class="btn w-sm btn-success waves-effect waves-light" value="reset" name="admin_check">還原</button>
                                @else
                                    <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">關閉</button>
                                @endif
                            @else
                                @if ($scrapped->status == '1' && $scrapped->user_id == Auth::user()->id)
                                    <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">取消</button>
                                    <button type="submit" class="btn w-sm btn-success waves-effect waves-light" value="usercheck" name="user_check" onclick="return confirm('是否已確定對帳，若要取消對帳，請進行撤回')">確定對帳</button>
                                @elseif($scrapped->status == '3' || $scrapped->status == '9')
                                    @if($scrapped->status == '3' && $scrapped->user_id != Auth::user()->id)
                                        <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">取消</button>
                                        <button type="submit" class="btn w-sm btn-danger waves-effect" value="not_check" name="admin_check">撤回對帳</button>
                                        <button type="submit" class="btn w-sm btn-success waves-effect waves-light" value="check" name="admin_check" onclick="return confirm('是否已確定對帳，若要取消對帳，請進行撤回')">確定對帳</button>
                                    @elseif($scrapped->status == '9')
                                        <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">取消</button>
                                        @if(Auth::user()->job_id == 10 || Auth::user()->job_id == 3)
                                            @if($scrapped->user_id != Auth::user()->id)
                                                <button type="submit" class="btn w-sm btn-success waves-effect waves-light" value="reset" name="admin_check">還原</button>
                                            @endif
                                        @endif
                                    @else
                                        <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">關閉</button>
                                    @endif
                                @else
                                    <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">關閉</button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // Modal 專用的初始化腳本
    $(document).ready(function() {
        console.log('Scrapped modal form loaded');
        
        // 注意：不要使用 disabled，因為 disabled 的欄位不會被提交
        // 所有欄位已經在 HTML 中設置為 readonly
    });
</script>


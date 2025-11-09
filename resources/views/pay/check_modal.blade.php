<style>
    @media screen and (max-width:768px) {
        .mobile {
            width: 120px;
        }
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">支出總資訊</h5>
                <div class="row">
                    <div class="mb-3 col-md-3">
                        <label for="pay_on" class="form-label">支出單號<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pay_on" name="pay_on"
                            value="{{ $data->pay_on }}" readonly>
                    </div>
                    <div class="mb-3 col-md-3">
                        <label for="price" class="form-label">總金額<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="price" name="price"
                            value="{{ $data->price }}" readonly>
                    </div>
                    <div class="mb-3 col-md-3">
                        <label for="comment" class="form-label">用途說明</label>
                        <input type="text" class="form-control" id="comment" name="comment"
                            value="{{ $data->comment }}" readonly>
                    </div>
                    <div class="mb-3 col-md-3">
                        <label for="user_id" class="form-label">服務專員<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="user_id" name="user_id"
                            value="{{ $data->user_name->name }}" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">發票清單</h5>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="cart" class="table cart-list">
                                <thead>
                                    <tr>
                                        <th>消費日期<span class="text-danger">*</span></th>
                                        <th>會計項目<span class="text-danger">*</span></th>
                                        <th>支出金額<span class="text-danger">*</span></th>
                                        <th>發票類型<span class="text-danger">*</span></th>
                                        <th></th>
                                        <th>備註<span class="text-danger">*</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($data->pay_items))
                                        @foreach ($data->pay_items as $key => $item)
                                            <tr id="row-{{ $key }}">
                                                <td scope="row">
                                                    <input id="pay_date-{{ $key }}"
                                                        class="mobile form-control" type="date"
                                                        name="pay_data_date[]" value="{{ $item->pay_date }}"
                                                        readonly>
                                                </td>
                                                <td>
                                                    <select id="pay_id-{{ $key }}" class="form-select"
                                                        aria-label="Default select example" name="pay_id[]"
                                                        disabled>
                                                        <option value="" selected>請選擇...</option>
                                                        @foreach ($pays as $pay)
                                                            <option value="{{ $pay->id }}"
                                                                @if ($pay->id == $item->pay_id) selected @endif>
                                                                {{ $pay->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input id="pay_price-{{ $key }}"
                                                        class="mobile form-control" type="text"
                                                        name="pay_price[]" value="{{ $item->price }}" readonly>
                                                </td>
                                                <td>
                                                    <select id="pay_invoice_type-{{ $key }}"
                                                        class="mobile form-select"
                                                        aria-label="Default select example"
                                                        name="pay_invoice_type[]" disabled>
                                                        <option value="" selected>請選擇</option>
                                                        <option @if ($item->invoice_type == 'FreeUniform') selected @endif
                                                            value="FreeUniform">免用統一發票</option>
                                                        <option @if ($item->invoice_type == 'Uniform') selected @endif
                                                            value="Uniform">統一發票</option>
                                                        <option @if ($item->invoice_type == 'Other') selected @endif
                                                            value="Other">其他</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input id="pay_invoice-{{ $key }}"
                                                        class="invoice mobile form-control" type="text"
                                                        name="pay_invoice_number[]" placeholder="請輸入發票號碼"
                                                        value="{{ $item->invoice_number }}" readonly>
                                                    <input list="vender_number_list_q" class="mobile form-control"
                                                        id="vendor-{{ $key }}" name="vender_id[]"
                                                        @if (isset($item->vender_data)) value="{{ $item->vender_id }}" @else value="{{ $item->vender_id }}" @endif
                                                        placeholder="請輸入統編號碼" readonly>
                                                    <datalist id="vender_number_list_q">
                                                    </datalist>
                                                </td>
                                                <td>
                                                    <input id="pay_text-{{ $key }}"
                                                        class="mobile form-control" type="text"
                                                        name="pay_text[]" value="{{ $item->comment }}" readonly>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('pay.check.data', $data->id) }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="text-center mb-3">
                <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">
                    關閉
                </button>
                @if ($data->status == 0)
                    @if ($data->user_id != Auth::user()->id)
                        <button type="submit" name="submit1" value="true" id="btn_submit"
                            class="btn w-sm btn-success waves-effect waves-light"
                            onclick="if(!confirm('是否確定審核?')){event.returnValue=false;return false;}">審核</button>
                        <button type="submit" name="submit1" value="return" id="btn_return"
                            class="btn w-sm btn-warning waves-effect waves-light"
                            onclick="if(!confirm('是否確定退回?')){event.returnValue=false;return false;}">退回</button>
                    @elseif(Auth::user()->level != 2)
                        <button type="submit" name="submit1" value="true" id="btn_submit"
                            class="btn w-sm btn-success waves-effect waves-light"
                            onclick="if(!confirm('是否確定審核?')){event.returnValue=false;return false;}">審核</button>
                    @endif
                @endif
            </div>
        </div>
    </div>
</form>


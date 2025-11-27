<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\CustGroup;
use App\Models\Customer;
use App\Models\CustomerBank;
use App\Models\CustomerMobile;
use App\Models\Lamp;
use App\Models\Plan;
use App\Models\Product;
use App\Models\PujaData;
use App\Models\Sale;
use App\Models\SaleCompanyCommission;
use App\Models\User;
use App\Models\Visit;
use Facade\FlareClient\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Whoops\Run;

class VisitController extends Controller
{
    public function search_district(Request $request)
    {
        $hospital_type = Str::contains($request->company_type, 'hospitals');  // 醫院

        if ($request->ajax()) {
            $output = '';

            $datas = Customer::where('group_id', 2)->where('county', $request->county)->get();

            $districts = [];
            foreach ($datas as $data) {
                $districts[] = $data->district;
            }
            $districts = array_unique($districts);

            if (isset($districts)) {
                foreach ($districts as $key => $district) {
                    $output .= '<option value="' . $district . '">' . $district . '</option>';
                }
            } else {
                $output .= '<option value="">請選擇...</option>';
            }
            // dd($output);
            return Response($output);
        }
    }

    public function index(Request $request, $id)
    {
        $datas = Visit::where('customer_id', $id)
            ->with(['user_name', 'supplement_by_user']);
        if (isset($request)) {
            $after_date = $request->after_date;
            if ($after_date) {
                $datas = $datas->where('date', '>=', $after_date);
            }
            $before_date = $request->before_date;
            if ($before_date) {
                $datas = $datas->where('date', '<=', $before_date);
            }
            $comment = $request->comment;
            if ($comment) {
                $comment = $request->comment . '%';
                $datas = $datas->where('comment', 'like', $comment);
            }
        }
        $datas = $datas->orderBy('date', 'desc')->paginate(50);
        $customer = Customer::where('id', $id)->first();
        return view('visit.index')->with('datas', $datas)->with('customer', $customer)->with('request', $request);
    }

    public function create(Request $request, $id)
    {
        $customer = Customer::where('id', $id)->first();
        $users = User::where('status', '0')->orderBy('name')->get();
        return view('visit.create')->with('customer', $customer)->with('users', $users);
    }

    public function store(Request $request, $id)
    {
        $customer = Customer::where('id', $id)->first();
        $data = new Visit;
        $data->customer_id = $request->customer_id;
        $data->date = $request->date;
        $data->visit_type = $request->visit_type ?? 'visit';
        
        // 根據拜訪類別處理不同欄位
        if ($request->visit_type === 'supply') {
            // 補給類別：處理補充項目和數量
            $supplementQuantities = $request->supplement_quantities ?? [];
            // 過濾掉數量為0或空的項目，只保留有數量的項目
            $supplementItems = [];
            $supplementTextParts = [];
            foreach ($supplementQuantities as $item => $quantity) {
                if (!empty($quantity) && $quantity > 0) {
                    $qty = (int)$quantity;
                    $supplementItems[$item] = $qty;
                    $supplementTextParts[] = $qty . '個' . $item;
                }
            }
            $data->supplement_items = !empty($supplementItems) ? $supplementItems : null;
            // 補給類別：「誰補」填入 supplement_by 欄位
            $data->supplement_by = $request->supplement_by;
            $data->user_id = null; // 補給類別不需要拜訪專員
            // 生成補給文字描述：補給5個大箱、3個小箱、10個DM
            // 確保 comment 不為 null（資料庫欄位不允許 null）
            $data->comment = !empty($supplementTextParts) ? '補給' . implode('、', $supplementTextParts) : '補給';
        } else {
            // 拜訪類別：處理拜訪紀錄和拜訪專員
            $data->comment = $request->comment ?? ''; // 確保不為 null
            $data->user_id = $request->user_id ?? Auth::user()->id;
            $data->supplement_items = null;
            $data->supplement_by = null;
        }
        
        $data->save();
        return redirect()->route('visits', $id)->with('customer', $customer);
    }

    public function show(Request $request, $cust_id, $id)
    {
        $customer = Customer::where('id', $cust_id)->first();
        $data = Visit::where('customer_id', $cust_id)->where('id', $id)
            ->with(['user_name', 'supplement_by_user'])
            ->first();
        $users = User::where('status', '0')->orderBy('name')->get();
        return view('visit.edit')->with('customer', $customer)->with('data', $data)->with('users', $users);
    }

    public function update(Request $request, $cust_id, $id)
    {
        // dd($id);
        $customer = Customer::where('id', $cust_id)->first();
        $data = Visit::where('customer_id', $cust_id)->where('id', $id)->first();
        // dd($data);
        $data->customer_id = $request->customer_id;
        $data->date = $request->date;
        $data->visit_type = $request->visit_type ?? 'visit';
        
        // 根據拜訪類別處理不同欄位
        if ($request->visit_type === 'supply') {
            // 補給類別：處理補充項目和數量
            $supplementQuantities = $request->supplement_quantities ?? [];
            // 過濾掉數量為0或空的項目，只保留有數量的項目
            $supplementItems = [];
            $supplementTextParts = [];
            foreach ($supplementQuantities as $item => $quantity) {
                if (!empty($quantity) && $quantity > 0) {
                    $qty = (int)$quantity;
                    $supplementItems[$item] = $qty;
                    $supplementTextParts[] = $qty . '個' . $item;
                }
            }
            $data->supplement_items = !empty($supplementItems) ? $supplementItems : null;
            $data->supplement_by = $request->supplement_by;
            // 生成補給文字描述：補給5個大箱、3個小箱、10個DM
            // 確保 comment 不為 null（資料庫欄位不允許 null）
            $data->comment = !empty($supplementTextParts) ? '補給' . implode('、', $supplementTextParts) : '補給';
            $data->user_id = null; // 補給類別不需要拜訪專員
        } else {
            // 拜訪類別：處理拜訪紀錄和拜訪專員
            $data->comment = $request->comment ?? ''; // 確保不為 null
            // 拜訪專員固定為當前登入者
            $data->user_id = Auth::user()->id;
            // 拜訪類別：清除補給相關欄位
            $data->supplement_items = null;
            $data->supplement_by = null;
        }
        
        $data->save();
        return redirect()->route('visits', $cust_id)->with('customer', $customer);
    }

    public function delete(Request $request, $cust_id, $id)
    {
        $customer = Customer::where('id', $cust_id)->first();
        $data = Visit::where('customer_id', $cust_id)->where('id', $id)->first();
        return view('visit.del')->with('customer', $customer)->with('data', $data);
    }

    public function destroy(Request $request, $cust_id, $id)
    {
        $customer = Customer::where('id', $cust_id)->first();
        Visit::where('customer_id', $cust_id)->where('id', $id)->delete();
        return redirect()->route('visits', $id)->with('customer', $customer);
    }

    public function hospitals(Request $request)
    {
        $datas = Customer::where('group_id', 2);
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%' . $request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
            $county = $request->county;
            if ($county != 'null') {
                if (isset($county)) {
                    $datas = $datas->where('county', $county);
                } else {
                    $datas = $datas;
                }
            }
            $district = $request->district;
            if ($district != 'null') {
                if (isset($district)) {
                    $datas = $datas->where('district', $district);
                } else {
                    $datas = $datas;
                }
            }
            $commission = $request->commission;
            if ($commission != 'null') {
                if (isset($commission)) {
                    $datas = $datas->where('commission', $commission);
                } else {
                    $datas = $datas;
                }
            }

            $has_bank_account = $request->has_bank_account;
            if ($has_bank_account != 'null') {
                if (isset($has_bank_account)) {
                    if ($has_bank_account == '1') {
                        // 有匯款帳號：bank 不為空且 bank_number 不為空
                        $datas = $datas->whereNotNull('bank')->whereNotNull('bank_number')->where('bank_number', '!=', '');
                    } else {
                        // 沒有匯款帳號：bank 為空或 bank_number 為空
                        $datas = $datas->where(function ($query) {
                            $query
                                ->whereNull('bank')
                                ->orWhereNull('bank_number')
                                ->orWhere('bank_number', '=', '');
                        });
                    }
                }
            }

            $contract_status = $request->contract_status;
            if ($contract_status != 'null') {
                if (isset($contract_status)) {
                    $datas = $datas->where('contract_status', $contract_status);
                }
            }
            $assigned_to = $request->assigned_to;
            if ($assigned_to != 'null') {
                if (isset($assigned_to)) {
                    $datas = $datas->where('assigned_to', $assigned_to);
                }
            }
            $seq = $request->seq;
            $recently_date_sort = $request->recently_date_sort;

            // 如果兩個排序都有選擇，優先使用叫件日期排序
            if ($recently_date_sort != 'null' && isset($recently_date_sort)) {
                // 使用子查詢來排序叫件日期
                $datas = $datas->orderByRaw('(
                    SELECT MAX(sale_date) 
                    FROM sale_company_commission 
                    WHERE company_id = customer.id
                ) ' . $recently_date_sort);
            } elseif ($seq != 'null' && isset($seq)) {
                $datas = $datas->orderby('created_at', $seq);
            }
        }

        // 如果沒有選擇任何排序，使用預設排序
        if (!isset($request->seq) && !isset($request->recently_date_sort)) {
            $datas = $datas->orderby('name', 'desc');
        }

        $datas = $datas->with('assigned_to_name')->paginate(50);

        $data_countys = Customer::where('group_id', 2)->whereNotNull('county')->where('county', '!=', '')->get();
        foreach ($data_countys as $data_county) {
            $countys[] = $data_county->county;
        }
        $countys = array_unique($countys);

        if (isset($county)) {
            $data_districts = Customer::where('group_id', 2)->where('county', $county)->whereNotNull('district')->where('district', '!=', '')->get();
        } else {
            $data_districts = [];
        }
        $districts = [];
        foreach ($data_districts as $data_district) {
            $districts[] = $data_district->district;
        }
        $districts = array_unique($districts);

        $bankData = $this->getFlatBankData();  // flat 結構的銀行分行 JSON
        foreach ($datas as $data) {
            $data->visit_count = Visit::where('customer_id', $data->id)->count();

            // 新增銀行/分行中文欄位
            $data->bank_name = $this->getBankNameFromFlatJson($data->bank, $bankData);
            $data->branch_name = $this->getBranchNameFromFlatJson($data->bank, $data->branch, $bankData);

            // 叫件次數
            $data->sale_count = SaleCompanyCommission::whereNotIn('type', ['self'])->where('company_id', $data->id)->count();
            $data->recently_date = SaleCompanyCommission::whereNotIn('type', ['self'])->where('company_id', $data->id)->orderby('sale_date', 'desc')->value('sale_date');
        }

        // 獲取負責人列表
        $users = User::where('status', '0')->whereIn('job_id', [1,2,3,10])->orderBy('name')->get();

        return view('visit.hospitals')->with('datas', $datas)->with('request', $request)->with('countys', $countys)->with('districts', $districts)->with('users', $users);
    }

    public function etiquettes(Request $request)  // 禮儀社
    {
        $datas = Customer::where('group_id', 5);
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%' . $request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
            $county = $request->county;
            if ($county != 'null') {
                if (isset($county)) {
                    $datas = $datas->where('county', $county);
                }
            }
            $district = $request->district;
            if ($district != 'null') {
                if (isset($district)) {
                    $datas = $datas->where('district', $district);
                }
            }
            $commission = $request->commission;
            if ($commission != 'null') {
                if (isset($commission)) {
                    $datas = $datas->where('commission', $commission);
                }
            }

            $has_bank_account = $request->has_bank_account;
            if ($has_bank_account != 'null') {
                if (isset($has_bank_account)) {
                    if ($has_bank_account == '1') {
                        // 有匯款帳號：bank 不為空且 bank_number 不為空
                        $datas = $datas->whereNotNull('bank')->whereNotNull('bank_number')->where('bank_number', '!=', '');
                    } else {
                        // 沒有匯款帳號：bank 為空或 bank_number 為空
                        $datas = $datas->where(function ($query) {
                            $query
                                ->whereNull('bank')
                                ->orWhereNull('bank_number')
                                ->orWhere('bank_number', '=', '');
                        });
                    }
                }
            }

            $contract_status = $request->contract_status;
            if ($contract_status != 'null') {
                if (isset($contract_status)) {
                    $datas = $datas->where('contract_status', $contract_status);
                }
            }
            $assigned_to = $request->assigned_to;
            if ($assigned_to != 'null') {
                if (isset($assigned_to)) {
                    $datas = $datas->where('assigned_to', $assigned_to);
                }
            }
            $seq = $request->seq;
            $recently_date_sort = $request->recently_date_sort;

            // 如果兩個排序都有選擇，優先使用叫件日期排序
            if ($recently_date_sort != 'null' && isset($recently_date_sort)) {
                $datas = $datas->orderByRaw('(
                    SELECT MAX(sale_date) 
                    FROM sale_company_commission 
                    WHERE company_id = customer.id
                ) ' . $recently_date_sort);
            } elseif ($seq != 'null' && isset($seq)) {
                $datas = $datas->orderby('created_at', $seq);
            }
        }

        // 如果沒有選擇任何排序，使用預設排序
        if (!isset($request->seq) && !isset($request->recently_date_sort)) {
            $datas = $datas->orderby('name', 'desc');
        }

        $datas = $datas->with('assigned_to_name')->paginate(50);

        $bankData = $this->getFlatBankData();  // flat 結構的銀行分行 JSON
        foreach ($datas as $data) {
            $data->visit_count = Visit::where('customer_id', $data->id)->count();

            // 新增銀行/分行中文欄位
            $data->bank_name = $this->getBankNameFromFlatJson($data->bank, $bankData);
            $data->branch_name = $this->getBranchNameFromFlatJson($data->bank, $data->branch, $bankData);

            // 叫件次數
            $data->sale_count = SaleCompanyCommission::where('company_id', $data->id)->whereNotIn('type', ['self'])->count();
            $data->recently_date = SaleCompanyCommission::where('company_id', $data->id)->whereNotIn('type', ['self'])->orderby('sale_date', 'desc')->value('sale_date');
        }

        // 載入地區資料
        $data_countys = Customer::where('group_id', 5)->whereNotNull('county')->where('county', '!=', '')->get();
        foreach ($data_countys as $data_county) {
            $countys[] = $data_county->county;
        }
        $countys = array_unique($countys);

        $county = $request->county;
        if (isset($county)) {
            $data_districts = Customer::where('group_id', 5)->where('county', $county)->whereNotNull('district')->where('district', '!=', '')->get();
        } else {
            $data_districts = [];
        }
        $districts = [];
        foreach ($data_districts as $data_district) {
            $districts[] = $data_district->district;
        }
        $districts = array_unique($districts);

        // 獲取負責人列表
        $users = User::where('status', '0')->whereIn('job_id', [1,2,3,10])->orderBy('name')->get();

        return view('visit.etiquettes')->with('datas', $datas)->with('request', $request)->with('countys', $countys)->with('districts', $districts)->with('users', $users);
    }

    public function reproduces(Request $request)  // 繁殖場
    {
        $datas = Customer::where('group_id', 4);
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%' . $request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
            $county = $request->county;
            if ($county != 'null') {
                if (isset($county)) {
                    $datas = $datas->where('county', $county);
                }
            }
            $district = $request->district;
            if ($district != 'null') {
                if (isset($district)) {
                    $datas = $datas->where('district', $district);
                }
            }
            $commission = $request->commission;
            if ($commission != 'null') {
                if (isset($commission)) {
                    $datas = $datas->where('commission', $commission);
                }
            }

            $has_bank_account = $request->has_bank_account;
            if ($has_bank_account != 'null') {
                if (isset($has_bank_account)) {
                    if ($has_bank_account == '1') {
                        // 有匯款帳號：bank 不為空且 bank_number 不為空
                        $datas = $datas->whereNotNull('bank')->whereNotNull('bank_number')->where('bank_number', '!=', '');
                    } else {
                        // 沒有匯款帳號：bank 為空或 bank_number 為空
                        $datas = $datas->where(function ($query) {
                            $query
                                ->whereNull('bank')
                                ->orWhereNull('bank_number')
                                ->orWhere('bank_number', '=', '');
                        });
                    }
                }
            }

            $contract_status = $request->contract_status;
            if ($contract_status != 'null') {
                if (isset($contract_status)) {
                    $datas = $datas->where('contract_status', $contract_status);
                }
            }
            $assigned_to = $request->assigned_to;
            if ($assigned_to != 'null') {
                if (isset($assigned_to)) {
                    $datas = $datas->where('assigned_to', $assigned_to);
                }
            }
            $seq = $request->seq;
            $recently_date_sort = $request->recently_date_sort;

            // 如果兩個排序都有選擇，優先使用叫件日期排序
            if ($recently_date_sort != 'null' && isset($recently_date_sort)) {
                $datas = $datas->orderByRaw('(
                    SELECT MAX(sale_date) 
                    FROM sale_company_commission 
                    WHERE company_id = customer.id
                ) ' . $recently_date_sort);
            } elseif ($seq != 'null' && isset($seq)) {
                $datas = $datas->orderby('created_at', $seq);
            }
        }

        // 如果沒有選擇任何排序，使用預設排序
        if (!isset($request->seq) && !isset($request->recently_date_sort)) {
            $datas = $datas->orderby('name', 'desc');
        }

        $datas = $datas->with('assigned_to_name')->paginate(50);

        $bankData = $this->getFlatBankData();  // flat 結構的銀行分行 JSON
        foreach ($datas as $data) {
            $data->visit_count = Visit::where('customer_id', $data->id)->count();

            // 新增銀行/分行中文欄位
            $data->bank_name = $this->getBankNameFromFlatJson($data->bank, $bankData);
            $data->branch_name = $this->getBranchNameFromFlatJson($data->bank, $data->branch, $bankData);

            // 叫件次數
            $data->sale_count = SaleCompanyCommission::where('company_id', $data->id)->whereNotIn('type', ['self'])->count();
            $data->recently_date = SaleCompanyCommission::where('company_id', $data->id)->whereNotIn('type', ['self'])->orderby('sale_date', 'desc')->value('sale_date');
        }
        // 載入地區資料
        $data_countys = Customer::where('group_id', 4)->whereNotNull('county')->where('county', '!=', '')->get();
        foreach ($data_countys as $data_county) {
            $countys[] = $data_county->county;
        }
        $countys = array_unique($countys);

        $county = $request->county;
        if (isset($county)) {
            $data_districts = Customer::where('group_id', 4)->where('county', $county)->whereNotNull('district')->where('district', '!=', '')->get();
        } else {
            $data_districts = [];
        }
        $districts = [];
        foreach ($data_districts as $data_district) {
            $districts[] = $data_district->district;
        }
        $districts = array_unique($districts);

        // 獲取負責人列表
        $users = User::where('status', '0')->whereIn('job_id', [1,2,3,10])->orderBy('name')->get();

        return view('visit.reproduces')->with('datas', $datas)->with('request', $request)->with('countys', $countys)->with('districts', $districts)->with('users', $users);
    }

    public function dogparks(Request $request)  // 狗園
    {
        $datas = Customer::where('group_id', 3);
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%' . $request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
            $county = $request->county;
            if ($county != 'null') {
                if (isset($county)) {
                    $datas = $datas->where('county', $county);
                }
            }
            $district = $request->district;
            if ($district != 'null') {
                if (isset($district)) {
                    $datas = $datas->where('district', $district);
                }
            }
            $commission = $request->commission;
            if ($commission != 'null') {
                if (isset($commission)) {
                    $datas = $datas->where('commission', $commission);
                }
            }

            $has_bank_account = $request->has_bank_account;
            if ($has_bank_account != 'null') {
                if (isset($has_bank_account)) {
                    if ($has_bank_account == '1') {
                        // 有匯款帳號：bank 不為空且 bank_number 不為空
                        $datas = $datas->whereNotNull('bank')->whereNotNull('bank_number')->where('bank_number', '!=', '');
                    } else {
                        // 沒有匯款帳號：bank 為空或 bank_number 為空
                        $datas = $datas->where(function ($query) {
                            $query
                                ->whereNull('bank')
                                ->orWhereNull('bank_number')
                                ->orWhere('bank_number', '=', '');
                        });
                    }
                }
            }

            $contract_status = $request->contract_status;
            if ($contract_status != 'null') {
                if (isset($contract_status)) {
                    $datas = $datas->where('contract_status', $contract_status);
                }
            }
            $assigned_to = $request->assigned_to;
            if ($assigned_to != 'null') {
                if (isset($assigned_to)) {
                    $datas = $datas->where('assigned_to', $assigned_to);
                }
            }
            $seq = $request->seq;
            $recently_date_sort = $request->recently_date_sort;

            // 如果兩個排序都有選擇，優先使用叫件日期排序
            if ($recently_date_sort != 'null' && isset($recently_date_sort)) {
                $datas = $datas->orderByRaw('(
                    SELECT MAX(sale_date) 
                    FROM sale_company_commission 
                    WHERE company_id = customer.id
                ) ' . $recently_date_sort);
            } elseif ($seq != 'null' && isset($seq)) {
                $datas = $datas->orderby('created_at', $seq);
            }
        }

        // 如果沒有選擇任何排序，使用預設排序
        if (!isset($request->seq) && !isset($request->recently_date_sort)) {
            $datas = $datas->orderby('name', 'desc');
        }

        $datas = $datas->with('assigned_to_name')->paginate(50);

        $bankData = $this->getFlatBankData();  // flat 結構的銀行分行 JSON
        foreach ($datas as $data) {
            $data->visit_count = Visit::where('customer_id', $data->id)->count();

            // 新增銀行/分行中文欄位
            $data->bank_name = $this->getBankNameFromFlatJson($data->bank, $bankData);
            $data->branch_name = $this->getBranchNameFromFlatJson($data->bank, $data->branch, $bankData);

            // 叫件次數
            $data->sale_count = SaleCompanyCommission::whereNotIn('type', ['self'])->where('company_id', $data->id)->count();
            $data->recently_date = SaleCompanyCommission::whereNotIn('type', ['self'])->where('company_id', $data->id)->orderby('sale_date', 'desc')->value('sale_date');
        }
        // 載入地區資料
        $data_countys = Customer::where('group_id', 3)->whereNotNull('county')->where('county', '!=', '')->get();
        foreach ($data_countys as $data_county) {
            $countys[] = $data_county->county;
        }
        $countys = array_unique($countys);

        $county = $request->county;
        if (isset($county)) {
            $data_districts = Customer::where('group_id', 3)->where('county', $county)->whereNotNull('district')->where('district', '!=', '')->get();
        } else {
            $data_districts = [];
        }
        $districts = [];
        foreach ($data_districts as $data_district) {
            $districts[] = $data_district->district;
        }
        $districts = array_unique($districts);

        // 獲取負責人列表
        $users = User::where('status', '0')->whereIn('job_id', [1,2,3,10])->orderBy('name')->get();

        return view('visit.dogparks')->with('datas', $datas)->with('request', $request)->with('countys', $countys)->with('districts', $districts)->with('users', $users);
    }

    public function salons(Request $request)  // 美容院
    {
        $datas = Customer::where('group_id', 6);
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%' . $request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
            $county = $request->county;
            if ($county != 'null') {
                if (isset($county)) {
                    $datas = $datas->where('county', $county);
                }
            }
            $district = $request->district;
            if ($district != 'null') {
                if (isset($district)) {
                    $datas = $datas->where('district', $district);
                }
            }
            $commission = $request->commission;
            if ($commission != 'null') {
                if (isset($commission)) {
                    $datas = $datas->where('commission', $commission);
                }
            }

            $has_bank_account = $request->has_bank_account;
            if ($has_bank_account != 'null') {
                if (isset($has_bank_account)) {
                    if ($has_bank_account == '1') {
                        // 有匯款帳號：bank 不為空且 bank_number 不為空
                        $datas = $datas->whereNotNull('bank')->whereNotNull('bank_number')->where('bank_number', '!=', '');
                    } else {
                        // 沒有匯款帳號：bank 為空或 bank_number 為空
                        $datas = $datas->where(function ($query) {
                            $query
                                ->whereNull('bank')
                                ->orWhereNull('bank_number')
                                ->orWhere('bank_number', '=', '');
                        });
                    }
                }
            }

            $contract_status = $request->contract_status;
            if ($contract_status != 'null') {
                if (isset($contract_status)) {
                    $datas = $datas->where('contract_status', $contract_status);
                }
            }
            $assigned_to = $request->assigned_to;
            if ($assigned_to != 'null') {
                if (isset($assigned_to)) {
                    $datas = $datas->where('assigned_to', $assigned_to);
                }
            }
            $seq = $request->seq;
            $recently_date_sort = $request->recently_date_sort;

            // 如果兩個排序都有選擇，優先使用叫件日期排序
            if ($recently_date_sort != 'null' && isset($recently_date_sort)) {
                $datas = $datas->orderByRaw('(
                    SELECT MAX(sale_date) 
                    FROM sale_company_commission 
                    WHERE company_id = customer.id
                ) ' . $recently_date_sort);
            } elseif ($seq != 'null' && isset($seq)) {
                $datas = $datas->orderby('created_at', $seq);
            }
        }

        // 如果沒有選擇任何排序，使用預設排序
        if (!isset($request->seq) && !isset($request->recently_date_sort)) {
            $datas = $datas->orderby('name', 'desc');
        }

        $datas = $datas->with('assigned_to_name')->paginate(50);

        $bankData = $this->getFlatBankData();  // flat 結構的銀行分行 JSON
        foreach ($datas as $data) {
            $data->visit_count = Visit::where('customer_id', $data->id)->count();

            // 新增銀行/分行中文欄位
            $data->bank_name = $this->getBankNameFromFlatJson($data->bank, $bankData);
            $data->branch_name = $this->getBranchNameFromFlatJson($data->bank, $data->branch, $bankData);

            // 叫件次數
            $data->sale_count = SaleCompanyCommission::whereNotIn('type', ['self'])->where('company_id', $data->id)->count();
            $data->recently_date = SaleCompanyCommission::whereNotIn('type', ['self'])->where('company_id', $data->id)->orderby('sale_date', 'desc')->value('sale_date');
        }
        // 載入地區資料
        $data_countys = Customer::where('group_id', 6)->whereNotNull('county')->where('county', '!=', '')->get();
        foreach ($data_countys as $data_county) {
            $countys[] = $data_county->county;
        }
        $countys = array_unique($countys);

        $county = $request->county;
        if (isset($county)) {
            $data_districts = Customer::where('group_id', 6)->where('county', $county)->whereNotNull('district')->where('district', '!=', '')->get();
        } else {
            $data_districts = [];
        }
        $districts = [];
        foreach ($data_districts as $data_district) {
            $districts[] = $data_district->district;
        }
        $districts = array_unique($districts);

        // 獲取負責人列表
        $users = User::where('status', '0')->whereIn('job_id', [1,2,3,10])->orderBy('name')->get();

        return view('visit.salons')->with('datas', $datas)->with('request', $request)->with('countys', $countys)->with('districts', $districts)->with('users', $users);
    }

    public function others(Request $request)
    {
        $datas = Customer::where('group_id', 7);
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%' . $request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
            $county = $request->county;
            if ($county != 'null') {
                if (isset($county)) {
                    $datas = $datas->where('county', $county);
                }
            }
            $district = $request->district;
            if ($district != 'null') {
                if (isset($district)) {
                    $datas = $datas->where('district', $district);
                }
            }
            $commission = $request->commission;
            if ($commission != 'null') {
                if (isset($commission)) {
                    $datas = $datas->where('commission', $commission);
                }
            }

            $has_bank_account = $request->has_bank_account;
            if ($has_bank_account != 'null') {
                if (isset($has_bank_account)) {
                    if ($has_bank_account == '1') {
                        // 有匯款帳號：bank 不為空且 bank_number 不為空
                        $datas = $datas->whereNotNull('bank')->whereNotNull('bank_number')->where('bank_number', '!=', '');
                    } else {
                        // 沒有匯款帳號：bank 為空或 bank_number 為空
                        $datas = $datas->where(function ($query) {
                            $query
                                ->whereNull('bank')
                                ->orWhereNull('bank_number')
                                ->orWhere('bank_number', '=', '');
                        });
                    }
                }
            }

            $contract_status = $request->contract_status;
            if ($contract_status != 'null') {
                if (isset($contract_status)) {
                    $datas = $datas->where('contract_status', $contract_status);
                }
            }
            $assigned_to = $request->assigned_to;
            if ($assigned_to != 'null') {
                if (isset($assigned_to)) {
                    $datas = $datas->where('assigned_to', $assigned_to);
                }
            }
            $seq = $request->seq;
            $recently_date_sort = $request->recently_date_sort;

            // 如果兩個排序都有選擇，優先使用叫件日期排序
            if ($recently_date_sort != 'null' && isset($recently_date_sort)) {
                $datas = $datas->orderByRaw('(
                    SELECT MAX(sale_date) 
                    FROM sale_company_commission 
                    WHERE company_id = customer.id
                ) ' . $recently_date_sort);
            } elseif ($seq != 'null' && isset($seq)) {
                $datas = $datas->orderby('created_at', $seq);
            }
        }

        // 如果沒有選擇任何排序，使用預設排序
        if (!isset($request->seq) && !isset($request->recently_date_sort)) {
            $datas = $datas->orderby('name', 'desc');
        }

        $datas = $datas->with('assigned_to_name')->paginate(50);

        $bankData = $this->getFlatBankData();  // flat 結構的銀行分行 JSON
        foreach ($datas as $data) {
            $data->visit_count = Visit::where('customer_id', $data->id)->count();

            // 新增銀行/分行中文欄位
            $data->bank_name = $this->getBankNameFromFlatJson($data->bank, $bankData);
            $data->branch_name = $this->getBranchNameFromFlatJson($data->bank, $data->branch, $bankData);

            // 叫件次數
            $data->sale_count = SaleCompanyCommission::whereNotIn('type', ['self'])->where('company_id', $data->id)->count();
            $data->recently_date = SaleCompanyCommission::whereNotIn('type', ['self'])->where('company_id', $data->id)->orderby('sale_date', 'desc')->value('sale_date');
        }
        // 載入地區資料
        $data_countys = Customer::where('group_id', 7)->whereNotNull('county')->where('county', '!=', '')->get();
        foreach ($data_countys as $data_county) {
            $countys[] = $data_county->county;
        }
        $countys = array_unique($countys);

        $county = $request->county;
        if (isset($county)) {
            $data_districts = Customer::where('group_id', 7)->where('county', $county)->whereNotNull('district')->where('district', '!=', '')->get();
        } else {
            $data_districts = [];
        }
        $districts = [];
        foreach ($data_districts as $data_district) {
            $districts[] = $data_district->district;
        }
        $districts = array_unique($districts);

        // 獲取負責人列表
        $users = User::where('status', '0')->whereIn('job_id', [1,2,3,10])->orderBy('name')->get();

        return view('visit.others')->with('datas', $datas)->with('request', $request)->with('countys', $countys)->with('districts', $districts)->with('users', $users);
    }

    // 新增公司
    public function company_create(Request $request)
    {
        $users = User::where('status', '0')->whereIn('job_id', ['1', '2', '3', '5', '10'])->get();
        $json = file_get_contents(public_path('assets/data/banks.json'));
        $banks = collect(json_decode($json, true));
        $groupedBanks = $banks->groupBy('銀行代號/總機構代碼');
        $company_type = $request->headers->get('referer');

        return View('visit.company_create')->with('hint', 0)->with('company_type', $company_type)->with('groupedBanks', $groupedBanks)->with('users', $users);
    }

    public function company_store(Request $request)
    {
        // 準備視圖所需的變數（用於驗證失敗時返回）
        $users = User::where('status', '0')->whereIn('job_id', ['1', '2', '3', '5', '10'])->get();
        $json = file_get_contents(public_path('assets/data/banks.json'));
        $banks = collect(json_decode($json, true));
        $groupedBanks = $banks->groupBy('銀行代號/總機構代碼');
        
        // dd($request->company_type);
        $hospital_type = Str::contains($request->company_type, 'hospitals');  // 醫院
        $etiquette_type = Str::contains($request->company_type, 'etiquettes');  // 禮儀社
        $reproduce_type = Str::contains($request->company_type, 'reproduces');  // 繁殖場
        $dogpark_type = Str::contains($request->company_type, 'dogparks');  // 狗園
        $salons_type = Str::contains($request->company_type, 'salons');  // 美容院
        $others_type = Str::contains($request->company_type, 'others');  // 其他業者

        // 處理多筆電話
        $mobiles = $request->input('mobiles', []);
        
        // 檢查電話重複：檢查所有提交的電話，比對 customer.mobile 和 customer_mobile 表
        $data = null;
        if ($request->not_mobile != 1) {
            // 過濾掉空值
            $mobilesFiltered = array_filter($mobiles, function($mobile) {
                return !empty($mobile);
            });
            
            // 檢查每個電話是否重複
            foreach ($mobilesFiltered as $mobile) {
                // 檢查 customer 表的 mobile 欄位
                $customerExists = Customer::where('mobile', $mobile)->first();
                if ($customerExists) {
                    $data = $customerExists;
                    break;
                }
                
                // 檢查 customer_mobile 表
                $customerMobileExists = CustomerMobile::where('mobile', $mobile)->first();
                if ($customerMobileExists) {
                    $data = $customerMobileExists;
                    break;
                }
            }
        }
        
        if ($request->not_mobile == 1) {  // 未提供電話
            $customer = new Customer;
            $customer->name = $request->name;
            $customer->mobile = '未提供電話';
            $customer->county = $request->county;
            $customer->district = $request->district;
            $customer->address = $request->address;
            // 處理帳戶資訊：如果勾選「不提供帳戶」，則不儲存帳戶資訊
            if ($request->not_provide_bank != 1 && (!empty($request->bank) || !empty($request->branch) || !empty($request->bank_number))) {
                $customer->bank = $request->bank;
                $customer->branch = $request->branch;
                $customer->bank_number = $request->bank_number;
            }
            $customer->commission = $request->commission;
            $customer->visit_status = $request->visit_status;
            $customer->contract_status = $request->contract_status;
            $customer->assigned_to = $request->assigned_to;
        } else {
            if (isset($data)) {
                return view('visit.company_create')
                    ->with('hint', '1')
                    ->with('company_type', $request->company_type)
                    ->with('groupedBanks', $groupedBanks)
                    ->with('users', $users);
            } else {
                if (isset($data)) {
                    return view('visit.company_create')
                        ->with('hint', '1')
                        ->with('company_type', $request->company_type)
                        ->with('groupedBanks', $groupedBanks)
                        ->with('users', $users);
                } else {
                    $customer = new Customer;
                    $customer->name = $request->name;
                    // 處理多筆電話：第一筆寫入 customer.mobile
                    if (!empty($mobiles) && !empty($mobiles[0])) {
                        $customer->mobile = $mobiles[0];
                    } else {
                        $customer->mobile = '未提供電話';
                    }
                    $customer->county = $request->county;
                    $customer->district = $request->district;
                    $customer->address = $request->address;
                    // 處理帳戶資訊：如果勾選「不提供帳戶」，則不儲存帳戶資訊
                    if ($request->not_provide_bank != 1 && (!empty($request->bank) || !empty($request->branch) || !empty($request->bank_number))) {
                        $customer->bank = $request->bank;
                        $customer->branch = $request->branch;
                        $customer->bank_number = $request->bank_number;
                    }
                    $customer->commission = $request->commission;
                    $customer->visit_status = $request->visit_status;
                    $customer->contract_status = $request->contract_status;
                    $customer->assigned_to = $request->assigned_to;
                }
            }
        }

        // 設定群組和建立者
        if ($hospital_type) {
            $customer->group_id = 2;
        } elseif ($etiquette_type) {
            $customer->group_id = 5;
        } elseif ($reproduce_type) {
            $customer->group_id = 4;
        } elseif ($dogpark_type) {
            $customer->group_id = 3;
        } elseif ($salons_type) {
            $customer->group_id = 6;
        } elseif ($others_type) {
            $customer->group_id = 7;
        }
        
        $customer->created_up = Auth::user()->id;
        
        // 儲存客戶資料
        $customer->save();
        
        // 處理多筆電話：所有電話都寫入 customer_mobile 表
        if ($request->not_mobile != 1 && !empty($mobiles)) {
            for ($i = 0; $i < count($mobiles); $i++) {
                if (!empty($mobiles[$i])) {
                    $mobile = new CustomerMobile;
                    $mobile->customer_id = $customer->id;
                    $mobile->mobile = $mobiles[$i];
                    $mobile->is_primary = ($i === 0) ? 1 : 0;  // 第一筆為主要電話
                    $mobile->save();
                }
            }
        }

        // 根據類型重定向
        if ($hospital_type) {
            return redirect()->route('hospitals');
        } elseif ($etiquette_type) {
            return redirect()->route('etiquettes');
        } elseif ($reproduce_type) {
            return redirect()->route('reproduces');
        } elseif ($dogpark_type) {
            return redirect()->route('dogparks');
        } elseif ($salons_type) {
            return redirect()->route('salons');
        } elseif ($others_type) {
            return redirect()->route('others');
        }
    }

    // 編輯公司
    public function company_edit($id, Request $request)
    {
        // 記錄原始來源頁面（編輯頁面的前一頁）
        session(['original_referer' => $request->headers->get('referer')]);
        $users = User::where('status', '0')->whereIn('job_id', ['1', '2', '3', '5', '10'])->get();
        $json = file_get_contents(public_path('assets/data/banks.json'));
        $banks = collect(json_decode($json, true));
        $groupedBanks = $banks->groupBy('銀行代號/總機構代碼');
        $company_type = $request->headers->get('referer');
        $data = Customer::with('mobiles')->where('id', $id)->first();
        $groups = CustGroup::get();
        return View('visit.company_edit')
            ->with('hint', 0)
            ->with('data', $data)
            ->with('company_type', $company_type)
            ->with('groups', $groups)
            ->with('groupedBanks', $groupedBanks)
            ->with('users', $users);
    }

    public function company_update($id, Request $request)
    {
        $hospital_type = Str::contains($request->company_type, 'hospitals');  // 醫院
        $etiquette_type = Str::contains($request->company_type, 'etiquettes');  // 禮儀社
        $reproduce_type = Str::contains($request->company_type, 'reproduces');  // 繁殖場
        $dogpark_type = Str::contains($request->company_type, 'dogparks');  // 狗園
        $salons_type = Str::contains($request->company_type, 'salon');  // 美容院
        $others_type = Str::contains($request->company_type, 'others');  // 其他業者

        $data = Customer::where('id', $id)->first();
        $data->name = $request->name;
        
        // 處理多筆電話：第一筆寫入 customer.mobile
        $mobiles = $request->input('mobiles', []);
        if ($request->not_mobile != 1 && !empty($mobiles) && !empty($mobiles[0])) {
            $data->mobile = $mobiles[0];
        } else {
            $data->mobile = '未提供電話';
        }
        
        $data->county = $request->county;
        $data->district = $request->district;
        $data->address = $request->address;
        $data->group_id = $request->group_id;
        
        // 處理帳戶資訊：如果勾選「不提供帳戶」，則不儲存帳戶資訊
        if ($request->not_provide_bank != 1 && (!empty($request->bank) || !empty($request->branch) || !empty($request->bank_number))) {
            $data->bank = $request->bank;
            $data->branch = $request->branch;
            $data->bank_number = $request->bank_number;
        } else {
            $data->bank = null;
            $data->branch = null;
            $data->bank_number = null;
        }
        
        $data->commission = $request->commission;
        $data->visit_status = $request->visit_status;
        $data->contract_status = $request->contract_status;
        $data->assigned_to = $request->assigned_to == 'null' ? null : $request->assigned_to;
        $data->save();

        // 處理多筆電話：刪除舊的電話記錄，重新建立
        CustomerMobile::where('customer_id', $id)->delete();
        if ($request->not_mobile != 1 && !empty($mobiles)) {
            for ($i = 0; $i < count($mobiles); $i++) {
                if (!empty($mobiles[$i])) {
                    $mobile = new CustomerMobile;
                    $mobile->customer_id = $data->id;
                    $mobile->mobile = $mobiles[$i];
                    $mobile->is_primary = ($i === 0) ? 1 : 0;  // 第一筆為主要電話
                    $mobile->save();
                }
            }
        }

        // 返回到原始來源頁面
        $originalReferer = session('original_referer');
        if ($originalReferer) {
            session()->forget('original_referer');  // 清除 session
            return redirect($originalReferer)->with('success', '資料更新成功！');
        } else {
            return back()->with('success', '資料更新成功！');
        }
    }

    public function company_delete($id, Request $request)
    {
        $company_type = $request->headers->get('referer');
        $data = Customer::where('id', $id)->first();
        return View('visit.company_del')->with('data', $data)->with('company_type', $company_type);
    }

    public function source_sale($id, Request $request)  // 叫件紀錄
    {
        $sales = SaleCompanyCommission::where('company_id', $id)
            ->leftJoin('sale_data', 'sale_data.id', '=', 'sale_company_commission.sale_id')
            ->orderby('sale_company_commission.sale_date', 'desc')
            ->whereNotIn('sale_company_commission.type', ['self'])
            ->orderby('sale_data.sale_on', 'desc')
            ->get();
        $customer = Customer::where('id', $id)->first();

        foreach ($sales as $sale) {
            $sale->user_name = User::where('id', $sale->user_id)->value('name');
            $sale->plan_name = Plan::where('id', $sale->plan_id)->value('name');
            $sale->customer_name = Customer::where('id', $sale->customer_id)->value('name');
        }
        // dd($sales);
        return view('visit.source_sales')
            ->with('sales', $sales)
            ->with('customer', $customer);
    }

    public function assigned_index(Request $request)
    {
        $datas = Customer::where('assigned_to', Auth::user()->id);
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%' . $request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
            $county = $request->county;
            if ($county != 'null') {
                if (isset($county)) {
                    $datas = $datas->where('county', $county);
                }
            }
            $district = $request->district;
            if ($district != 'null') {
                if (isset($district)) {
                    $datas = $datas->where('district', $district);
                }
            }
            $commission = $request->commission;
            if ($commission != 'null') {
                if (isset($commission)) {
                    $datas = $datas->where('commission', $commission);
                }
            }

            $has_bank_account = $request->has_bank_account;
            if ($has_bank_account != 'null') {
                if (isset($has_bank_account)) {
                    if ($has_bank_account == '1') {
                        // 有匯款帳號：bank 不為空且 bank_number 不為空
                        $datas = $datas->whereNotNull('bank')->whereNotNull('bank_number')->where('bank_number', '!=', '');
                    } else {
                        // 沒有匯款帳號：bank 為空或 bank_number 為空
                        $datas = $datas->where(function ($query) {
                            $query
                                ->whereNull('bank')
                                ->orWhereNull('bank_number')
                                ->orWhere('bank_number', '=', '');
                        });
                    }
                }
            }

            $contract_status = $request->contract_status;
            if ($contract_status != 'null') {
                if (isset($contract_status)) {
                    $datas = $datas->where('contract_status', $contract_status);
                }
            }
        }

        $recently_date_sort = $request->recently_date_sort;
        if ($recently_date_sort != 'null' && isset($recently_date_sort)) {
            $datas = $datas->orderByRaw('(
                SELECT MAX(sale_date) 
                FROM sale_company_commission 
                WHERE company_id = customer.id
            ) ' . $recently_date_sort);
        }

        $datas = $datas->paginate(50);

        $bankData = $this->getFlatBankData();  // flat 結構的銀行分行 JSON
        foreach ($datas as $data) {
            $data->visit_count = Visit::where('customer_id', $data->id)->count();

            // 新增銀行/分行中文欄位
            $data->bank_name = $this->getBankNameFromFlatJson($data->bank, $bankData);
            $data->branch_name = $this->getBranchNameFromFlatJson($data->bank, $data->branch, $bankData);

            // 叫件次數
            $data->sale_count = SaleCompanyCommission::where('company_id', $data->id)->whereNotIn('type', ['self'])->count();
            $data->recently_date = SaleCompanyCommission::where('company_id', $data->id)->whereNotIn('type', ['self'])->orderby('sale_date', 'desc')->value('sale_date');
        }

        // 載入地區資料
        $data_countys = Customer::where('group_id', 5)->whereNotNull('county')->where('county', '!=', '')->get();
        foreach ($data_countys as $data_county) {
            $countys[] = $data_county->county;
        }
        $countys = array_unique($countys);

        $county = $request->county;
        if (isset($county)) {
            $data_districts = Customer::where('group_id', 5)->where('county', $county)->whereNotNull('district')->where('district', '!=', '')->get();
        } else {
            $data_districts = [];
        }
        $districts = [];
        foreach ($data_districts as $data_district) {
            $districts[] = $data_district->district;
        }
        $districts = array_unique($districts);

        return view('visit.assigned')->with('datas', $datas)->with('request', $request)->with('countys', $countys)->with('districts', $districts);
    }

    // 讀取 flat 結構的 JSON
    protected function getFlatBankData()
    {
        $filePath = public_path('assets/data/banks.json');

        if (!file_exists($filePath)) {
            Log::error("找不到銀行資料檔案：$filePath");
            return [];
        }

        return json_decode(file_get_contents($filePath), true) ?? [];
    }

    // 找出銀行名稱
    protected function getBankNameFromFlatJson($bankCode, $bankData)
    {
        foreach ($bankData as $row) {
            if (isset($row['銀行代號/總機構代碼']) && $row['銀行代號/總機構代碼'] == $bankCode) {
                return $row['金融機構名稱'] ?? '未知銀行';
            }
        }
        return '未知銀行';
    }

    // 找出分行名稱
    protected function getBranchNameFromFlatJson($bankCode, $branchCode, $bankData)
    {
        foreach ($bankData as $row) {
            if (
                isset($row['銀行代號/總機構代碼'], $row['分支機構代號']) &&
                $row['銀行代號/總機構代碼'] == $bankCode &&
                $row['分支機構代號'] == $branchCode
            ) {
                return $row['分支機構名稱'] ?? '未知分行';
            }
        }
        return '未知分行';
    }

    // 匯出醫院資料為 Excel
    public function hospitalsExport(Request $request)
    {
        $datas = Customer::where('group_id', 2);

        // 應用相同的篩選邏輯
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%' . $request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
            $county = $request->county;
            if ($county != 'null') {
                if (isset($county)) {
                    $datas = $datas->where('county', $county);
                }
            }
            $district = $request->district;
            if ($district != 'null') {
                if (isset($district)) {
                    $datas = $datas->where('district', $district);
                }
            }
            $commission = $request->commission;
            if ($commission != 'null') {
                if (isset($commission)) {
                    $datas = $datas->where('commission', $commission);
                }
            }

            $has_bank_account = $request->has_bank_account;
            if ($has_bank_account != 'null') {
                if (isset($has_bank_account)) {
                    if ($has_bank_account == '1') {
                        // 有匯款帳號：bank 不為空且 bank_number 不為空
                        $datas = $datas->whereNotNull('bank')->whereNotNull('bank_number')->where('bank_number', '!=', '');
                    } else {
                        // 沒有匯款帳號：bank 為空或 bank_number 為空
                        $datas = $datas->where(function ($query) {
                            $query
                                ->whereNull('bank')
                                ->orWhereNull('bank_number')
                                ->orWhere('bank_number', '=', '');
                        });
                    }
                }
            }

            $contract_status = $request->contract_status;
            if ($contract_status != 'null') {
                if (isset($contract_status)) {
                    $datas = $datas->where('contract_status', $contract_status);
                }
            }
            $assigned_to = $request->assigned_to;
            if ($assigned_to != 'null') {
                if (isset($assigned_to)) {
                    $datas = $datas->where('assigned_to', $assigned_to);
                }
            }
            $seq = $request->seq;
            $recently_date_sort = $request->recently_date_sort;

            // 如果兩個排序都有選擇，優先使用叫件日期排序
            if ($recently_date_sort != 'null' && isset($recently_date_sort)) {
                // 使用子查詢來排序叫件日期
                $datas = $datas->orderByRaw('(
                    SELECT MAX(sale_date) 
                    FROM sale_company_commission 
                    WHERE company_id = customer.id
                ) ' . $recently_date_sort);
            } elseif ($seq != 'null' && isset($seq)) {
                $datas = $datas->orderby('created_at', $seq);
            }
        }

        // 如果沒有選擇任何排序，使用預設排序
        if (!isset($request->seq) && !isset($request->recently_date_sort)) {
            $datas = $datas->orderby('name', 'desc');
        }

        $datas = $datas->with('assigned_to_name')->get();

        $bankData = $this->getFlatBankData();
        foreach ($datas as $data) {
            $data->visit_count = Visit::where('customer_id', $data->id)->count();

            // 新增銀行/分行中文欄位
            $data->bank_name = $this->getBankNameFromFlatJson($data->bank, $bankData);
            $data->branch_name = $this->getBranchNameFromFlatJson($data->bank, $data->branch, $bankData);

            // 叫件次數
            $data->sale_count = SaleCompanyCommission::whereNotIn('type', ['self'])->where('company_id', $data->id)->count();
            $data->recently_date = SaleCompanyCommission::whereNotIn('type', ['self'])->where('company_id', $data->id)->orderby('sale_date', 'desc')->value('sale_date');

            // 新增銀行/分行中文欄位（匯出用）
            if ($data->bank && $data->bank_number) {
                $data->export_bank_name = $data->bank_name;
                $data->export_branch_name = $data->branch_name;
            } else {
                $data->export_bank_name = '';
                $data->export_branch_name = '';
            }
        }

        // 生成 CSV 內容（不使用套件）
        $filename = '醫院列表_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($datas) {
            $file = fopen('php://output', 'w');

            // 寫入 BOM 以支援中文
            fwrite($file, "\u{FEFF}");

            // 寫入標題行
            fputcsv($file, [
                '編號',
                '姓名',
                '電話',
                '縣市',
                '地區',
                '地址',
                '銀行',
                '分行',
                '帳號',
                '佣金',
                '拜訪狀態',
                '拜訪次數',
                '叫件次數',
                '最近叫件日期',
                '新增時間',
                '負責人員',
                '新增人員'
            ]);

            // 寫入資料行
            foreach ($datas as $key => $data) {
                // 處理帳號，確保顯示為文字格式
                $accountNumber = $data->bank_number ?: '';
                if ($accountNumber && is_numeric($accountNumber)) {
                    // 如果是數字，在前面加上單引號強制為文字格式
                    $accountNumber = "'" . $accountNumber;
                }

                fputcsv($file, [
                    $key + 1,
                    $data->name,
                    $data->mobile,
                    $data->county,
                    $data->district,
                    $data->address,
                    $data->export_bank_name,
                    $data->export_branch_name,
                    $accountNumber,
                    $data->commission == 1 ? '有' : '無',
                    $data->visit_status == 1 ? '有' : '無',
                    $data->visit_count,
                    $data->sale_count,
                    $data->recently_date ?: '-',
                    date('Y-m-d', strtotime($data->created_at)),
                    $data->assigned_to_name->name ?? '-',
                    $data->createdBy->name ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // 生成 XLSX 檔案內容（不使用套件）
    private function generateXLSX($datas)
    {
        // 建立 ZIP 檔案（XLSX 本質上是 ZIP 檔案）
        $zip = new \ZipArchive();
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_');

        if ($zip->open($tempFile, \ZipArchive::CREATE) !== TRUE) {
            return '';
        }

        // 標題行
        $headers = [
            '編號',
            '姓名',
            '電話',
            '縣市',
            '地區',
            '地址',
            '銀行',
            '分行',
            '帳號',
            '佣金',
            '拜訪狀態',
            '拜訪次數',
            '叫件次數',
            '最近叫件日期',
            '新增時間'
        ];

        // 建立工作表 XML
        $worksheetContent = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $worksheetContent .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . "\n";
        $worksheetContent .= '  <sheetData>' . "\n";

        // 寫入標題行
        $worksheetContent .= '    <row r="1">' . "\n";
        foreach ($headers as $index => $header) {
            $cellRef = $this->getCellReference($index, 1);
            $worksheetContent .= '      <c r="' . $cellRef . '" t="s">' . "\n";
            $worksheetContent .= '        <v>' . $index . '</v>' . "\n";
            $worksheetContent .= '      </c>' . "\n";
        }
        $worksheetContent .= '    </row>' . "\n";

        // 寫入資料行
        foreach ($datas as $key => $data) {
            $rowNumber = $key + 2;
            $worksheetContent .= '    <row r="' . $rowNumber . '">' . "\n";

            $rowData = [
                $key + 1,
                $data->name,
                $data->mobile,
                $data->county,
                $data->district,
                $data->address,
                $data->export_bank_name,
                $data->export_branch_name,
                $data->bank_number ?: '',
                $data->commission == 1 ? '有' : '無',
                $data->visit_status == 1 ? '有' : '無',
                $data->visit_count,
                $data->sale_count,
                $data->recently_date ?: '-',
                date('Y-m-d', strtotime($data->created_at))
            ];

            foreach ($rowData as $index => $cellValue) {
                $cellRef = $this->getCellReference($index, $rowNumber);
                $worksheetContent .= '      <c r="' . $cellRef . '" t="s">' . "\n";
                $worksheetContent .= '        <v>' . htmlspecialchars($cellValue) . '</v>' . "\n";
                $worksheetContent .= '      </c>' . "\n";
            }

            $worksheetContent .= '    </row>' . "\n";
        }

        $worksheetContent .= '  </sheetData>' . "\n";
        $worksheetContent .= '</worksheet>';

        // 建立工作簿 XML
        $workbookContent = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $workbookContent .= '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' . "\n";
        $workbookContent .= '  <sheets>' . "\n";
        $workbookContent .= '    <sheet name="醫院列表" sheetId="1" r:id="rId1"/>' . "\n";
        $workbookContent .= '  </sheets>' . "\n";
        $workbookContent .= '</workbook>';

        // 建立共享字串表
        $sharedStrings = [];
        $sharedStrings[] = '編號';
        $sharedStrings[] = '姓名';
        $sharedStrings[] = '電話';
        $sharedStrings[] = '縣市';
        $sharedStrings[] = '地區';
        $sharedStrings[] = '地址';
        $sharedStrings[] = '銀行';
        $sharedStrings[] = '分行';
        $sharedStrings[] = '帳號';
        $sharedStrings[] = '佣金';
        $sharedStrings[] = '拜訪狀態';
        $sharedStrings[] = '拜訪次數';
        $sharedStrings[] = '叫件次數';
        $sharedStrings[] = '最近叫件日期';
        $sharedStrings[] = '新增時間';

        // 添加資料到共享字串表
        foreach ($datas as $data) {
            $sharedStrings[] = $data->name;
            $sharedStrings[] = $data->mobile;
            $sharedStrings[] = $data->county;
            $sharedStrings[] = $data->district;
            $sharedStrings[] = $data->address;
            $sharedStrings[] = $data->export_bank_name;
            $sharedStrings[] = $data->export_branch_name;
            $sharedStrings[] = $data->bank_number ?: '';
            $sharedStrings[] = $data->commission == 1 ? '有' : '無';
            $sharedStrings[] = $data->visit_status == 1 ? '有' : '無';
            $sharedStrings[] = (string) $data->visit_count;
            $sharedStrings[] = (string) $data->sale_count;
            $sharedStrings[] = $data->recently_date ?: '-';
            $sharedStrings[] = date('Y-m-d', strtotime($data->created_at));
        }

        // 建立共享字串表 XML
        $sharedStringsContent = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $sharedStringsContent .= '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($sharedStrings) . '" uniqueCount="' . count(array_unique($sharedStrings)) . '">' . "\n";
        foreach (array_unique($sharedStrings) as $string) {
            $sharedStringsContent .= '  <si><t>' . htmlspecialchars($string) . '</t></si>' . "\n";
        }
        $sharedStringsContent .= '</sst>';

        // 建立關係檔案
        $relsContent = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $relsContent .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' . "\n";
        $relsContent .= '  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>' . "\n";
        $relsContent .= '  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>' . "\n";
        $relsContent .= '</Relationships>';

        // 建立內容類型檔案
        $contentTypesContent = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $contentTypesContent .= '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' . "\n";
        $contentTypesContent .= '  <Default Extension="xml" ContentType="application/xml"/>' . "\n";
        $contentTypesContent .= '  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats.spreadsheetml.sheet.main+xml"/>' . "\n";
        $contentTypesContent .= '  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats.spreadsheetml.worksheet+xml"/>' . "\n";
        $contentTypesContent .= '  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats.spreadsheetml.sharedStrings+xml"/>' . "\n";
        $contentTypesContent .= '</Types>';

        try {
            // 添加檔案到 ZIP
            $zip->addFromString('xl/worksheets/sheet1.xml', $worksheetContent);
            $zip->addFromString('xl/workbook.xml', $workbookContent);
            $zip->addFromString('xl/sharedStrings.xml', $sharedStringsContent);
            $zip->addFromString('xl/_rels/workbook.xml.rels', $relsContent);
            $zip->addFromString('[Content_Types].xml', $contentTypesContent);

            $zip->close();

            // 讀取 ZIP 檔案內容
            $xlsxContent = file_get_contents($tempFile);

            // 清理臨時檔案
            unlink($tempFile);

            return $xlsxContent;
        } catch (Exception $e) {
            // 如果出錯，清理臨時檔案並返回空
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
            return '';
        }
    }

    // 獲取儲存格引用（A1, B1, C1...）
    private function getCellReference($columnIndex, $rowNumber)
    {
        $column = '';
        while ($columnIndex >= 0) {
            $column = chr(65 + ($columnIndex % 26)) . $column;
            $columnIndex = intval($columnIndex / 26) - 1;
        }
        return $column . $rowNumber;
    }

    // 匯出禮儀社資料為 CSV
    public function etiquettesExport(Request $request)
    {
        $datas = Customer::where('group_id', 5);

        // 應用相同的篩選邏輯
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%' . $request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
            $county = $request->county;
            if ($county != 'null') {
                if (isset($county)) {
                    $datas = $datas->where('county', $county);
                }
            }
            $district = $request->district;
            if ($district != 'null') {
                if (isset($district)) {
                    $datas = $datas->where('district', $district);
                }
            }
            $commission = $request->commission;
            if ($commission != 'null') {
                if (isset($commission)) {
                    $datas = $datas->where('commission', $commission);
                }
            }

            $has_bank_account = $request->has_bank_account;
            if ($has_bank_account != 'null') {
                if (isset($has_bank_account)) {
                    if ($has_bank_account == '1') {
                        // 有匯款帳號：bank 不為空且 bank_number 不為空
                        $datas = $datas->whereNotNull('bank')->whereNotNull('bank_number')->where('bank_number', '!=', '');
                    } else {
                        // 沒有匯款帳號：bank 為空或 bank_number 為空
                        $datas = $datas->where(function ($query) {
                            $query
                                ->whereNull('bank')
                                ->orWhereNull('bank_number')
                                ->orWhere('bank_number', '=', '');
                        });
                    }
                }
            }
        }

        $recently_date_sort = $request->recently_date_sort;
        if ($recently_date_sort != 'null' && isset($recently_date_sort)) {
            $datas = $datas->orderByRaw('(
                SELECT MAX(sale_date) 
                FROM sale_company_commission 
                WHERE company_id = customer.id
            ) ' . $recently_date_sort);
        }

        $datas = $datas->get();

        $bankData = $this->getFlatBankData();
        foreach ($datas as $data) {
            $data->visit_count = Visit::where('customer_id', $data->id)->count();
            $data->sale_count = SaleCompanyCommission::where('company_id', $data->id)->count();
            $data->recently_date = SaleCompanyCommission::where('company_id', $data->id)->orderby('sale_date', 'desc')->value('sale_date');

            // 新增銀行/分行中文欄位（匯出用）
            if ($data->bank && $data->bank_number) {
                $data->export_bank_name = $this->getBankNameFromFlatJson($data->bank, $bankData);
                $data->export_branch_name = $this->getBranchNameFromFlatJson($data->bank, $data->branch, $bankData);
            } else {
                $data->export_bank_name = '';
                $data->export_branch_name = '';
            }
        }

        // 生成 CSV 內容
        $filename = '禮儀社列表_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($datas) {
            $file = fopen('php://output', 'w');

            // 寫入 BOM 以支援中文
            fwrite($file, "\u{FEFF}");

            // 寫入標題行
            fputcsv($file, [
                '編號',
                '姓名',
                '電話',
                '縣市',
                '地區',
                '地址',
                '銀行',
                '分行',
                '帳號',
                '佣金',
                '拜訪狀態',
                '拜訪次數',
                '叫件次數',
                '最近叫件日期',
                '新增時間'
            ]);

            // 寫入資料行
            foreach ($datas as $key => $data) {
                // 處理帳號，確保顯示為文字格式
                $accountNumber = $data->bank_number ?: '';
                if ($accountNumber && is_numeric($accountNumber)) {
                    // 如果是數字，在前面加上單引號強制為文字格式
                    $accountNumber = "'" . $accountNumber;
                }

                fputcsv($file, [
                    $key + 1,
                    $data->name,
                    $data->mobile,
                    $data->county,
                    $data->district,
                    $data->address,
                    $data->export_bank_name,
                    $data->export_branch_name,
                    $accountNumber,
                    $data->commission == 1 ? '有' : '無',
                    $data->visit_status == 1 ? '有' : '無',
                    $data->visit_count,
                    $data->sale_count,
                    $data->recently_date ?: '-',
                    date('Y-m-d', strtotime($data->created_at))
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // 匯出繁殖場資料為 CSV
    public function reproducesExport(Request $request)
    {
        $datas = Customer::where('group_id', 4);

        // 應用相同的篩選邏輯
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%' . $request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
            $county = $request->county;
            if ($county != 'null') {
                if (isset($county)) {
                    $datas = $datas->where('county', $county);
                }
            }
            $district = $request->district;
            if ($district != 'null') {
                if (isset($district)) {
                    $datas = $datas->where('district', $district);
                }
            }
            $commission = $request->commission;
            if ($commission != 'null') {
                if (isset($commission)) {
                    $datas = $datas->where('commission', $commission);
                }
            }

            $has_bank_account = $request->has_bank_account;
            if ($has_bank_account != 'null') {
                if (isset($has_bank_account)) {
                    if ($has_bank_account == '1') {
                        // 有匯款帳號：bank 不為空且 bank_number 不為空
                        $datas = $datas->whereNotNull('bank')->whereNotNull('bank_number')->where('bank_number', '!=', '');
                    } else {
                        // 沒有匯款帳號：bank 為空或 bank_number 為空
                        $datas = $datas->where(function ($query) {
                            $query
                                ->whereNull('bank')
                                ->orWhereNull('bank_number')
                                ->orWhere('bank_number', '=', '');
                        });
                    }
                }
            }
        }

        $recently_date_sort = $request->recently_date_sort;
        if ($recently_date_sort != 'null' && isset($recently_date_sort)) {
            $datas = $datas->orderByRaw('(
                SELECT MAX(sale_date) 
                FROM sale_company_commission 
                WHERE company_id = customer.id
            ) ' . $recently_date_sort);
        }

        $datas = $datas->get();

        $bankData = $this->getFlatBankData();
        foreach ($datas as $data) {
            $data->visit_count = Visit::where('customer_id', $data->id)->count();
            $data->sale_count = SaleCompanyCommission::where('company_id', $data->id)->count();
            $data->recently_date = SaleCompanyCommission::where('company_id', $data->id)->orderby('sale_date', 'desc')->value('sale_date');

            // 新增銀行/分行中文欄位（匯出用）
            if ($data->bank && $data->bank_number) {
                $data->export_bank_name = $this->getBankNameFromFlatJson($data->bank, $bankData);
                $data->export_branch_name = $this->getBranchNameFromFlatJson($data->bank, $data->branch, $bankData);
            } else {
                $data->export_bank_name = '';
                $data->export_branch_name = '';
            }
        }

        // 生成 CSV 內容
        $filename = '繁殖場列表_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($datas) {
            $file = fopen('php://output', 'w');

            // 寫入 BOM 以支援中文
            fwrite($file, "\u{FEFF}");

            // 寫入標題行
            fputcsv($file, [
                '編號',
                '姓名',
                '電話',
                '縣市',
                '地區',
                '地址',
                '銀行',
                '分行',
                '帳號',
                '佣金',
                '拜訪狀態',
                '拜訪次數',
                '叫件次數',
                '最近叫件日期',
                '新增時間'
            ]);

            // 寫入資料行
            foreach ($datas as $key => $data) {
                // 處理帳號，確保顯示為文字格式
                $accountNumber = $data->bank_number ?: '';
                if ($accountNumber && is_numeric($accountNumber)) {
                    // 如果是數字，在前面加上單引號強制為文字格式
                    $accountNumber = "'" . $accountNumber;
                }

                fputcsv($file, [
                    $key + 1,
                    $data->name,
                    $data->mobile,
                    $data->county,
                    $data->district,
                    $data->address,
                    $data->export_bank_name,
                    $data->export_branch_name,
                    $accountNumber,
                    $data->commission == 1 ? '有' : '無',
                    $data->visit_status == 1 ? '有' : '無',
                    $data->visit_count,
                    $data->sale_count,
                    $data->recently_date ?: '-',
                    date('Y-m-d', strtotime($data->created_at))
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // 匯出狗園資料為 CSV
    public function dogparksExport(Request $request)
    {
        $datas = Customer::where('group_id', 3);

        // 應用相同的篩選邏輯
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%' . $request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
            $county = $request->county;
            if ($county != 'null') {
                if (isset($county)) {
                    $datas = $datas->where('county', $county);
                }
            }
            $district = $request->district;
            if ($district != 'null') {
                if (isset($district)) {
                    $datas = $datas->where('district', $district);
                }
            }
            $commission = $request->commission;
            if ($commission != 'null') {
                if (isset($commission)) {
                    $datas = $datas->where('commission', $commission);
                }
            }

            $has_bank_account = $request->has_bank_account;
            if ($has_bank_account != 'null') {
                if (isset($has_bank_account)) {
                    if ($has_bank_account == '1') {
                        // 有匯款帳號：bank 不為空且 bank_number 不為空
                        $datas = $datas->whereNotNull('bank')->whereNotNull('bank_number')->where('bank_number', '!=', '');
                    } else {
                        // 沒有匯款帳號：bank 為空或 bank_number 為空
                        $datas = $datas->where(function ($query) {
                            $query
                                ->whereNull('bank')
                                ->orWhereNull('bank_number')
                                ->orWhere('bank_number', '=', '');
                        });
                    }
                }
            }
        }

        $recently_date_sort = $request->recently_date_sort;
        if ($recently_date_sort != 'null' && isset($recently_date_sort)) {
            $datas = $datas->orderByRaw('(
                SELECT MAX(sale_date) 
                FROM sale_company_commission 
                WHERE company_id = customer.id
            ) ' . $recently_date_sort);
        }

        $datas = $datas->get();

        $bankData = $this->getFlatBankData();
        foreach ($datas as $data) {
            $data->visit_count = Visit::where('customer_id', $data->id)->count();
            $data->sale_count = SaleCompanyCommission::where('company_id', $data->id)->count();
            $data->recently_date = SaleCompanyCommission::where('company_id', $data->id)->orderby('sale_date', 'desc')->value('sale_date');

            // 新增銀行/分行中文欄位（匯出用）
            if ($data->bank && $data->bank_number) {
                $data->export_bank_name = $this->getBankNameFromFlatJson($data->bank, $bankData);
                $data->export_branch_name = $this->getBranchNameFromFlatJson($data->bank, $data->branch, $bankData);
            } else {
                $data->export_bank_name = '';
                $data->export_branch_name = '';
            }
        }

        // 生成 CSV 內容
        $filename = '狗園列表_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($datas) {
            $file = fopen('php://output', 'w');

            // 寫入 BOM 以支援中文
            fwrite($file, "\u{FEFF}");

            // 寫入標題行
            fputcsv($file, [
                '編號',
                '姓名',
                '電話',
                '縣市',
                '地區',
                '地址',
                '銀行',
                '分行',
                '帳號',
                '佣金',
                '拜訪狀態',
                '拜訪次數',
                '叫件次數',
                '最近叫件日期',
                '新增時間'
            ]);

            // 寫入資料行
            foreach ($datas as $key => $data) {
                // 處理帳號，確保顯示為文字格式
                $accountNumber = $data->bank_number ?: '';
                if ($accountNumber && is_numeric($accountNumber)) {
                    // 如果是數字，在前面加上單引號強制為文字格式
                    $accountNumber = "'" . $accountNumber;
                }

                fputcsv($file, [
                    $key + 1,
                    $data->name,
                    $data->mobile,
                    $data->county,
                    $data->district,
                    $data->address,
                    $data->export_bank_name,
                    $data->export_branch_name,
                    $accountNumber,
                    $data->commission == 1 ? '有' : '無',
                    $data->visit_status == 1 ? '有' : '無',
                    $data->visit_count,
                    $data->sale_count,
                    $data->recently_date ?: '-',
                    date('Y-m-d', strtotime($data->created_at))
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function salonsExport(Request $request)
    {
        $datas = Customer::where('group_id', 6);

        // 應用相同的篩選邏輯
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%' . $request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
            $county = $request->county;
            if ($county != 'null') {
                if (isset($county)) {
                    $datas = $datas->where('county', $county);
                }
            }
            $district = $request->district;
            if ($district != 'null') {
                if (isset($district)) {
                    $datas = $datas->where('district', $district);
                }
            }
            $commission = $request->commission;
            if ($commission != 'null') {
                if (isset($commission)) {
                    $datas = $datas->where('commission', $commission);
                }
            }

            $has_bank_account = $request->has_bank_account;
            if ($has_bank_account != 'null') {
                if (isset($has_bank_account)) {
                    if ($has_bank_account == '1') {
                        // 有匯款帳號：bank 不為空且 bank_number 不為空
                        $datas = $datas->whereNotNull('bank')->whereNotNull('bank_number')->where('bank_number', '!=', '');
                    } else {
                        // 沒有匯款帳號：bank 為空或 bank_number 為空
                        $datas = $datas->where(function ($query) {
                            $query
                                ->whereNull('bank')
                                ->orWhereNull('bank_number')
                                ->orWhere('bank_number', '=', '');
                        });
                    }
                }
            }

            $contract_status = $request->contract_status;
            if ($contract_status != 'null') {
                if (isset($contract_status)) {
                    $datas = $datas->where('contract_status', $contract_status);
                }
            }
            $assigned_to = $request->assigned_to;
            if ($assigned_to != 'null') {
                if (isset($assigned_to)) {
                    $datas = $datas->where('assigned_to', $assigned_to);
                }
            }
            $seq = $request->seq;
            $recently_date_sort = $request->recently_date_sort;

            // 如果兩個排序都有選擇，優先使用叫件日期排序
            if ($recently_date_sort != 'null' && isset($recently_date_sort)) {
                // 使用子查詢來排序叫件日期
                $datas = $datas->orderByRaw('(
                    SELECT MAX(sale_date) 
                    FROM sale_company_commission 
                    WHERE company_id = customer.id
                ) ' . $recently_date_sort);
            } elseif ($seq != 'null' && isset($seq)) {
                $datas = $datas->orderby('created_at', $seq);
            }
        }

        // 如果沒有選擇任何排序，使用預設排序
        if (!isset($request->seq) && !isset($request->recently_date_sort)) {
            $datas = $datas->orderby('name', 'desc');
        }

        $datas = $datas->with('assigned_to_name')->get();

        $bankData = $this->getFlatBankData();
        foreach ($datas as $data) {
            $data->visit_count = Visit::where('customer_id', $data->id)->count();

            // 新增銀行/分行中文欄位
            $data->bank_name = $this->getBankNameFromFlatJson($data->bank, $bankData);
            $data->branch_name = $this->getBranchNameFromFlatJson($data->bank, $data->branch, $bankData);

            // 叫件次數
            $data->sale_count = SaleCompanyCommission::whereNotIn('type', ['self'])->where('company_id', $data->id)->count();
            $data->recently_date = SaleCompanyCommission::whereNotIn('type', ['self'])->where('company_id', $data->id)->orderby('sale_date', 'desc')->value('sale_date');

            // 新增銀行/分行中文欄位（匯出用）
            if ($data->bank && $data->bank_number) {
                $data->export_bank_name = $data->bank_name;
                $data->export_branch_name = $data->branch_name;
            } else {
                $data->export_bank_name = '';
                $data->export_branch_name = '';
            }
        }

        // 生成 CSV 內容（不使用套件）
        $filename = '醫院列表_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($datas) {
            $file = fopen('php://output', 'w');

            // 寫入 BOM 以支援中文
            fwrite($file, "\u{FEFF}");

            // 寫入標題行
            fputcsv($file, [
                '編號',
                '姓名',
                '電話',
                '縣市',
                '地區',
                '地址',
                '銀行',
                '分行',
                '帳號',
                '佣金',
                '拜訪狀態',
                '拜訪次數',
                '叫件次數',
                '最近叫件日期',
                '新增時間',
                '負責人員',
                '新增人員'
            ]);

            // 寫入資料行
            foreach ($datas as $key => $data) {
                // 處理帳號，確保顯示為文字格式
                $accountNumber = $data->bank_number ?: '';
                if ($accountNumber && is_numeric($accountNumber)) {
                    // 如果是數字，在前面加上單引號強制為文字格式
                    $accountNumber = "'" . $accountNumber;
                }

                fputcsv($file, [
                    $key + 1,
                    $data->name,
                    $data->mobile,
                    $data->county,
                    $data->district,
                    $data->address,
                    $data->export_bank_name,
                    $data->export_branch_name,
                    $accountNumber,
                    $data->commission == 1 ? '有' : '無',
                    $data->visit_status == 1 ? '有' : '無',
                    $data->visit_count,
                    $data->sale_count,
                    $data->recently_date ?: '-',
                    date('Y-m-d', strtotime($data->created_at)),
                    $data->assigned_to_name->name ?? '-',
                    $data->createdBy->name ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function othersExport(Request $request)
    {
        $datas = Customer::where('group_id', 7);

        // 應用相同的篩選邏輯
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%' . $request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
            $county = $request->county;
            if ($county != 'null') {
                if (isset($county)) {
                    $datas = $datas->where('county', $county);
                }
            }
            $district = $request->district;
            if ($district != 'null') {
                if (isset($district)) {
                    $datas = $datas->where('district', $district);
                }
            }
            $commission = $request->commission;
            if ($commission != 'null') {
                if (isset($commission)) {
                    $datas = $datas->where('commission', $commission);
                }
            }

            $has_bank_account = $request->has_bank_account;
            if ($has_bank_account != 'null') {
                if (isset($has_bank_account)) {
                    if ($has_bank_account == '1') {
                        // 有匯款帳號：bank 不為空且 bank_number 不為空
                        $datas = $datas->whereNotNull('bank')->whereNotNull('bank_number')->where('bank_number', '!=', '');
                    } else {
                        // 沒有匯款帳號：bank 為空或 bank_number 為空
                        $datas = $datas->where(function ($query) {
                            $query
                                ->whereNull('bank')
                                ->orWhereNull('bank_number')
                                ->orWhere('bank_number', '=', '');
                        });
                    }
                }
            }

            $contract_status = $request->contract_status;
            if ($contract_status != 'null') {
                if (isset($contract_status)) {
                    $datas = $datas->where('contract_status', $contract_status);
                }
            }
            $assigned_to = $request->assigned_to;
            if ($assigned_to != 'null') {
                if (isset($assigned_to)) {
                    $datas = $datas->where('assigned_to', $assigned_to);
                }
            }
            $seq = $request->seq;
            $recently_date_sort = $request->recently_date_sort;

            // 如果兩個排序都有選擇，優先使用叫件日期排序
            if ($recently_date_sort != 'null' && isset($recently_date_sort)) {
                // 使用子查詢來排序叫件日期
                $datas = $datas->orderByRaw('(
                    SELECT MAX(sale_date) 
                    FROM sale_company_commission 
                    WHERE company_id = customer.id
                ) ' . $recently_date_sort);
            } elseif ($seq != 'null' && isset($seq)) {
                $datas = $datas->orderby('created_at', $seq);
            }
        }

        // 如果沒有選擇任何排序，使用預設排序
        if (!isset($request->seq) && !isset($request->recently_date_sort)) {
            $datas = $datas->orderby('name', 'desc');
        }

        $datas = $datas->with('assigned_to_name')->get();

        $bankData = $this->getFlatBankData();
        foreach ($datas as $data) {
            $data->visit_count = Visit::where('customer_id', $data->id)->count();

            // 新增銀行/分行中文欄位
            $data->bank_name = $this->getBankNameFromFlatJson($data->bank, $bankData);
            $data->branch_name = $this->getBranchNameFromFlatJson($data->bank, $data->branch, $bankData);

            // 叫件次數
            $data->sale_count = SaleCompanyCommission::whereNotIn('type', ['self'])->where('company_id', $data->id)->count();
            $data->recently_date = SaleCompanyCommission::whereNotIn('type', ['self'])->where('company_id', $data->id)->orderby('sale_date', 'desc')->value('sale_date');

            // 新增銀行/分行中文欄位（匯出用）
            if ($data->bank && $data->bank_number) {
                $data->export_bank_name = $data->bank_name;
                $data->export_branch_name = $data->branch_name;
            } else {
                $data->export_bank_name = '';
                $data->export_branch_name = '';
            }
        }

        // 生成 CSV 內容（不使用套件）
        $filename = '醫院列表_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($datas) {
            $file = fopen('php://output', 'w');

            // 寫入 BOM 以支援中文
            fwrite($file, "\u{FEFF}");

            // 寫入標題行
            fputcsv($file, [
                '編號',
                '姓名',
                '電話',
                '縣市',
                '地區',
                '地址',
                '銀行',
                '分行',
                '帳號',
                '佣金',
                '拜訪狀態',
                '拜訪次數',
                '叫件次數',
                '最近叫件日期',
                '新增時間',
                '負責人員',
                '新增人員'
            ]);

            // 寫入資料行
            foreach ($datas as $key => $data) {
                // 處理帳號，確保顯示為文字格式
                $accountNumber = $data->bank_number ?: '';
                if ($accountNumber && is_numeric($accountNumber)) {
                    // 如果是數字，在前面加上單引號強制為文字格式
                    $accountNumber = "'" . $accountNumber;
                }

                fputcsv($file, [
                    $key + 1,
                    $data->name,
                    $data->mobile,
                    $data->county,
                    $data->district,
                    $data->address,
                    $data->export_bank_name,
                    $data->export_branch_name,
                    $accountNumber,
                    $data->commission == 1 ? '有' : '無',
                    $data->visit_status == 1 ? '有' : '無',
                    $data->visit_count,
                    $data->sale_count,
                    $data->recently_date ?: '-',
                    date('Y-m-d', strtotime($data->created_at)),
                    $data->assigned_to_name->name ?? '-',
                    $data->createdBy->name ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

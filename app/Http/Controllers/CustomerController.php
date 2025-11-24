<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\CustGroup;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerHistory;
use App\Models\CustomerMobile;
use App\Models\Gdpaper;
use App\Models\Lamp;
use App\Models\Plan;
use App\Models\Product;
use App\Models\PromA;
use App\Models\PromB;
use App\Models\PujaData;
use App\Models\Sale;
use App\Models\Sale_gdpaper;
use App\Models\Sale_promB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /* ajax */
    public function customer(Request $request)
    {
        if ($request->ajax()) {
            $output = '';
            $custs = Customer::where('name', 'like', $request->cust_name . '%')->get();

            if ($custs) {
                foreach ($custs as $key => $cust) {
                    $output .= '<option value="' . $cust->id . '" label="(' . $cust->name . ')-' . $cust->mobile . '">';
                }
            }
            return Response($output);
        }
    }

    public function customer_data(Request $request)
    {
        if ($request->ajax()) {
            $output = '';
            $cust = Customer::where('id', $request->cust_id)->first();

            if ($cust) {
                return Response($cust);
            }
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customers = Customer::paginate(30);
        $customer_groups = CustGroup::get();

        $county = $request->county;
        $district = $request->district;
        // 縣市下拉市
        $data_countys = Customer::whereNotNull('county')->get();
        foreach ($data_countys as $data_county) {
            $countys[] = $data_county->county;
        }
        $countys = array_unique($countys);

        if (isset($county)) {
            $data_districts = Customer::where('county', $county)->get();
        } else {
            $data_districts = [];
        }
        $districts = [];
        foreach ($data_districts as $data_district) {
            $districts[] = $data_district->district;
        }
        $districts = array_unique($districts);
        // 結束
        if ($request) {
            $query = Customer::query();  // Start building the query

            if (!empty($request->name)) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }
            if (!empty($request->mobile)) {
                // 收集所有符合電話條件的客戶 ID
                $customer_ids = [];
                
                // 從 customer 表查找
                $customers_by_mobile = Customer::where('mobile', 'like', $request->mobile . '%')
                    ->pluck('id')
                    ->toArray();
                $customer_ids = array_merge($customer_ids, $customers_by_mobile);
                
                // 從 customer_mobile 表查找
                $customer_mobiles = CustomerMobile::where('mobile', 'like', $request->mobile . '%')
                    ->pluck('customer_id')
                    ->toArray();
                $customer_ids = array_merge($customer_ids, $customer_mobiles);
                
                // 去重並使用 whereIn
                $customer_ids = array_unique($customer_ids);
                if (!empty($customer_ids)) {
                    $query->whereIn('id', $customer_ids);
                } else {
                    $query->whereNull('id'); // 確保沒有結果
                }
            }
            if (!empty($request->group_id)) {
                $query->where('group_id', $request->group_id);
            }

            if (!empty($request->pet_name)) {
                $customer_ids = Sale::where('pet_name', 'like', '%' . $request->pet_name . '%')
                    ->pluck('customer_id');
                if ($customer_ids->isNotEmpty()) {
                    $query->whereIn('id', $customer_ids);
                } else {
                    $query->whereNull('id');  // Ensure no results if no sales match
                }
            }

            if ($county != 'null') {
                if (isset($county)) {
                    $query = $query->where('county', $county);
                } else {
                    $query = $query;
                }
            }
            $district = $request->district;
            if ($district != 'null') {
                if (isset($district)) {
                    $query = $query->where('district', $district);
                } else {
                    $query = $query;
                }
            }

            $address = $request->address;
            if (!empty($address) && $address != 'null') {
                $address = '%' . $request->address . '%';
                $query = $query->where('address', 'like', $address);
            }

            $customers = $query->paginate(30);
            $condition = $request->all();
        } else {
            $customers = collect();  // Return an empty collection if no request
            $condition = '';
        }

        return view('customer.customers')
            ->with('customers', $customers)
            ->with('request', $request)
            ->with('condition', $condition)
            ->with('customer_groups', $customer_groups)
            ->with('countys', $countys)
            ->with('districts', $districts);
    }

    public function customer_sale($id, Request $request)
    {
        $customer = Customer::where('id', $id)->first();
        if ($request) {
            $sales = Sale::where('customer_id', $id)->whereIn('status', [9, 100]);

            $after_date = $request->after_date;
            if ($after_date) {
                $sales = $sales->where('sale_date', '>=', $after_date);
            }
            $before_date = $request->before_date;
            if ($before_date) {
                $sales = $sales->where('sale_date', '<=', $before_date);
            }
            $sale_on = $request->sale_on;
            if ($sale_on) {
                $sales = $sales->where('sale_on', $sale_on);
            }
            $cust_mobile = $request->cust_mobile;
            if ($cust_mobile) {
                $customer = Customer::where('mobile', $cust_mobile)->first();
                $sales = $sales->where('customer_id', $customer->id);
            }
            $user = $request->user;
            if ($user != 'null') {
                if (isset($user)) {
                    $sales = $sales->where('user_id', $user);
                } else {
                    $sales = $sales;
                }
            }
            $pay_id = $request->pay_id;
            if ($pay_id) {
                $sales = $sales->where('pay_id', $pay_id);
            }
            $sales = $sales->orderby('id', 'desc')->paginate(15);
            $price_total = $sales->sum('pay_price');
            $condition = $request->all();

            foreach ($sales as $sale) {
                $sale_ids[] = $sale->id;
            }

            if (isset($sale_ids)) {
                $gdpaper_total = Sale_gdpaper::whereIn('sale_id', $sale_ids)->sum('gdpaper_total');
            } else {
                $gdpaper_total = 0;
            }
        } else {
            $condition = ' ';
            $sales = Sale::where('user_id', $id)->where('status', '1')->orderby('sale_date', 'desc')->paginate(15);
            $price_total = Sale::where('user_id', $id)->where('status', '1')->sum('pay_price');
        }
        $users = User::get();
        return view('cust_sale')
            ->with('sales', $sales)
            ->with('request', $request)
            ->with('customer', $customer)
            ->with('users', $users)
            ->with('condition', $condition)
            ->with('price_total', $price_total)
            ->with('gdpaper_total', $gdpaper_total);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $groups = CustGroup::where('status', 'up')->get();
        return view('customer.create')->with('groups', $groups)->with('hint', 0);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $groups = CustGroup::where('status', 'up')->get();

        // 檢查電話重複：檢查所有提交的電話，比對 customer.mobile 和 customer_mobile 表
        if ($request->not_mobile == '0' || $request->not_mobile == null) {
            $mobiles = $request->input('mobiles', []);

            // 過濾掉空值
            $mobiles = array_filter($mobiles, function ($mobile) {
                return !empty($mobile);
            });

            // 檢查每個電話是否重複
            foreach ($mobiles as $mobile) {
                // 檢查 customer 表的 mobile 欄位
                $customerExists = Customer::where('mobile', $mobile)->first();
                if ($customerExists) {
                    return view('customer.create')->with('groups', $groups)->with(['hint' => '1']);
                }

                // 檢查 customer_mobile 表
                $customerMobileExists = CustomerMobile::where('mobile', $mobile)->first();
                if ($customerMobileExists) {
                    return view('customer.create')->with('groups', $groups)->with(['hint' => '1']);
                }
            }
        }

        // 處理多個地址
        $addresses = $request->input('addresses', []);
        $counties = $request->input('county', []);
        $districts = $request->input('district', []);
        $mobiles = $request->input('mobiles', []);

        // 建立客戶
        $customer = new Customer;
        $customer->comment = $request->comment;
        $customer->group_id = 1;
        $customer->created_up = Auth::user()->id;
        $customer->name = $request->name;

        // 處理多個電話
        if ($request->not_mobile == 1) {
            $customer->mobile = '未提供電話';
        } else {
            if (!empty($mobiles) && !empty($mobiles[0])) {
                $customer->mobile = $mobiles[0];
            } else {
                $customer->mobile = '未提供電話';
            }
        }

        // 如果有地址，第一筆寫入 customer 表
        if (!empty($counties) && !empty($counties[0])) {
            $customer->county = $counties[0] ?? '';
            $customer->district = $districts[0] ?? '';
            $customer->address = $addresses[0];
        } else {
            // 如果沒有地址或選擇「未提供地址」
            $customer->county = '';
            $customer->district = '';
            $customer->address = '未提供地址';
        }

        $customer->save();

        // 所有地址都寫入 customer_addresses 表
        for ($i = 0; $i < count($addresses); $i++) {
            if (!empty($addresses[$i])) {
                $address = new CustomerAddress;
                $address->customer_id = $customer->id;
                $address->county = $counties[$i] ?? '';
                $address->district = $districts[$i] ?? '';
                $address->address = $addresses[$i];
                $address->is_primary = ($i === 0) ? 1 : 0;  // 第一筆為主要地址
                $address->save();
            }
        }
        // 處理多個電話
        for ($i = 0; $i < count($mobiles); $i++) {
            if (!empty($mobiles[$i])) {
                $mobile = new CustomerMobile;
                $mobile->customer_id = $customer->id;
                $mobile->mobile = $mobiles[$i];
                $mobile->is_primary = ($i === 0) ? 1 : 0;  // 第一筆為主要電話
                $mobile->save();
            }
        }

        // 如果沒有地址或選擇「未提供地址」，建立預設地址記錄
        if (empty($addresses) || $request->not_address == 1) {
            $address = new CustomerAddress;
            $address->customer_id = $customer->id;
            $address->county = '';
            $address->district = '';
            $address->address = '未提供地址';
            $address->is_primary = 1;
            $address->save();
        }

        $customer->load(['mobiles', 'addresses']);

        $historyChanges = $this->buildCustomerHistoryPayload(
            [],
            $customer,
            [],
            $this->extractMobilesForHistory($customer->mobiles),
            [],
            $this->extractAddressesForHistory($customer->addresses)
        );

        $this->persistCustomerHistory($customer, 'created', $historyChanges);

        return redirect()->route('customer');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $groups = CustGroup::where('status', 'up')->get();
        $customer = Customer::where('id', $id)->first();
        return view('customer.edit')->with('customer', $customer)->with('groups', $groups);
    }

    public function detail($id)
    {
        $groups = CustGroup::where('status', 'up')->get();
        $customer = Customer::with([
            'mobiles',
            'addresses',
            'histories.user',
            'createdBy',
        ])->findOrFail($id);
        return view('customer.detail')->with('customer', $customer)->with('groups', $groups);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $groups = CustGroup::where('status', 'up')->get();
        $customer = Customer::with(['mobiles', 'addresses'])->findOrFail($id);
        return view('customer.edit')->with('customer', $customer)->with('groups', $groups);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::with(['mobiles', 'addresses'])->findOrFail($id);
        $originalAttributes = $customer->attributesToArray();
        $originalMobiles = $this->extractMobilesForHistory($customer->mobiles);
        $originalAddresses = $this->extractAddressesForHistory($customer->addresses);
        $customer->name = $request->name;
        $customer->comment = $request->comment;
        if (isset($customer->group_id)) {
            $customer->group_id = $request->group_id;
        } else {
            $customer->group_id = 1;
        }

        // 處理多個電話
        $mobiles = $request->input('mobiles', []);
        
        // 檢查電話重複：檢查所有提交的電話，比對 customer.mobile 和 customer_mobile 表
        // 但需要排除當前客戶自己的電話
        $mobilesFiltered = array_filter($mobiles, function($mobile) {
            return !empty($mobile);
        });
        
        // 取得當前客戶的所有電話（用於排除）
        $currentCustomerMobiles = [];
        if (!empty($customer->mobile) && $customer->mobile != '未提供電話') {
            $currentCustomerMobiles[] = $customer->mobile;
        }
        foreach ($customer->mobiles as $mobile) {
            if (!empty($mobile->mobile)) {
                $currentCustomerMobiles[] = $mobile->mobile;
            }
        }
        
        // 檢查每個電話是否重複（排除當前客戶自己的電話）
        foreach ($mobilesFiltered as $mobile) {
            // 如果是當前客戶的電話，跳過檢查
            if (in_array($mobile, $currentCustomerMobiles)) {
                continue;
            }
            
            // 檢查 customer 表的 mobile 欄位
            $customerExists = Customer::where('mobile', $mobile)
                ->where('id', '!=', $id)  // 排除當前客戶
                ->first();
            if ($customerExists) {
                $groups = CustGroup::where('status', 'up')->get();
                return view('customer.edit')
                    ->with('customer', $customer)
                    ->with('groups', $groups)
                    ->with(['hint' => '1', 'error_message' => '電話號碼 ' . $mobile . ' 已被其他客戶使用']);
            }
            
            // 檢查 customer_mobile 表
            $customerMobileExists = CustomerMobile::where('mobile', $mobile)
                ->where('customer_id', '!=', $id)  // 排除當前客戶
                ->first();
            if ($customerMobileExists) {
                $groups = CustGroup::where('status', 'up')->get();
                return view('customer.edit')
                    ->with('customer', $customer)
                    ->with('groups', $groups)
                    ->with(['hint' => '1', 'error_message' => '電話號碼 ' . $mobile . ' 已被其他客戶使用']);
            }
        }

        // 如果有電話，第一筆寫入 customer 表的 mobile 欄位
        if (!empty($mobiles) && !empty($mobiles[0])) {
            $customer->mobile = $mobiles[0];
        } else {
            $customer->mobile = '未提供電話';
        }

        // 處理多個地址
        $addresses = $request->input('addresses', []);
        $counties = $request->input('county', []);
        $districts = $request->input('district', []);

        // 如果有地址，第一筆寫入 customer 表
        if (!empty($counties) && !empty($counties[0])) {
            $customer->county = $counties[0] ?? '';
            $customer->district = $districts[0] ?? '';
            $customer->address = $addresses[0];
        } else {
            // 如果沒有地址
            $customer->county = '';
            $customer->district = '';
            $customer->address = '';
        }

        $customer->save();

        // 刪除舊的電話記錄
        CustomerMobile::where('customer_id', $id)->delete();

        // 所有電話都寫入 customer_mobile 表
        for ($i = 0; $i < count($mobiles); $i++) {
            if (!empty($mobiles[$i])) {
                $mobile = new CustomerMobile;
                $mobile->customer_id = $customer->id;
                $mobile->mobile = $mobiles[$i];
                $mobile->is_primary = ($i === 0) ? 1 : 0;  // 第一筆為主要電話
                $mobile->save();
            }
        }

        // 刪除舊的地址記錄
        CustomerAddress::where('customer_id', $id)->delete();

        // 所有地址都寫入 customer_addresses 表
        for ($i = 0; $i < count($addresses); $i++) {
            if (!empty($addresses[$i])) {
                $address = new CustomerAddress;
                $address->customer_id = $customer->id;
                $address->county = $counties[$i] ?? '';
                $address->district = $districts[$i] ?? '';
                $address->address = $addresses[$i];
                $address->is_primary = ($i === 0) ? 1 : 0;  // 第一筆為主要地址
                $address->save();
            }
        }

        $customer->refresh()->load(['mobiles', 'addresses']);

        $historyChanges = $this->buildCustomerHistoryPayload(
            $originalAttributes,
            $customer,
            $originalMobiles,
            $this->extractMobilesForHistory($customer->mobiles),
            $originalAddresses,
            $this->extractAddressesForHistory($customer->addresses)
        );

        $this->persistCustomerHistory($customer, 'updated', $historyChanges);

        return redirect()->route('customer');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $groups = CustGroup::where('status', 'up')->get();
        $customer = Customer::with(['mobiles', 'addresses'])->where('id', $id)->first();
        return view('customer.del')->with('customer', $customer)->with('groups', $groups);
    }

    public function destroy($id)
    {
        $customer = Customer::where('id', $id)->first();
        $customer->delete();
        CustomerAddress::where('customer_id', $id)->delete();
        CustomerMobile::where('customer_id', $id)->delete();
        return redirect()->route('customer');
    }

    public function sales($id)
    {
        $sales = Sale::whereIn('status', ['9', '100'])->where('customer_id', $id)->orderby('sale_date', 'desc')->get();
        $customer = Customer::where('id', $id)->first();
        $contract_datas = Contract::where('customer_id', $id)->orderby('start_date', 'desc')->get();
        $lamp_datas = Lamp::where('customer_id', $id)->orderby('start_date', 'desc')->get();
        $puja_datas = PujaData::where('status', '1')->where('customer_id', $id)->orderby('date', 'desc')->get();
        $products = Product::where('status', 'up')->orderby('seq', 'asc')->orderby('price', 'desc')->get();
        foreach ($products as $product) {
            $product_name[$product->id] = $product->name;
        }
        return view('customer.sales')
            ->with('sales', $sales)
            ->with('customer', $customer)
            ->with('contract_datas', $contract_datas)
            ->with('lamp_datas', $lamp_datas)
            ->with('puja_datas', $puja_datas)
            ->with('product_name', $product_name);
    }

    public function export(Request $request)
    {
        $customers = Customer::get();
        $customer_groups = CustGroup::get();
        if ($request) {
            $county = $request->county;
            $district = $request->district;
            $query = Customer::query();  // Start building the query

            if (!empty($request->name)) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }
            if (!empty($request->mobile)) {
                $query->where('mobile', 'like', $request->mobile . '%');
            }
            if (!empty($request->group_id)) {
                $query->where('group_id', $request->group_id);
            }

            if (!empty($request->pet_name)) {
                $customer_ids = Sale::where('pet_name', 'like', $request->pet_name . '%')
                    ->pluck('customer_id');
                if ($customer_ids->isNotEmpty()) {
                    $query->whereIn('id', $customer_ids);
                } else {
                    $query->whereNull('id');  // Ensure no results if no sales match
                }
            }

            if ($county != 'null') {
                if (isset($county)) {
                    $query = $query->where('county', $county);
                } else {
                    $query = $query;
                }
            }
            $district = $request->district;
            if ($district != 'null') {
                if (isset($district)) {
                    $query = $query->where('district', $district);
                } else {
                    $query = $query;
                }
            }

            $address = $request->address;
            if ($district != '') {
                $address = '%' . $request->address . '%';
                if (isset($address)) {
                    $query = $query->where('address', 'like', $address);
                } else {
                    $query = $query;
                }
            }

            $customers = $query->get();
        }
        $fileName = '客戶資料匯出' . date('Y-m-d') . '.csv';

        $headers = array(
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        );
        // $header = array('日期', $after_date.'~' ,  $before_date);
        $columns = array('編號', '姓名', '電話', '寶貝名稱', '地址', '群組', '新增時間');

        $callback = function () use ($customers, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF), 3);
            fputcsv($file, $columns);

            foreach ($customers as $key => $customer) {
                $row['編號'] = $key + 1;
                $row['姓名'] = $customer->name;
                $row['電話'] = $customer->mobile;
                $row['寶貝名稱'] = '';
                if (isset($customer->sale_datas)) {
                    foreach ($customer->sale_datas as $sale_data) {
                        $row['寶貝名稱'] .= ($row['寶貝名稱'] == '' ? '' : "\r\n") . $sale_data->pet_name;
                    }
                }
                $row['群組'] = '';
                if (isset($customer->group)) {
                    $row['群組'] = $customer->group->name;
                }
                $row['地址'] = $customer->county . $customer->district . $customer->address;
                $row['新增時間'] = date('Y-m-d', strtotime($customer->created_at));
                fputcsv($file, array($row['編號'], $row['姓名'], $row['電話'], $row['寶貝名稱'], $row['地址'], $row['群組'], $row['新增時間']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function buildCustomerHistoryPayload(
        array $originalAttributes,
        Customer $customer,
        array $originalMobiles,
        array $newMobiles,
        array $originalAddresses,
        array $newAddresses
    ): array {
        $fieldsToTrack = [
            'name',
            'comment',
            'group_id',
            'mobile',
            'county',
            'district',
            'address',
            'bank_id',
            'bank_number',
            'commission',
            'visit_status',
            'contract_status',
            'assigned_to',
        ];

        $fieldChanges = [];

        foreach ($fieldsToTrack as $field) {
            $old = $originalAttributes[$field] ?? null;
            $new = $customer->{$field};
            if ($old != $new) {
                $fieldChanges[$field] = [
                    'old' => $old,
                    'new' => $new,
                ];
            }
        }

        $changes = [];

        if (!empty($fieldChanges)) {
            $changes['fields'] = $fieldChanges;
        }

        if ($originalMobiles !== $newMobiles) {
            $changes['mobiles'] = [
                'old' => $this->formatMobilesForHistory($originalMobiles),
                'new' => $this->formatMobilesForHistory($newMobiles),
            ];
        }

        if ($originalAddresses !== $newAddresses) {
            $changes['addresses'] = [
                'old' => $this->formatAddressesForHistory($originalAddresses),
                'new' => $this->formatAddressesForHistory($newAddresses),
            ];
        }

        return $changes;
    }

    private function extractMobilesForHistory($mobiles): array
    {
        if (empty($mobiles)) {
            return [];
        }

        return collect($mobiles)->map(function ($mobile) {
            return [
                'mobile' => $mobile->mobile ?? ($mobile['mobile'] ?? ''),
                'is_primary' => (bool) ($mobile->is_primary ?? ($mobile['is_primary'] ?? false)),
            ];
        })->values()->toArray();
    }

    private function extractAddressesForHistory($addresses): array
    {
        if (empty($addresses)) {
            return [];
        }

        return collect($addresses)->map(function ($address) {
            return [
                'county' => $address->county ?? ($address['county'] ?? ''),
                'district' => $address->district ?? ($address['district'] ?? ''),
                'address' => $address->address ?? ($address['address'] ?? ''),
                'is_primary' => (bool) ($address->is_primary ?? ($address['is_primary'] ?? false)),
            ];
        })->values()->toArray();
    }

    private function formatMobilesForHistory(array $mobiles): array
    {
        return array_map(function ($mobile) {
            $label = $mobile['mobile'] ?? '';
            if (isset($mobile['is_primary']) && $mobile['is_primary']) {
                $label = '[主要] ' . $label;
            }
            return $label !== '' ? $label : '未提供電話';
        }, $mobiles);
    }

    private function formatAddressesForHistory(array $addresses): array
    {
        return array_map(function ($address) {
            $parts = array_filter([
                $address['county'] ?? '',
                $address['district'] ?? '',
                $address['address'] ?? '',
            ]);
            $label = implode('', $parts);
            if (isset($address['is_primary']) && $address['is_primary']) {
                $label = '[主要] ' . $label;
            }
            return $label !== '' ? $label : '未提供地址';
        }, $addresses);
    }

    private function persistCustomerHistory(Customer $customer, string $action, array $changes): void
    {
        if (empty($changes)) {
            return;
        }

        CustomerHistory::create([
            'customer_id' => $customer->id,
            'changed_by' => Auth::id(),
            'action' => $action,
            'changes' => $changes,
        ]);
    }
}

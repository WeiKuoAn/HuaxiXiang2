<?php

namespace App\Http\Controllers;

use App\Models\CustGroup;
use App\Models\Customer;
use App\Models\Gdpaper;
use App\Models\MemorialDate;
use App\Models\PayData;
use App\Models\PayItem;
use App\Models\Plan;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Prom;
use App\Models\Sale;
use App\Models\Sale_gdpaper;
use App\Models\Sale_prom;
use App\Models\SaleAddress;
use App\Models\SaleChange;
use App\Models\SaleCompanyCommission;
use App\Models\SaleHistory;
use App\Models\SalePlan;
use App\Models\SaleSource;
use App\Models\SaleSouvenir;
use App\Models\SaleSplit;
use App\Models\Souvenir;
use App\Models\SouvenirType;
use App\Models\Suit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleDataControllerNew extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /* ajax */
    public function customer_search(Request $request)
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

    public function company_search(Request $request)
    {
        if ($request->ajax()) {
            $output = '';
            $hospitals = Customer::whereIn('group_id', [2, 3, 4, 5, 6, 7])->where('name', 'like', '%' . $request->cust_name . '%')->get();

            if ($hospitals) {
                foreach ($hospitals as $key => $hospital) {
                    $CustGroup = CustGroup::where('id', $hospital->group_id)->first();
                    $output .= '<option value="' . $hospital->id . '" label="' . $CustGroup->name . '(' . $hospital->name . ')-' . $hospital->mobile . '">';
                }
            }
            return Response($output);
        }
    }

    public function get_customers_by_type(Request $request)
    {
        if ($request->ajax()) {
            $type = $request->type;
            $group_id = '';
            if ($type == 'H') {
                $group_id = 2;
            } elseif ($type == 'B') {
                $group_id = 5;
            } elseif ($type == 'Salon') {
                $group_id = 6;
            } elseif ($type == 'dogpark') {
                $group_id = 3;
            } elseif ($type == 'G') {
                $group_id = 4;
            } elseif ($type == 'other') {
                $group_id = 7;
            }

            // 根據 type 找到對應的群組
            $group = CustGroup::where('id', $group_id)->first();

            if ($group || $type == 'self') {
                // 根據群組 ID 查詢客戶
                if ($type == 'self') {
                    $customers = User::where('status', '0')->get();
                } else {
                    $customers = Customer::where('group_id', $group->id)->get();
                }

                $output = '<option value="">請選擇...</option>';
                foreach ($customers as $customer) {
                    $output .= '<option value="' . $customer->id . '">';
                    if ($type == 'self') {
                        $output .= '（員工）' . $customer->name . '（' . $customer->mobile . '）';
                    } else {
                        $output .= '（' . $group->name . '）' . $customer->name . '（' . $customer->mobile . '）';
                    }
                    $output .= '</option>';
                }

                return response()->json(['html' => $output]);
            } else {
                return response()->json(['html' => '<option value="">找不到對應的群組</option>']);
            }
        }
    }

    public function prom_search(Request $request)
    {
        if ($request->ajax()) {
            $output = '';

            $proms = Prom::where('type', $request->select_prom)->where('status', 'up')->orderby('seq', 'asc')->get();

            if (isset($proms)) {
                foreach ($proms as $key => $prom) {
                    $output .= '<option value="' . $prom->id . '">' . $prom->name . '</option>';
                }
            } else {
                $output .= '<option value="">請選擇...</option>';
            }
            return Response($output);
        }
    }

    public function gdpaper_search(Request $request)
    {
        if ($request->ajax()) {
            $output = '';
            $product = Product::where('id', $request->gdpaper_id)->first();

            if ($product) {
                $output .= $product->price;
            }
            return Response($output);
        }
    }

    public function check_sale_on(Request $request)
    {
        if ($request->ajax()) {
            $sale_on = $request->sale_on;
            $current_id = $request->current_id ?? null;

            // 正規化單號格式，統一轉換為小寫並移除空格
            $normalized_sale_on = strtolower(trim($sale_on));

            // 查詢資料庫，使用正規化後的單號進行比較
            $query = Sale::whereRaw('LOWER(TRIM(sale_on)) = ?', [$normalized_sale_on]);

            // 如果是編輯模式，排除當前記錄
            if ($current_id) {
                $query->where('id', '!=', $current_id);
            }

            $existing_sale = $query->first();

            if ($existing_sale) {
                return response()->json([
                    'exists' => true,
                    'message' => '此單號已存在，請檢查是否重複輸入'
                ]);
            } else {
                return response()->json([
                    'exists' => false,
                    'message' => '單號可用'
                ]);
            }
        }

        return response()->json(['error' => '無效的請求'], 400);
    }

    public function final_price(Request $request)
    {
        if ($request->ajax()) {
            $customerId = $request->customer_id;  // 確保變數名稱一致
            $pet_name = $request->pet_name;
            $type_list = $request->type_list;  // 新增 type_list 參數
            $output = '';

            // 如果是追思單，直接允許新增
            if ($type_list == 'memorial') {
                return response()->json(['message' => 'OK', 'data' => null]);
            }

            // 根據不同的支付類型，查詢不同的相關單據
            switch ($request->pay_id) {
                case 'D':  // 尾款 - 需要找到對應的訂金單，且不能重複建立尾款
                    // 先檢查是否已有尾款單
                    $existing_tail = Sale::where('customer_id', $customerId)
                        ->where('pet_name', $pet_name)
                        ->where('type_list', '!=', 'memorial')  // 排除追思單
                        ->where('pay_id', 'D');  // 查詢尾款單

                    if (isset($request->current_id)) {
                        $existing_tail = $existing_tail->where('id', '<>', $request->current_id);
                    }
                    $existing_tail = $existing_tail->orderby('id', 'desc')->first();

                    if (isset($existing_tail->pay_id) && $existing_tail->pay_id == 'D') {
                        $output = '此客戶已建立尾款，請勿重複建立';
                        $data = $existing_tail;
                    } else {
                        // 檢查是否有訂金單
                        $data = Sale::where('customer_id', $customerId)
                            ->where('pet_name', $pet_name)
                            ->where('type_list', '!=', 'memorial')  // 排除追思單
                            ->where('pay_id', 'C');  // 只查詢訂金單

                        if (isset($request->current_id)) {
                            $data = $data->where('id', '<>', $request->current_id);
                        }
                        $data = $data->orderby('id', 'desc')->first();

                        if (isset($data->pay_id) && $data->pay_id == 'C') {
                            $output = 'OK';
                        } else {
                            $output = '此客戶尚未建立訂金，請先建立訂金';
                        }
                    }
                    break;

                case 'E':  // 追加 - 允許直接新增
                    $output = 'OK';
                    $data = null;
                    break;

                case 'A':  // 一次付清 - 檢查是否已有訂金單
                    $data = Sale::where('customer_id', $customerId)
                        ->where('pet_name', $pet_name)
                        ->where('type_list', '!=', 'memorial')  // 排除追思單
                        ->where('pay_id', 'C');  // 只查詢訂金單

                    if (isset($request->current_id)) {
                        $data = $data->where('id', '<>', $request->current_id);
                    }
                    $data = $data->orderby('id', 'desc')->first();

                    if (isset($data->pay_id) && $data->pay_id == 'C') {
                        $output = '此客戶已建立訂金，請先完成尾款';
                    } else {
                        $output = 'OK';
                    }
                    break;

                case 'C':  // 訂金 - 檢查是否已有訂金單
                    $data = Sale::where('customer_id', $customerId)
                        ->where('pet_name', $pet_name)
                        ->where('type_list', '!=', 'memorial')  // 排除追思單
                        ->where('pay_id', 'C');  // 只查詢訂金單

                    if (isset($request->current_id)) {
                        $data = $data->where('id', '<>', $request->current_id);
                    }
                    $data = $data->orderby('id', 'desc')->first();

                    if (isset($data->pay_id) && $data->pay_id == 'C') {
                        $output = '此客戶已建立訂金，請勿重複建立';
                    } else {
                        $output = 'OK';
                    }
                    break;

                default:
                    $output = '無效的支付狀態';
                    $data = null;
                    break;
            }

            return response()->json(['message' => $output, 'data' => $data]);
        }

        return response()->json(['message' => '無效的請求'], 400);
    }

    public function create_gpt()
    {
        $sources = SaleSource::where('status', 'up')->orderby('seq', 'asc')->get();
        $plans = Plan::where('status', 'up')->get();
        $products = Product::where('status', 'up')->where('category_id', '1')->orderby('seq', 'asc')->orderby('price', 'desc')->get();
        $customers = Customer::orderby('created_at', 'desc')->get();
        $source_companys = Customer::whereIn('group_id', [2, 3, 4, 5, 6, 7])->get();
        $suits = Suit::where('status', 'up')->get();
        $souvenir_types = SouvenirType::where('status', 'up')->get();
        $hospitals = Customer::whereIn('group_id', [2])->get();
        // dd($souvenirs);
        return view('sale.create_gpt')
            ->with('products', $products)
            ->with('sources', $sources)
            ->with('plans', $plans)
            ->with('customers', $customers)
            ->with('source_companys', $source_companys)
            ->with('suits', $suits)
            ->with('souvenir_types', $souvenir_types)
            ->with('hospitals', $hospitals);
    }

    public function edit_gpt($id)
    {
        $sources = SaleSource::where('status', 'up')->orderby('seq', 'asc')->get();
        $plans = Plan::where('status', 'up')->get();
        $products = Product::where('status', 'up')->where('category_id', '1')->orderby('seq', 'asc')->orderby('price', 'desc')->get();
        $customers = Customer::orderby('created_at', 'desc')->get();
        $source_companys = Customer::whereIn('group_id', [2, 3, 4, 5, 6, 7])->get();
        $suits = Suit::where('status', 'up')->get();
        $souvenir_types = SouvenirType::where('status', 'up')->get();
        $proms = Prom::where('status', 'up')->get();
        $hospitals = Customer::whereIn('group_id', [2])->get();
        $data = Sale::where('id', $id)->first();

        if (!$data) {
            return redirect()->route('sales')->with('error', '找不到指定的業務單');
        }

        // 載入相關資料
        $sale_proms = Sale_prom::where('sale_id', $id)->get();
        $sale_gdpapers = Sale_gdpaper::where('sale_id', $id)->get();
        $sale_souvenirs = SaleSouvenir::where('sale_id', $id)->get();

        // 載入每個後續處理項目對應的紀念品資料
        foreach ($sale_proms as $sale_prom) {
            $sale_prom->souvenir_data = SaleSouvenir::where('sale_id', $id)
                ->where('sale_prom_id', $sale_prom->id)
                ->first();

            // 判斷商品類型
            if ($sale_prom->souvenir_data) {
                if ($sale_prom->souvenir_data->souvenir_type == null) {
                    // 關聯商品：souvenir_type 為 null
                    $sale_prom->is_custom_product = false;
                    $sale_prom->product_id = $sale_prom->souvenir_data->product_name;  // product_name 存的是商品ID
                    $sale_prom->variant_id = $sale_prom->souvenir_data->product_variant_id;
                } else {
                    // 自訂商品：souvenir_type 不為 null
                    $sale_prom->is_custom_product = true;
                    $sale_prom->product_name = $sale_prom->souvenir_data->product_name;  // product_name 存的是商品名稱
                    $sale_prom->souvenir_type_id = $sale_prom->souvenir_data->souvenir_type;
                }
            }
        }

        // 載入來源公司資料
        $sale_company = SaleCompanyCommission::where('sale_id', $id)->first();

        // 載入接體地址資料
        $sale_address = SaleAddress::where('sale_id', $id)->first();

        // 載入紀念品資料
        $souvenirs = Souvenir::where('status', 'up')->get();

        return view('sale.edit_gpt')
            ->with('data', $data)
            ->with('products', $products)
            ->with('sources', $sources)
            ->with('plans', $plans)
            ->with('customers', $customers)
            ->with('source_companys', $source_companys)
            ->with('suits', $suits)
            ->with('souvenir_types', $souvenir_types)
            ->with('proms', $proms)
            ->with('hospitals', $hospitals)
            ->with('sale_proms', $sale_proms)
            ->with('sale_gdpapers', $sale_gdpapers)
            ->with('sale_souvenirs', $sale_souvenirs)
            ->with('sale_company', $sale_company)
            ->with('sale_address', $sale_address)
            ->with('souvenirs', $souvenirs);
    }

    public function test()
    {
        $sources = SaleSource::where('status', 'up')->orderby('seq', 'asc')->get();
        $plans = Plan::where('status', 'up')->get();
        $products = Product::where('status', 'up')->where('category_id', '1')->orderby('seq', 'asc')->orderby('price', 'desc')->get();

        return view('sale.create_test')
            ->with('products', $products)
            ->with('sources', $sources)
            ->with('plans', $plans);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_gpt(Request $request)
    {
        // 使用正則表達式匹配No.後的數字(客戶)
        $sale = new Sale();
        $sale->sale_on = 'No.' . $request->sale_on;
        $sale->user_id = Auth::user()->id;
        $sale->sale_date = $request->sale_date;
        $sale->type_list = $request->type_list;
        $sale->customer_id = $request->cust_name_q;
        $sale->pet_name = $request->pet_name;
        $sale->kg = $request->kg;
        $sale->suit_id = $request->suit_id;
        $sale->variety = $request->variety;
        $sale->type = $request->type;
        if ($request->type_list == 'memorial') {
            // 如果是追思單就客戶為空
            $sale->plan_id = '4';
        } else {
            $sale->plan_id = $request->plan_id;
        }
        $sale->plan_price = $request->plan_price;
        $sale->pay_id = $request->pay_id;
        // 尾款或追加為方案價格
        if (isset($request->final_price)) {
            $sale->plan_price = $request->final_price;
        }
        $sale->pay_price = $request->pay_price;
        if ($request->pay_method == 'B' || $request->pay_method == 'C') {
            $sale->cash_price = $request->cash_price;
            $sale->transfer_price = $request->transfer_price;
            $sale->transfer_number = $request->transfer_number;
            $sale->transfer_channel = $request->transfer_channel;
        }
        $sale->pay_method = $request->pay_method;
        $sale->total = $request->total;
        $sale->comm = $request->comm;
        // 儲存宗教與往生日期（若前端未顯示則可能為空）
        $sale->religion = $request->religion;
        $sale->religion_other = $request->religion_other;
        $sale->death_date = $request->death_date;
        if (Auth::user()->job_id == '8') {
            $sale->status = '100';
        }
        if ($request->send == 1) {
            $sale->send = $request->send;
        } else {
            $sale->send = 0;
        }
        if ($request->connector_address == 1) {
            $sale->connector_address = $request->connector_address;
        } else {
            $sale->connector_address = 0;
        }
        if ($request->connector_hospital_address == 1) {
            $sale->hospital_address = $request->hospital_address;
        }
        if ($request->cooperation_price == 1) {
            $sale->cooperation_price = $request->cooperation_price;
        } else {
            $sale->cooperation_price = 0;
        }
        $sale->save();

        $sale_id = Sale::orderby('id', 'desc')->first();

        // 建立 / 更新重要日期（佛道教且有往生日期時）
        if (in_array($sale->religion, ['buddhism', 'taoism', 'buddhism_taoism']) && !empty($sale->death_date)) {
            $dates = MemorialDate::calculateMemorialDates($sale->death_date, $sale->plan_id);
            MemorialDate::updateOrCreate(
                ['sale_id' => $sale_id->id],
                array_merge($dates, [
                    'sale_id' => $sale_id->id,
                    'seventh_reserved' => false,
                    'seventh_reserved_at' => null,
                    'forty_ninth_reserved' => false,
                    'forty_ninth_reserved_at' => null,
                    'hundredth_reserved' => false,
                    'hundredth_reserved_at' => null,
                    'anniversary_reserved' => false,
                    'anniversary_reserved_at' => null,
                    'notes' => null
                ])
            );
        } else {
            // 不適用時移除既有的重要日期記錄
            MemorialDate::where('sale_id', $sale_id->id)->delete();
        }

        // 如果存在來源公司名稱的話就存入
        if (isset($request->source_company_name_q)) {
            $CompanyCommission = new SaleCompanyCommission();
            $CompanyCommission->sale_date = $request->sale_date;
            $CompanyCommission->type = $request->type;
            $CompanyCommission->customer_id = $request->cust_name_q;
            $CompanyCommission->sale_id = $sale_id->id;
            $CompanyCommission->company_id = $request->source_company_name_q;
            $CompanyCommission->plan_price = $request->plan_price;
            if ($request->cooperation_price != 1) {  // 如果不是院內就新增
                if ($request->plan_price / 2 > 2500) {
                    $CompanyCommission->commission = 2500;
                } else {
                    $CompanyCommission->commission = $request->plan_price / 2;
                }
            } else {
                $CompanyCommission->commission = $request->plan_price;
                $CompanyCommission->cooperation_price = $request->cooperation_price;
            }
            $CompanyCommission->save();
        }
        // 要為派件單且支付類別為一次跟訂金
        if ($request->type_list == 'dispatch' && $request->pay_id == 'A' || $request->pay_id == 'C') {
            if ($request->send == 1) {
                $SaleAddress = new SaleAddress();
                $SaleAddress->sale_id = $sale_id->id;
                $SaleAddress->send = '1';
                $SaleAddress->save();
            } elseif ($request->connector_address == 1) {
                $SaleAddress = new SaleAddress();
                $SaleAddress->sale_id = $sale_id->id;
                $SaleAddress->county = $request->county;
                $SaleAddress->district = $request->district;
                $SaleAddress->address = $request->address;
                $SaleAddress->save();
            } elseif ($request->connector_hospital_address == 1) {
                $SaleAddress = new SaleAddress();
                $SaleAddress->sale_id = $sale_id->id;
                $SaleAddress->send = '2';
                $cust_data = Customer::where('id', $request->cust_name_q)->first();
                if (isset($cust_data)) {
                    $SaleAddress->county = $cust_data->county;
                    $SaleAddress->district = $cust_data->district;
                    $SaleAddress->address = $cust_data->address;
                }
                $SaleAddress->save();
            } else {
                $cust_data = Customer::where('id', $request->cust_name_q)->first();
                if (isset($cust_data)) {
                    $SaleAddress = new SaleAddress();
                    $SaleAddress->sale_id = $sale_id->id;
                    $SaleAddress->county = $cust_data->county;
                    $SaleAddress->district = $cust_data->district;
                    $SaleAddress->address = $cust_data->address;
                    $SaleAddress->save();
                }
            }
        }

        foreach ($request->select_proms as $key => $select_prom) {
            if (isset($select_prom)) {
                $prom = new Sale_prom();
                $prom->prom_type = $request->select_proms[$key];
                $prom->sale_id = $sale_id->id;
                $prom->prom_id = $request->prom[$key];
                $prom->prom_total = $request->prom_total[$key];
                $prom->comment = $request->prom_extra_text[$key];
                $prom->save();

                // 這裡就能取得 $prom->id
                // 如果有紀念品資料，這裡直接建立
                if (
                    isset($request->product_souvenir_types[$key]) ||
                    isset($request->product_proms[$key])
                ) {
                    $souvenir = new SaleSouvenir();
                    $souvenir->sale_id = $sale_id->id;
                    $souvenir->sale_prom_id = $prom->id;  // 關鍵：這裡用剛剛新增的 Sale_prom id
                    $souvenir->souvenir_type = $request->product_souvenir_types[$key];
                    if (isset($request->product_proms[$key])) {
                        $souvenir->product_name = $request->product_proms[$key];
                    } else {
                        $souvenir->product_name = $request->product_name[$key];
                    }
                    $souvenir->product_num = $request->product_num[$key];

                    // 處理細項 ID
                    if (isset($request->product_variants[$key]) && !empty($request->product_variants[$key])) {
                        $souvenir->product_variant_id = $request->product_variants[$key];
                    }

                    $souvenir->total = $request->prom_total[$key];
                    $souvenir->comment = $request->product_comment[$key];
                    $souvenir->save();
                }
            }
        }

        foreach ($request->gdpaper_ids as $key => $gdpaper_id) {
            if (isset($gdpaper_id)) {
                $gdpaper = new Sale_gdpaper();
                $gdpaper->sale_id = $sale_id->id;
                $gdpaper->type_list = $request->type_list;
                $gdpaper->gdpaper_id = $request->gdpaper_ids[$key];
                $gdpaper->gdpaper_num = $request->gdpaper_num[$key];
                $gdpaper->gdpaper_total = $request->gdpaper_total[$key];
                $gdpaper->save();
            }
        }

        // 業務單軌跡-新增
        $sale_history = new SaleHistory();
        $sale_history->sale_id = $sale_id->id;
        $sale_history->user_id = Auth::user()->id;
        $sale_history->state = 'create';
        $sale_history->save();

        return redirect()->route('sale.create');
    }

    public function index(Request $request)
    {
        $check_users = User::where('status', '0')->whereIn('job_id', [1, 2, 8, 9, 10])->orderby('seq')->get();
        if ($request) {
            $status = $request->status;
            if (!isset($status) || $status == 'not_check') {
                $sales = Sale::whereIn('status', [1, 2]);
            }
            if ($status == 'check') {
                $sales = Sale::whereIn('status', [9, 100]);
            }
            $type_list = $request->type_list;
            if ($type_list != 'null') {
                if (isset($type_list)) {
                    $sales = $sales->where('type_list', $type_list);
                } else {
                    $sales = $sales;
                }
            }

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
                $sale_on = '%' . $request->sale_on . '%';
                $sales = $sales->where('sale_on', 'like', $sale_on);
            }
            $cust_name = $request->cust_name;

            if ($cust_name) {
                $cust_name = $request->cust_name . '%';
                $customers = Customer::where('name', 'like', $cust_name)->get();
                foreach ($customers as $customer) {
                    $customer_ids[] = $customer->id;
                }
                if (isset($customer_ids)) {
                    $sales = $sales->whereIn('customer_id', $customer_ids);
                } else {
                    $sales = $sales;
                }
            }

            $pet_name = $request->pet_name;
            if ($pet_name) {
                $pet_name = $request->pet_name . '%';
                $sales = $sales->where('pet_name', 'like', $pet_name);
            }

            $user = $request->user;
            if ($user != 'null') {
                if (isset($user)) {
                    $sales = $sales->where('user_id', $user);
                } else {
                    $sales = $sales;
                }
            }

            $plan = $request->plan;
            if ($plan != 'null') {
                if (isset($plan)) {
                    $sales = $sales->where('plan_id', $plan);
                } else {
                    $sales = $sales;
                }
            }

            $pay_id = $request->pay_id;
            if ($pay_id) {
                if ($pay_id == 'A') {
                    $sales = $sales->whereIn('pay_id', ['A', 'B']);
                } else {
                    $sales = $sales->where('pay_id', $pay_id);
                }
            }
            if (isset($after_date)) {
                $other_after_date = $after_date . ' 00:00:00';
            }
            if (isset($before_date)) {
                $other_before_date = $before_date . ' 11:59:59';
            }

            $check_user_id = $request->check_user_id;
            if ($check_user_id != 'null') {
                if (isset($check_user_id)) {
                    $sales = $sales->where('check_user_id', $check_user_id);
                } else {
                    $sales = $sales;
                }
            }

            $other = $request->other;
            if ($other == 'change') {
                if (!isset($sale_change_ids)) {
                    $sale_change_ids = [];
                }
                if (isset($other_after_date)) {
                    $sale_changes = SaleChange::where('updated_at', '>=', $other_after_date)->get();
                } elseif (isset($other_before_date)) {
                    $sale_changes = SaleChange::where('updated_at', '<=', $other_before_date)->get();
                } elseif (isset($other_after_date) && isset($other_before_date)) {
                    $sale_changes = SaleChange::where('updated_at', '>=', $other_after_date)->where('updated_at', '<=', $other_before_date)->get();
                } else {
                    $sale_changes = SaleChange::get();
                }
                foreach ($sale_changes as $sale_change) {
                    $sale_change_ids[] = $sale_change->sale_id;
                }
                if (empty($sale_change_ids)) {
                    $sales = $sales->whereIn('id', $sale_change_ids);
                }
                $sales = $sales->whereIn('id', $sale_change_ids);
            } elseif ($other == 'split') {
                if (!isset($sale_split_ids)) {
                    $sale_split_ids = [];
                }
                if (isset($other_after_date)) {
                    $sale_splits = SaleSplit::where('updated_at', '>=', $other_after_date)->get();
                } elseif (isset($other_before_date)) {
                    $sale_splits = SaleSplit::where('updated_at', '<=', $other_before_date)->get();
                } elseif (isset($other_after_date) && isset($other_before_date)) {
                    $sale_splits = SaleSplit::where('updated_at', '>=', $other_after_date)->where('updated_at', '<=', $other_before_date)->get();
                } else {
                    $sale_splits = SaleSplit::get();
                }
                foreach ($sale_splits as $sale_split) {
                    $sale_split_ids[] = $sale_split->sale_id;
                }
                if (empty($sale_split_ids)) {
                    $sales = $sales->whereIn('id', $sale_split_ids);
                }
            }

            $price_total = $sales->sum('pay_price');
            $sales = $sales->orderby('sale_date', 'desc')->orderby('user_id', 'desc')->orderby('sale_on', 'desc')->paginate(50);

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
            $price_total = Sale::where('status', '1')->sum('pay_price');
            $sales = Sale::orderby('sale_date', 'desc')->orderby('user_id', 'desc')->orderby('sale_on', 'desc')->where('status', '1')->paginate(50);
        }
        $users = User::whereIn('job_id', [1, 3, 5])->where('status', '0')->orderby('seq')->get();
        $sources = SaleSource::where('status', 'up')->orderby('seq', 'asc')->get();
        $plans = Plan::where('status', 'up')->get();

        if (Auth::user()->level != 2 || Auth::user()->job_id == '9' || Auth::user()->job_id == '10') {
            return view('sale.index')
                ->with('sales', $sales)
                ->with('users', $users)
                ->with('request', $request)
                ->with('condition', $condition)
                ->with('price_total', $price_total)
                ->with('gdpaper_total', $gdpaper_total)
                ->with('sources', $sources)
                ->with('plans', $plans)
                ->with('check_users', $check_users);
        } else {
            return redirect()->route('person.sales');
        }
    }

    public function wait_index(Request $request)  // 代確認業務單
    {
        $sales = Sale::where('status', 3);
        if ($request) {
            $after_date = $request->after_date;
            if ($after_date) {
                $sales = $sales->where('sale_date', '>=', $after_date);
            }
            $before_date = $request->before_date;
            if ($before_date) {
                $sales = $sales->where('sale_date', '<=', $before_date);
            }
            $user = $request->user;
            if ($user != 'null') {
                if (isset($user)) {
                    $sales = $sales->where('user_id', $user);
                } else {
                    $sales = $sales;
                }
            }
        } else {
            $sales = $sales->orderby('sale_date', 'desc')->orderby('user_id', 'desc')->orderby('sale_on', 'desc');
        }
        $sales = $sales->get();
        $users = User::where('status', '0')->whereIn('job_id', [3, 5, 10])->get();

        $total = 0;
        foreach ($sales as $sale) {
            $total += $sale->pay_price;
        }
        return view('sale.wait')->with('sales', $sales)->with('request', $request)->with('users', $users)->with('total', $total);
    }

    public function user_sale($id, Request $request)  // 從用戶管理進去看業務單
    {
        $user = User::where('id', $id)->first();
        $plans = Plan::where('status', 'up')->get();
        if ($request) {
            $status = $request->status;
            if (!isset($status) || $status == 'not_check') {
                $sales = Sale::where('user_id', $id)->whereIn('status', [1, 2]);
            }
            if ($status == 'check') {
                $sales = Sale::where('user_id', $id)->whereIn('status', [9, 100]);;
            }
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
                $sale_on = '%' . $request->sale_on . '%';
                $sales = $sales->where('sale_on', 'like', $sale_on);
            }
            $cust_mobile = $request->cust_mobile;
            if ($cust_mobile) {
                $customer = Customer::where('mobile', $cust_mobile)->first();
                $sales = $sales->where('customer_id', $customer->id);
            }
            $pay_id = $request->pay_id;
            if ($pay_id) {
                $sales = $sales->where('pay_id', $pay_id);
            }
            $sales = $sales->orderby('sale_date', 'desc')->orderby('sale_date', 'desc')->orderby('user_id', 'desc')->orderby('sale_on', 'desc')->paginate(15);
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
            $sales = Sale::where('user_id', $id)->where('status', '1')->orderby('sale_date', 'desc')->orderby('user_id', 'desc')->orderby('sale_on', 'desc')->paginate(15);
            $price_total = Sale::where('user_id', $id)->where('status', '1')->sum('pay_price');
        }

        return view('sale.user_index')
            ->with('sales', $sales)
            ->with('user', $user)
            ->with('request', $request)
            ->with('condition', $condition)
            ->with('price_total', $price_total)
            ->with('gdpaper_total', $gdpaper_total)
            ->with('plans', $plans);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sources = SaleSource::where('status', 'up')->orderby('seq', 'asc')->get();
        $customers = Customer::get();
        $plans = Plan::where('status', 'up')->get();
        $products = Product::where('status', 'up')->where('category_id', '1')->orderby('seq', 'asc')->orderby('price', 'desc')->get();
        $proms = Prom::where('status', 'up')->orderby('seq', 'asc')->get();
        $data = Sale::where('id', $id)->first();
        $sale_gdpapers = Sale_gdpaper::where('sale_id', $id)->get();
        $sale_proms = Sale_prom::where('sale_id', $id)->get();
        $sale_company = SaleCompanyCommission::where('sale_id', $id)->first();
        $sale_address = SaleAddress::where('sale_id', $id)->first();
        $source_companys = Customer::whereIn('group_id', [2, 3, 4, 5, 6, 7])->get();
        $suits = Suit::where('status', 'up')->get();
        $sale_souvenirs = SaleSouvenir::where('sale_id', $id)->get();
        $souvenir_types = SouvenirType::where('status', 'up')->get();
        $souvenirs = Souvenir::where('status', 'up')->get();
        $users = User::where('status', '0')->get();

        return view('sale.edit')
            ->with('data', $data)
            ->with('customers', $customers)
            ->with('plans', $plans)
            ->with('products', $products)
            ->with('proms', $proms)
            ->with('sale_proms', $sale_proms)
            ->with('sale_gdpapers', $sale_gdpapers)
            ->with('sources', $sources)
            ->with('sale_company', $sale_company)
            ->with('sale_address', $sale_address)
            ->with('source_companys', $source_companys)
            ->with('suits', $suits)
            ->with('souvenir_types', $souvenir_types)
            ->with('sale_souvenirs', $sale_souvenirs)
            ->with('souvenirs', $souvenirs)
            ->with('users', $users);
    }

    public function check_show_gpt(Request $request , $id)
    {
        $sources = SaleSource::where('status', 'up')->orderby('seq', 'asc')->get();
        $plans = Plan::where('status', 'up')->get();
        $products = Product::where('status', 'up')->where('category_id', '1')->orderby('seq', 'asc')->orderby('price', 'desc')->get();
        $customers = Customer::orderby('created_at', 'desc')->get();
        $source_companys = Customer::whereIn('group_id', [2, 3, 4, 5, 6, 7])->get();
        $suits = Suit::where('status', 'up')->get();
        $souvenir_types = SouvenirType::where('status', 'up')->get();
        $proms = Prom::where('status', 'up')->get();
        $hospitals = Customer::whereIn('group_id', [2])->get();
        $data = Sale::where('id', $id)->first();

        if (!$data) {
            return redirect()->route('sales')->with('error', '找不到指定的業務單');
        }

        // 載入相關資料
        $sale_proms = Sale_prom::where('sale_id', $id)->get();
        $sale_gdpapers = Sale_gdpaper::where('sale_id', $id)->get();
        $sale_souvenirs = SaleSouvenir::where('sale_id', $id)->get();

        // 載入每個後續處理項目對應的紀念品資料
        foreach ($sale_proms as $sale_prom) {
            $sale_prom->souvenir_data = SaleSouvenir::where('sale_id', $id)
                ->where('sale_prom_id', $sale_prom->id)
                ->first();

            // 判斷商品類型
            if ($sale_prom->souvenir_data) {
                if ($sale_prom->souvenir_data->souvenir_type == null) {
                    // 關聯商品：souvenir_type 為 null
                    $sale_prom->is_custom_product = false;
                    $sale_prom->product_id = $sale_prom->souvenir_data->product_name;  // product_name 存的是商品ID
                    $sale_prom->variant_id = $sale_prom->souvenir_data->product_variant_id;
                } else {
                    // 自訂商品：souvenir_type 不為 null
                    $sale_prom->is_custom_product = true;
                    $sale_prom->product_name = $sale_prom->souvenir_data->product_name;  // product_name 存的是商品名稱
                    $sale_prom->souvenir_type_id = $sale_prom->souvenir_data->souvenir_type;
                }
            }
        }

        // 載入來源公司資料
        $sale_company = SaleCompanyCommission::where('sale_id', $id)->first();

        // 載入接體地址資料
        $sale_address = SaleAddress::where('sale_id', $id)->first();

        // 載入紀念品資料
        $souvenirs = Souvenir::where('status', 'up')->get();

        $previousUrl = $request->session()->get('_previous.url');
        $parsedUrl = parse_url($previousUrl);

        // 初始化变量
        $user = null;
        $afterDate = null;
        $beforeDate = null;
        // dd($source_companys);

        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParameters);

            // 检查并获取user参数的值
            if (isset($queryParameters['user'])) {
                $user = $queryParameters['user'];
            }
            if (isset($queryParameters['after_date'])) {
                $afterDate = $queryParameters['after_date'];
            }
            if (isset($queryParameters['before_date'])) {
                $beforeDate = $queryParameters['before_date'];
            }

            // 存储参数值在会话中
            session(['user' => $user, 'afterDate' => $afterDate, 'beforeDate' => $beforeDate]);
        }

        return view('sale.check_gpt')
            ->with('data', $data)
            ->with('products', $products)
            ->with('sources', $sources)
            ->with('plans', $plans)
            ->with('customers', $customers)
            ->with('source_companys', $source_companys)
            ->with('suits', $suits)
            ->with('souvenir_types', $souvenir_types)
            ->with('proms', $proms)
            ->with('hospitals', $hospitals)
            ->with('sale_proms', $sale_proms)
            ->with('sale_gdpapers', $sale_gdpapers)
            ->with('sale_souvenirs', $sale_souvenirs)
            ->with('sale_company', $sale_company)
            ->with('sale_address', $sale_address)
            ->with('souvenirs', $souvenirs);
    }

    public function check_update_gpt(Request $request, $id)
    {
        $sale = Sale::where('id', $id)->first();

        if (isset($request->admin_check)) {
            if ($request->admin_check == 'check') {
                $sale->status = '9';
                $sale->check_user_id = Auth::user()->id;
                $sale->save();

                // 業務單軌跡-確認對帳
                $sale_history = new SaleHistory();
                $sale_history->sale_id = $id;
                $sale_history->user_id = Auth::user()->id;
                $sale_history->state = 'check';
                $sale_history->save();
            }
            if ($request->admin_check == 'not_check') {
                $sale->status = '1';
                $sale->check_user_id = null;
                $sale->save();

                // 業務單軌跡-撤回對帳
                $sale_history = new SaleHistory();
                $sale_history->sale_id = $id;
                $sale_history->user_id = Auth::user()->id;
                $sale_history->state = 'not_check';
                $sale_history->save();
            }
            if ($request->admin_check == 'reset') {
                $sale->status = '1';
                $sale->check_user_id = null;
                $sale->save();

                // 業務單軌跡-已對帳還原未對帳
                $sale_history = new SaleHistory();
                $sale_history->sale_id = $id;
                $sale_history->user_id = Auth::user()->id;
                $sale_history->state = 'reset';
                $sale_history->save();
            }
            $user = session('user');
            $afterDate = session('afterDate');
            $beforeDate = session('beforeDate');
            // dd($user, $afterDate, $beforeDate);
            // 构建重定向的URL，将筛选条件添加到URL中
            if (session()->has('user') || session()->has('after_date') || session()->has('before_date')) {
                $url = route('wait.sales', ['user' => $user, 'after_date' => $afterDate, 'before_date' => $beforeDate]);
                // 重定向到筛选页面并传递筛选条件
                return redirect($url);
            } else {
                return redirect()->route('wait.sales');
            }
        } else {
            if ($request->user_check == 'usercheck') {
                $sale->status = '3';
                $sale->save();

                // 業務單軌跡-專員送出對帳
                $sale_history = new SaleHistory();
                $sale_history->sale_id = $id;
                $sale_history->user_id = Auth::user()->id;
                $sale_history->state = 'usercheck';
                $sale_history->save();
            }
            return redirect()->route('person.sales');
        }
    }

    public function check_show(Request $request, $id)
    {
        $source_companys = Customer::whereIn('group_id', [2, 3, 4, 5, 6, 7])->get();
        $sources = SaleSource::where('status', 'up')->orderby('seq', 'asc')->get();
        $customers = Customer::get();
        $plans = Plan::where('status', 'up')->get();
        $products = Product::where('status', 'up')->where('category_id', '1')->orderby('seq', 'asc')->orderby('price', 'desc')->get();
        $proms = Prom::where('status', 'up')->orderby('seq', 'asc')->get();
        $data = Sale::where('id', $id)->first();
        $sale_gdpapers = Sale_gdpaper::where('sale_id', $id)->get();
        $sale_proms = Sale_prom::where('sale_id', $id)->get();
        $sale_company = SaleCompanyCommission::where('sale_id', $id)->first();
        $sale_address = SaleAddress::where('sale_id', $id)->first();
        $suits = Suit::where('status', 'up')->get();
        $sale_souvenirs = SaleSouvenir::where('sale_id', $id)->get();
        $souvenir_types = SouvenirType::where('status', 'up')->get();
        $souvenirs = Souvenir::where('status', 'up')->get();

        // 获取上一个页面的 URL
        // 从_previous中获取user参数的值
        // dd($request->session());
        $previousUrl = $request->session()->get('_previous.url');
        $parsedUrl = parse_url($previousUrl);

        // 初始化变量
        $user = null;
        $afterDate = null;
        $beforeDate = null;
        // dd($source_companys);

        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParameters);

            // 检查并获取user参数的值
            if (isset($queryParameters['user'])) {
                $user = $queryParameters['user'];
            }
            if (isset($queryParameters['after_date'])) {
                $afterDate = $queryParameters['after_date'];
            }
            if (isset($queryParameters['before_date'])) {
                $beforeDate = $queryParameters['before_date'];
            }

            // 存储参数值在会话中
            session(['user' => $user, 'afterDate' => $afterDate, 'beforeDate' => $beforeDate]);
        }

        return view('sale.check')
            ->with('data', $data)
            ->with('customers', $customers)
            ->with('plans', $plans)
            ->with('products', $products)
            ->with('proms', $proms)
            ->with('sale_proms', $sale_proms)
            ->with('sale_gdpapers', $sale_gdpapers)
            ->with('sources', $sources)
            ->with('sale_company', $sale_company)
            ->with('sale_address', $sale_address)
            ->with('source_companys', $source_companys)
            ->with('suits', $suits)
            ->with('souvenirs', $souvenirs)
            ->with('sale_souvenirs', $sale_souvenirs)
            ->with('souvenir_types', $souvenir_types);
    }

    public function sale_on_show($sale_on)
    {
        $source_companys = Customer::whereIn('group_id', [2, 3, 4, 5, 6, 7])->get();
        $sources = SaleSource::where('status', 'up')->orderby('seq', 'asc')->get();
        $customers = Customer::get();
        $plans = Plan::where('status', 'up')->get();
        $products = Product::where('status', 'up')->where('category_id', '1')->orderby('seq', 'asc')->orderby('price', 'desc')->get();
        $proms = Prom::where('status', 'up')->orderby('seq', 'asc')->get();
        $data = Sale::where('sale_on', $sale_on)->first();
        $sale_gdpapers = Sale_gdpaper::where('sale_id', $data->id)->get();
        $sale_proms = Sale_prom::where('sale_id', $data->id)->get();
        $sale_company = SaleCompanyCommission::where('sale_id', $data->id)->first();
        $sale_address = SaleAddress::where('sale_id', $data->id)->first();
        $suits = Suit::where('status', 'up')->get();
        $sale_souvenirs = SaleSouvenir::where('sale_id', $data->id)->get();
        $souvenir_types = SouvenirType::where('status', 'up')->get();
        $souvenirs = Souvenir::where('status', 'up')->get();
        return view('sale.check')
            ->with('data', $data)
            ->with('customers', $customers)
            ->with('plans', $plans)
            ->with('products', $products)
            ->with('proms', $proms)
            ->with('sale_proms', $sale_proms)
            ->with('sale_gdpapers', $sale_gdpapers)
            ->with('sources', $sources)
            ->with('sale_company', $sale_company)
            ->with('sale_address', $sale_address)
            ->with('source_companys', $source_companys)
            ->with('suits', $suits)
            ->with('souvenirs', $souvenirs)
            ->with('sale_souvenirs', $sale_souvenirs)
            ->with('souvenir_types', $souvenir_types);
    }

    public function check_update(Request $request, $id)
    {
        $sale = Sale::where('id', $id)->first();

        if (Auth::user()->level != 2 || Auth::user()->job_id == 9) {
            if ($request->admin_check == 'check') {
                $sale->status = '9';
                $sale->check_user_id = Auth::user()->id;
                $sale->save();

                // 業務單軌跡-確認對帳
                $sale_history = new SaleHistory();
                $sale_history->sale_id = $id;
                $sale_history->user_id = Auth::user()->id;
                $sale_history->state = 'check';
                $sale_history->save();
            }
            if ($request->admin_check == 'not_check') {
                $sale->status = '1';
                $sale->check_user_id = null;
                $sale->save();

                // 業務單軌跡-撤回對帳
                $sale_history = new SaleHistory();
                $sale_history->sale_id = $id;
                $sale_history->user_id = Auth::user()->id;
                $sale_history->state = 'not_check';
                $sale_history->save();
            }
            if ($request->admin_check == 'reset') {
                $sale->status = '1';
                $sale->check_user_id = null;
                $sale->save();

                // 業務單軌跡-已對帳還原未對帳
                $sale_history = new SaleHistory();
                $sale_history->sale_id = $id;
                $sale_history->user_id = Auth::user()->id;
                $sale_history->state = 'reset';
                $sale_history->save();
            }
            $user = session('user');
            $afterDate = session('afterDate');
            $beforeDate = session('beforeDate');

            // 构建重定向的URL，将筛选条件添加到URL中
            if (session()->has('user') || session()->has('after_date') || session()->has('before_date')) {
                $url = route('wait.sales', ['user' => $user, 'after_date' => $afterDate, 'before_date' => $beforeDate]);
                // 重定向到筛选页面并传递筛选条件
                return redirect($url);
            } else {
                return redirect()->route('wait.sales');
            }
        } else {
            if ($request->user_check == 'usercheck') {
                $sale->status = '3';
                $sale->save();

                // 業務單軌跡-專員送出對帳
                $sale_history = new SaleHistory();
                $sale_history->sale_id = $id;
                $sale_history->user_id = Auth::user()->id;
                $sale_history->state = 'usercheck';
                $sale_history->save();
            }
            return redirect()->route('person.sales');
        }
    }

    // 轉單、對拆
    public function change_record($id)
    {
        $sale_changes = SaleChange::where('sale_id', $id)->orderby('id', 'desc')->get();
        $sale_splits = SaleSplit::where('sale_id', $id)->orderby('id', 'desc')->get();
        return view('sale.change_record')->with('sale_changes', $sale_changes)->with('sale_splits', $sale_splits);
    }

    public function change_show($id)
    {
        $users = User::where('status', '0')->get();
        $sources = SaleSource::where('status', 'up')->orderby('seq', 'asc')->get();
        $customers = Customer::get();
        $plans = Plan::where('status', 'up')->get();
        $products = Product::where('status', 'up')->where('category_id', '1')->orderby('seq', 'asc')->orderby('price', 'desc')->get();
        $proms = Prom::where('status', 'up')->orderby('seq', 'asc')->get();
        $data = Sale::where('id', $id)->first();
        $sale_gdpapers = Sale_gdpaper::where('sale_id', $id)->get();
        $sale_proms = Sale_prom::where('sale_id', $id)->get();
        $sale_company = SaleCompanyCommission::where('sale_id', $id)->first();

        $sale_change = SaleChange::where('sale_id', $id)->orderby('id', 'desc')->first();
        $sale_split = SaleSplit::where('sale_id', $id)->orderby('id', 'desc')->first();

        $suits = Suit::where('status', 'up')->get();
        $souvenirs = Prom::where('type', 'D')->where('status', 'up')->orderby('seq', 'asc')->get();
        $sale_souvenirs = SaleSouvenir::where('sale_id', $id)->get();

        return view('sale.change')
            ->with('data', $data)
            ->with('customers', $customers)
            ->with('plans', $plans)
            ->with('products', $products)
            ->with('proms', $proms)
            ->with('sale_proms', $sale_proms)
            ->with('sale_gdpapers', $sale_gdpapers)
            ->with('sources', $sources)
            ->with('sale_company', $sale_company)
            ->with('users', $users)
            ->with('sale_change', $sale_change)
            ->with('sale_split', $sale_split)
            ->with('suits', $suits)
            ->with('souvenirs', $souvenirs)
            ->with('sale_souvenirs', $sale_souvenirs);
    }

    public function change_plan_show($id)
    {
        $users = User::where('status', '0')->get();
        $sources = SaleSource::where('status', 'up')->orderby('seq', 'asc')->get();
        $customers = Customer::get();
        $plans = Plan::where('status', 'up')->get();
        $products = Product::where('status', 'up')->where('category_id', '1')->orderby('seq', 'asc')->orderby('price', 'desc')->get();
        $proms = Prom::where('status', 'up')->orderby('seq', 'asc')->get();
        $data = Sale::where('id', $id)->first();
        $sale_gdpapers = Sale_gdpaper::where('sale_id', $id)->get();
        $sale_proms = Sale_prom::where('sale_id', $id)->get();
        $sale_company = SaleCompanyCommission::where('sale_id', $id)->first();

        return view('sale.change_plan')
            ->with('data', $data)
            ->with('customers', $customers)
            ->with('plans', $plans)
            ->with('products', $products)
            ->with('proms', $proms)
            ->with('sale_proms', $sale_proms)
            ->with('sale_gdpapers', $sale_gdpapers)
            ->with('sources', $sources)
            ->with('sale_company', $sale_company)
            ->with('users', $users);
    }

    public function change_plan_update($id, Request $request)
    {
        $data = Sale::where('id', $id)->first();
        $data->plan_id = $request->new_plan_id;
        $data->pay_price = $request->new_pay_price;
        $data->save();

        $sale_plan = new SalePlan;
        $sale_plan->sale_id = $data->id;
        $sale_plan->plan_id = $request->old_plan_id;
        $sale_plan->new_plan_id = $request->new_plan_id;
        $sale_plan->pay_price = $request->old_pay_price;
        $sale_plan->new_pay_price = $request->new_pay_price;
        $sale_plan->save();

        // 業務單軌跡-更新方案
        $sale_history = new SaleHistory();
        $sale_history->sale_id = $id;
        $sale_history->user_id = Auth::user()->id;
        $sale_history->state = 'update_plan';
        $sale_history->save();

        return redirect()->route('sales', ['status' => 'check']);

        // return view('sale.change_plan')->with('data', $data)
        //                           ->with('customers', $customers)
        //                           ->with('plans', $plans)
        //                           ->with('products', $products)
        //                           ->with('proms', $proms)
        //                           ->with('sale_proms', $sale_proms)
        //                           ->with('sale_gdpapers', $sale_gdpapers)
        //                           ->with('sources',$sources)
        //                           ->with('sale_company',$sale_company)
        //                           ->with('users',$users);
    }

    public function change_update(Request $request, $id)
    {
        $data = Sale::where('id', $id)->first();

        if ($request->check_change == 1) {
            $change_data = new SaleChange;
            $change_data->sale_id = $data->id;
            $change_data->user_id = $request->user_id;
            $change_data->change_user_id = $request->change_user_id;
            $change_data->comm = $request->change_comm;
            $change_data->save();

            // $data->user_id = $request->change_user_id;
            // $data->save();
        }

        if ($request->check_split == 1) {
            $split_data = new SaleSplit();
            $split_data->sale_id = $data->id;
            $split_data->user_id = $request->split_user_id_1;
            $split_data->split_user_id = $request->split_user_id_2;
            $split_data->comm = $request->split_comm;
            $split_data->save();
        }

        return redirect()->route('sales', ['status' => 'check']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_gpt(Request $request, $id)
    {
        $sale = Sale::where('id', $id)->first();
        $sale->sale_on = 'No.' . $request->sale_on;
        $sale->type_list = $request->type_list;
        $sale->sale_date = $request->sale_date;
        $sale->customer_id = $request->cust_name_q;
        $sale->pet_name = $request->pet_name;
        $sale->suit_id = $request->suit_id;
        $sale->kg = $request->kg;
        $sale->variety = $request->variety;
        $sale->type = $request->type;
        if ($request->type_list == 'memorial') {
            $sale->plan_id = '4';
        } else {
            $sale->plan_id = $request->plan_id;
        }
        $sale->plan_price = $request->plan_price;
        $sale->pay_id = $request->pay_id;
        // 尾款或追加為方案價格
        if (isset($request->final_price)) {
            $sale->plan_price = $request->final_price;
        }
        $sale->pay_price = $request->pay_price;
        if ($request->pay_method == 'B' || $request->pay_method == 'C') {
            $sale->cash_price = $request->cash_price;
            $sale->transfer_price = $request->transfer_price;
            $sale->transfer_number = $request->transfer_number;
            $sale->transfer_channel = $request->transfer_channel;
        } else {
            $sale->cash_price = null;
            $sale->transfer_price = null;
            $sale->transfer_number = null;
            $sale->transfer_channel = null;
        }
        if ($request->send == 1) {
            $sale->send = $request->send;
        } else {
            $sale->send = 0;
        }
        if ($request->connector_address == 1) {
            $sale->connector_address = $request->connector_address;
        } else {
            $sale->connector_address = 0;
        }
        if ($request->connector_hospital_address == 1) {
            $sale->hospital_address = $request->hospital_address;
        }
        if ($request->cooperation_price == 1) {
            $sale->cooperation_price = $request->cooperation_price;
        } else {
            $sale->cooperation_price = 0;
        }
        $sale->pay_method = $request->pay_method;
        $sale->total = $request->total;
        $sale->comm = $request->comm;
        // 儲存宗教與往生日期（若前端未顯示則可能為空）
        $sale->religion = $request->religion;
        $sale->religion_other = $request->religion_other;
        $sale->death_date = $request->death_date;
        $sale->save();

        $sale_id = Sale::where('id', $id)->first();

        // 更新重要日期（佛道教且有往生日期時），否則刪除
        if (in_array($sale->religion, ['buddhism', 'taoism', 'buddhism_taoism']) && !empty($sale->death_date)) {
            $dates = MemorialDate::calculateMemorialDates($sale->death_date, $sale->plan_id);
            MemorialDate::updateOrCreate(
                ['sale_id' => $sale_id->id],
                array_merge($dates, [
                    'sale_id' => $sale_id->id,
                    'seventh_reserved' => false,
                    'seventh_reserved_at' => null,
                    'forty_ninth_reserved' => false,
                    'forty_ninth_reserved_at' => null,
                    'hundredth_reserved' => false,
                    'hundredth_reserved_at' => null,
                    'anniversary_reserved' => false,
                    'anniversary_reserved_at' => null,
                    'notes' => null
                ])
            );
        } else {
            MemorialDate::where('sale_id', $sale_id->id)->delete();
        }

        // 清除舊的相關資料
        Sale_prom::where('sale_id', $sale_id->id)->delete();
        SaleAddress::where('sale_id', $sale_id->id)->delete();
        SaleSouvenir::where('sale_id', $sale_id->id)->delete();

        // 要為派件單且支付類別為一次跟訂金
        if ($request->type_list == 'dispatch' && ($request->pay_id == 'A' || $request->pay_id == 'C')) {
            if ($request->send == 1) {
                $SaleAddress = new SaleAddress();
                $SaleAddress->sale_id = $sale_id->id;
                $SaleAddress->send = '1';
                $SaleAddress->save();
            } elseif ($request->connector_address == 1) {
                $SaleAddress = new SaleAddress();
                $SaleAddress->sale_id = $sale_id->id;
                $SaleAddress->county = $request->county;
                $SaleAddress->district = $request->district;
                $SaleAddress->address = $request->address;
                $SaleAddress->save();
            } elseif ($request->connector_hospital_address == 1) {
                $SaleAddress = new SaleAddress();
                $SaleAddress->sale_id = $sale_id->id;
                $SaleAddress->send = '2';
                $cust_data = Customer::where('id', $request->cust_name_q)->first();
                if (isset($cust_data)) {
                    $SaleAddress->county = $cust_data->county;
                    $SaleAddress->district = $cust_data->district;
                    $SaleAddress->address = $cust_data->address;
                }
                $SaleAddress->save();
            } else {
                $cust_data = Customer::where('id', $request->cust_name_q)->first();
                if (isset($cust_data)) {
                    $SaleAddress = new SaleAddress();
                    $SaleAddress->sale_id = $sale_id->id;
                    $SaleAddress->county = $cust_data->county;
                    $SaleAddress->district = $cust_data->district;
                    $SaleAddress->address = $cust_data->address;
                    $SaleAddress->save();
                }
            }
        }

        // 處理後續處理項目
        if (isset($request->select_proms)) {
            foreach ($request->select_proms as $key => $select_prom) {
                if (isset($select_prom)) {  // 不等於空的話
                    $prom = new Sale_prom();
                    $prom->prom_type = $request->select_proms[$key];
                    $prom->sale_id = $sale_id->id;
                    $prom->prom_id = $request->prom[$key];
                    $prom->prom_total = $request->prom_total[$key];
                    $prom->comment = $request->prom_extra_text[$key] ?? null;
                    $prom->save();

                    // 如果有紀念品資料，這裡直接建立
                    if (
                        isset($request->product_souvenir_types[$key]) ||
                        isset($request->product_proms[$key])
                    ) {
                        $souvenir = new SaleSouvenir();
                        $souvenir->sale_id = $sale_id->id;
                        $souvenir->sale_prom_id = $prom->id;  // 關鍵：這裡用剛剛新增的 Sale_prom id
                        $souvenir->souvenir_type = $request->product_souvenir_types[$key];
                        if (isset($request->product_proms[$key])) {
                            $souvenir->product_name = $request->product_proms[$key];
                        } else {
                            $souvenir->product_name = $request->product_name[$key];
                        }
                        $souvenir->product_num = $request->product_num[$key];

                        // 處理細項 ID
                        if (isset($request->product_variants[$key]) && !empty($request->product_variants[$key])) {
                            $souvenir->product_variant_id = $request->product_variants[$key];
                        }

                        $souvenir->total = $request->prom_total[$key];
                        $souvenir->comment = $request->product_comment[$key];
                        $souvenir->save();
                    }
                }
            }
        }

        // 處理來源公司佣金
        if ($request->source_company_name_q == null) {
            // 如果是null，刪除舊的記錄
            SaleCompanyCommission::where('sale_id', $id)->delete();
        } else {
            // 不是null，更新或新增記錄
            $sale_company = SaleCompanyCommission::where('sale_id', $id)->first();
            if (isset($sale_company)) {
                // 更新現有記錄
                $sale_company->sale_date = $request->sale_date;
                $sale_company->type = $request->type;
                $sale_company->customer_id = $request->cust_name_q;
                $sale_company->company_id = $request->source_company_name_q;
                $sale_company->plan_price = $request->plan_price;
                if ($request->cooperation_price != 1) {
                    if ($request->plan_price / 2 > 2500) {
                        $sale_company->commission = 2500;
                    } else {
                        $sale_company->commission = $request->plan_price / 2;
                    }
                } else {
                    $sale_company->commission = $request->plan_price;
                    $sale_company->cooperation_price = $request->cooperation_price;
                }
                $sale_company->save();
            } else {
                // 新增記錄
                $CompanyCommission = new SaleCompanyCommission();
                $CompanyCommission->sale_date = $request->sale_date;
                $CompanyCommission->type = $request->type;
                $CompanyCommission->customer_id = $request->cust_name_q;
                $CompanyCommission->sale_id = $sale_id->id;
                $CompanyCommission->company_id = $request->source_company_name_q;
                $CompanyCommission->plan_price = $request->plan_price;
                if ($request->cooperation_price != 1) {
                    if ($request->plan_price / 2 > 2500) {
                        $CompanyCommission->commission = 2500;
                    } else {
                        $CompanyCommission->commission = $request->plan_price / 2;
                    }
                } else {
                    $CompanyCommission->commission = $request->plan_price;
                    $CompanyCommission->cooperation_price = $request->cooperation_price;
                }
                $CompanyCommission->save();
            }
        }

        // 處理金紙選購
        Sale_gdpaper::where('sale_id', $sale_id->id)->delete();
        if (isset($request->gdpaper_ids)) {
            foreach ($request->gdpaper_ids as $key => $gdpaper_id) {
                if (isset($gdpaper_id)) {
                    $gdpaper = new Sale_gdpaper();
                    $gdpaper->sale_id = $sale_id->id;
                    $gdpaper->type_list = $request->type_list;
                    $gdpaper->gdpaper_id = $request->gdpaper_ids[$key];
                    $gdpaper->gdpaper_num = $request->gdpaper_num[$key];
                    $gdpaper->gdpaper_total = $request->gdpaper_total[$key];
                    $gdpaper->save();
                }
            }
        }

        // 業務單軌跡-更新
        $sale_history = new SaleHistory();
        $sale_history->sale_id = $sale_id->id;
        $sale_history->user_id = Auth::user()->id;
        $sale_history->state = 'update';
        $sale_history->save();

        return redirect()->route('sales');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete_gpt($id)
    {
        $sources = SaleSource::where('status', 'up')->orderby('seq', 'asc')->get();
        $plans = Plan::where('status', 'up')->get();
        $products = Product::where('status', 'up')->where('category_id', '1')->orderby('seq', 'asc')->orderby('price', 'desc')->get();
        $customers = Customer::orderby('created_at', 'desc')->get();
        $source_companys = Customer::whereIn('group_id', [2, 3, 4, 5, 6, 7])->get();
        $suits = Suit::where('status', 'up')->get();
        $souvenir_types = SouvenirType::where('status', 'up')->get();
        $proms = Prom::where('status', 'up')->get();
        $hospitals = Customer::whereIn('group_id', [2])->get();
        $data = Sale::where('id', $id)->first();

        if (!$data) {
            return redirect()->route('sales')->with('error', '找不到指定的業務單');
        }

        // 載入相關資料
        $sale_proms = Sale_prom::where('sale_id', $id)->get();
        $sale_gdpapers = Sale_gdpaper::where('sale_id', $id)->get();
        $sale_souvenirs = SaleSouvenir::where('sale_id', $id)->get();

        // 載入每個後續處理項目對應的紀念品資料
        foreach ($sale_proms as $sale_prom) {
            $sale_prom->souvenir_data = SaleSouvenir::where('sale_id', $id)
                ->where('sale_prom_id', $sale_prom->id)
                ->first();

            // 判斷商品類型
            if ($sale_prom->souvenir_data) {
                if ($sale_prom->souvenir_data->souvenir_type == null) {
                    // 關聯商品：souvenir_type 為 null
                    $sale_prom->is_custom_product = false;
                    $sale_prom->product_id = $sale_prom->souvenir_data->product_name;  // product_name 存的是商品ID
                    $sale_prom->variant_id = $sale_prom->souvenir_data->product_variant_id;
                } else {
                    // 自訂商品：souvenir_type 不為 null
                    $sale_prom->is_custom_product = true;
                    $sale_prom->product_name = $sale_prom->souvenir_data->product_name;  // product_name 存的是商品名稱
                    $sale_prom->souvenir_type_id = $sale_prom->souvenir_data->souvenir_type;
                }
            }
        }

        // 載入來源公司資料
        $sale_company = SaleCompanyCommission::where('sale_id', $id)->first();

        // 載入接體地址資料
        $sale_address = SaleAddress::where('sale_id', $id)->first();

        // 載入紀念品資料
        $souvenirs = Souvenir::where('status', 'up')->get();

        return view('sale.del_gpt')
            ->with('data', $data)
            ->with('products', $products)
            ->with('sources', $sources)
            ->with('plans', $plans)
            ->with('customers', $customers)
            ->with('source_companys', $source_companys)
            ->with('suits', $suits)
            ->with('souvenir_types', $souvenir_types)
            ->with('proms', $proms)
            ->with('hospitals', $hospitals)
            ->with('sale_proms', $sale_proms)
            ->with('sale_gdpapers', $sale_gdpapers)
            ->with('sale_souvenirs', $sale_souvenirs)
            ->with('sale_company', $sale_company)
            ->with('sale_address', $sale_address)
            ->with('souvenirs', $souvenirs);
    }

    public function delete($id)
    {
        $souvenir_types = SouvenirType::where('status', 'up')->get();
        $source_companys = Customer::whereIn('group_id', [2, 3, 4, 5, 6, 7])->get();
        $sources = SaleSource::where('status', 'up')->orderby('seq', 'asc')->get();
        $customers = Customer::get();
        $plans = Plan::where('status', 'up')->get();
        $products = Product::where('status', 'up')->where('category_id', '1')->orderby('seq', 'asc')->orderby('price', 'desc')->get();
        $proms = Prom::where('status', 'up')->orderby('seq', 'asc')->get();
        $data = Sale::where('id', $id)->first();
        $sale_gdpapers = Sale_gdpaper::where('sale_id', $id)->get();
        $sale_proms = Sale_prom::where('sale_id', $id)->get();
        $sale_company = SaleCompanyCommission::where('sale_id', $id)->first();
        $sale_address = SaleAddress::where('sale_id', $id)->first();
        $suits = Suit::where('status', 'up')->get();
        $souvenirs = Prom::where('type', 'D')->where('status', 'up')->orderby('seq', 'asc')->get();
        $sale_souvenirs = SaleSouvenir::where('sale_id', $id)->first();

        return view('sale.del')
            ->with('data', $data)
            ->with('customers', $customers)
            ->with('plans', $plans)
            ->with('products', $products)
            ->with('proms', $proms)
            ->with('sale_proms', $sale_proms)
            ->with('sale_gdpapers', $sale_gdpapers)
            ->with('sources', $sources)
            ->with('sale_company', $sale_company)
            ->with('sale_address', $sale_address)
            ->with('source_companys', $source_companys)
            ->with('suits', $suits)
            ->with('souvenirs', $souvenirs)
            ->with('sale_souvenirs', $sale_souvenirs)
            ->with('souvenir_types', $souvenir_types);
    }

    public function destroy_gpt($id)
    {
        // 檢查業務單是否存在
        $sale = Sale::where('id', $id)->first();
        if (!$sale) {
            return redirect()->route('sales')->with('error', '找不到指定的業務單');
        }

        try {
            DB::beginTransaction();

            // 刪除相關資料
            Sale_gdpaper::where('sale_id', $id)->delete();
            Sale_prom::where('sale_id', $id)->delete();
            SaleCompanyCommission::where('sale_id', $id)->delete();
            SaleAddress::where('sale_id', $id)->delete();
            SaleSouvenir::where('sale_id', $id)->delete();
            MemorialDate::where('sale_id', $id)->delete();

            // 最後刪除業務單
            $sale->delete();

            DB::commit();

            // 業務單軌跡-刪除
            $sale_history = new SaleHistory();
            $sale_history->sale_id = $id;
            $sale_history->user_id = Auth::user()->id;
            $sale_history->state = 'delete';
            $sale_history->save();

            return redirect()->route('sales')->with('success', '業務單已成功刪除');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('sales')->with('error', '刪除失敗：' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $sale = Sale::where('id', $id);
        $sale_gdpapers = Sale_gdpaper::where('sale_id', $id);
        $sale_promBs = Sale_prom::where('sale_id', $id);
        $sale_company = SaleCompanyCommission::where('sale_id', $id);
        $sale_address = SaleAddress::where('sale_id', $id);
        $sale_souvenirs = SaleSouvenir::where('sale_id', $id);

        $sale->delete();
        $sale_gdpapers->delete();
        $sale_promBs->delete();
        $sale_company->delete();
        $sale_address->delete();
        $sale_souvenirs->delete();

        return redirect()->route('sales');
    }

    public function checkHistory(Request $request)
    {
        $years = range(Carbon::now()->year, 2022);
        if ($request->input() == null) {
            $search_year = $request->year;
            $search_month = $request->month;
            $firstDay = Carbon::today();
            $lastDay = Carbon::today();
        } else {
            $firstDay = $request->after_date;
            $lastDay = $request->before_date;
        }

        $sales = Sale::where('status', 9)->where('sale_date', '>=', $firstDay)->where('sale_date', '<=', $lastDay);

        $payItems = PayItem::leftJoin('pay_data', 'pay_item.pay_data_id', '=', 'pay_data.id')
            ->leftJoin('users', 'pay_data.user_id', '=', 'users.id')
            ->whereNotIn('users.job_id', [1, 2, 7])  // 不抓老闆、工程師、行政經理
            ->where('pay_item.status', 1)
            ->where('pay_item.pay_date', '>=', $firstDay)
            ->where('pay_item.pay_date', '<=', $lastDay)
            ->get();

        $check_user_id = $request->check_user_id;
        if ($check_user_id != 'null') {
            if (isset($check_user_id)) {
                $sales = $sales->where('check_user_id', $check_user_id);
            } else {
                $sales = $sales;
            }
        }
        $sales = $sales->orderby('sale_date', 'desc')->get();
        // dd($sales);
        $users = User::where('status', '0')->whereIn('job_id', [2, 3, 5, 10])->get();
        $sums = ['count' => 0, 'price' => 0, 'pay_price' => 0, 'cash_total' => 0, 'transfer_total' => 0, 'pay_count' => 0, 'actual_price' => 0];
        $datas = [];

        foreach ($sales as $key => $sale) {
            $item_sales = Sale::where('status', 9)->where('sale_date', '>=', $firstDay)->where('sale_date', '<=', $lastDay);
            if ($check_user_id != 'null') {
                if (isset($check_user_id)) {
                    $item_sales = $item_sales->where('check_user_id', $check_user_id);
                } else {
                    $item_sales = $item_sales;
                }
            }
            $datas[$sale->user_id]['name'] = $sale->user_name->name;
            $datas[$sale->user_id]['items'] = $item_sales->where('user_id', $sale->user_id)->orderby('sale_date', 'desc')->orderby('user_id', 'desc')->orderby('sale_on', 'desc')->get();
            $datas[$sale->user_id]['count'] = $item_sales->count();
            $datas[$sale->user_id]['price'] = $item_sales->sum('pay_price');

            // 計算付款方式為現金的總金額
            $cash_total = Sale::where('status', 9)
                ->where('sale_date', $sale->sale_date)
                ->where('pay_method', 'A')
                ->where('user_id', $sale->user_id)
                ->sum('pay_price');

            $transfer_total = Sale::where('status', 9)
                ->where('sale_date', $sale->sale_date)
                ->where('pay_method', 'B')
                ->where('user_id', $sale->user_id)
                ->sum('pay_price');

            $cash_transfer_cash_total = Sale::where('status', 9)
                ->where('sale_date', $sale->sale_date)
                ->where('pay_method', 'C')
                ->where('user_id', $sale->user_id)
                ->sum('cash_price');

            $cash_transfer_transfer_total = Sale::where('status', 9)
                ->where('sale_date', $sale->sale_date)
                ->where('pay_method', 'C')
                ->where('user_id', $sale->user_id)
                ->sum('transfer_price');

            $datas[$sale->user_id]['cash_total'] = $cash_total + $cash_transfer_cash_total;
            $datas[$sale->user_id]['transfer_total'] = $transfer_total + $cash_transfer_transfer_total;

            if (!isset($datas[$sale->user_id]['pay_count'])) {
                $datas[$sale->user_id]['pay_count'] = 0;
            }
            if (!isset($datas[$sale->user_id]['pay_price'])) {
                $datas[$sale->user_id]['pay_price'] = 0;
            }
        }

        foreach ($payItems as $key => $payItem) {
            $datas[$payItem->pay_data->user_id]['name'] = $payItem->pay_data->user_name->name;
            $datas[$payItem->pay_data->user_id]['pay_items'] = PayItem::leftJoin('pay_data', 'pay_item.pay_data_id', '=', 'pay_data.id')
                ->leftJoin('pay', 'pay_item.pay_id', '=', 'pay.id')
                ->where('pay_data.user_id', $payItem->pay_data->user_id)
                ->where('pay_item.pay_date', '>=', $firstDay)
                ->where('pay_item.pay_date', '<=', $lastDay)
                ->select('pay_item.*', 'pay.name as pay_name', 'pay_data.pay_date as pay_data_date', 'pay_data.pay_on as pay_on')
                ->get();
            $datas[$payItem->pay_data->user_id]['pay_count'] = $datas[$payItem->pay_data->user_id]['pay_items']->count();
            $datas[$payItem->pay_data->user_id]['pay_price'] = $datas[$payItem->pay_data->user_id]['pay_items']->sum('price');

            if (!isset($datas[$payItem->pay_data->user_id]['count'])) {
                $datas[$payItem->pay_data->user_id]['count'] = 0;
            }
            if (!isset($datas[$payItem->pay_data->user_id]['price'])) {
                $datas[$payItem->pay_data->user_id]['price'] = 0;
            }
            if (!isset($datas[$payItem->pay_data->user_id]['cash_total'])) {
                $datas[$payItem->pay_data->user_id]['cash_total'] = 0;
            }
            if (!isset($datas[$payItem->pay_data->user_id]['transfer_total'])) {
                $datas[$payItem->pay_data->user_id]['transfer_total'] = 0;
            }
        }

        $sums['actual_price'] = 0;
        $sums['cash_actual_price'] = 0;
        // 使用 foreach 遍歷 $datas 並累計到 $sums
        foreach ($datas as $date => &$data) {
            if (isset($data['count'])) {
                $sums['count'] += $data['count'];
            }
            if (isset($data['price'])) {
                $sums['price'] += $data['price'];
            }
            // 現金
            if (isset($data['cash_total'])) {
                $sums['cash_total'] += $data['cash_total'];
            }
            // 轉帳
            if (isset($data['transfer_total'])) {
                $sums['transfer_total'] += $data['transfer_total'];
            }
            // 加上支出資料的統計
            if (isset($data['pay_count'])) {
                $sums['pay_count'] += $data['pay_count'];
            }
            if (isset($data['pay_price'])) {
                $sums['price'] += $data['pay_price'];
            }
            if (isset($data['pay_price'])) {
                $sums['pay_price'] += $data['pay_price'];
            }
            // 計算實際收入（業務收入 - 支出）
            $datas[$date]['cash_actual_price'] = ($data['cash_total'] ?? 0) - ($data['pay_price'] ?? 0);
            $datas[$date]['actual_price'] = ($data['price'] ?? 0) - ($data['pay_price'] ?? 0);
            if (isset($data['actual_price'])) {
                $sums['actual_price'] += $data['actual_price'];
            } else {
                $sums['actual_price'] = 0;
            }
        }

        $check_users = User::where('status', '0')->whereIn('job_id', [1, 2, 7, 8, 9])->orderby('seq')->get();
        // dd($datas);
        return view('sale.check_history')
            ->with('sales', $sales)
            ->with('years', $years)
            ->with('users', $users)
            ->with('firstDay', $firstDay)
            ->with('lastDay', $lastDay)
            ->with('request', $request)
            ->with('sums', $sums)
            ->with('datas', $datas)
            ->with('check_users', $check_users);
    }

    // 匯出
    public function export(Request $request)
    {
        // 獲取選擇的欄位
        $selectedFields = $request->input('export_fields', []);

        // 如果沒有選擇欄位，使用預設欄位
        if (empty($selectedFields)) {
            $selectedFields = [
                '案件單類別', '單號', '專員', '日期', '客戶', '寶貝名',
                '寵物品種', '公斤數', '方案', '方案價格', '案件來源', '套裝', '金紙', '金紙總賣價',
                '安葬方式', '後續處理', '其他處理', '紀念品', '付款方式',
                '實收價格', '狀態', '備註', '更改後方案',
                '確認對帳人員', '確認對帳時間'
            ];
        }

        if ($request->input() != null) {
            $status = $request->status;
            if (!isset($status) || $status == 'not_check') {
                $sales = Sale::whereIn('status', [1, 2]);
            }
            if ($status == 'check') {
                $sales = Sale::whereIn('status', [9, 100]);
            }
            $type_list = $request->type_list;
            if ($type_list != 'null') {
                if (isset($type_list)) {
                    $sales = $sales->where('type_list', $type_list);
                } else {
                    $sales = $sales;
                }
            }

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
                $sale_on = '%' . $request->sale_on . '%';
                $sales = $sales->where('sale_on', 'like', $sale_on);
            }
            $cust_mobile = $request->cust_mobile;

            if ($cust_mobile) {
                $cust_mobile = $request->cust_mobile . '%';
                $customers = Customer::where('mobile', 'like', $cust_mobile)->get();
                foreach ($customers as $customer) {
                    $customer_ids[] = $customer->id;
                }
                if (isset($customer_ids)) {
                    $sales = $sales->whereIn('customer_id', $customer_ids);
                } else {
                    $sales = $sales;
                }
            }

            $pet_name = $request->pet_name;
            if ($pet_name) {
                $pet_name = $request->pet_name . '%';
                $sales = $sales->where('pet_name', 'like', $pet_name);
            }

            $user = $request->user;
            if ($user != 'null') {
                if (isset($user)) {
                    $sales = $sales->where('user_id', $user);
                } else {
                    $sales = $sales;
                }
            }

            $plan = $request->plan;
            if ($plan != 'null') {
                if (isset($plan)) {
                    $sales = $sales->where('plan_id', $plan);
                } else {
                    $sales = $sales;
                }
            }

            $pay_id = $request->pay_id;
            if ($pay_id) {
                if ($pay_id == 'A') {
                    $sales = $sales->whereIn('pay_id', ['A', 'B']);
                } else {
                    $sales = $sales->where('pay_id', $pay_id);
                }
            }
            $other = $request->other;
            if (isset($after_date)) {
                $other_after_date = $after_date . ' 00:00:00';
            }
            if (isset($before_date)) {
                $other_before_date = $before_date . ' 11:59:59';
            }

            $check_user_id = $request->check_user_id;
            if ($check_user_id != 'null') {
                if (isset($check_user_id)) {
                    $sales = $sales->where('check_user_id', $check_user_id);
                } else {
                    $sales = $sales;
                }
            }

            // dd($sales);

            $other = $request->other;
            if ($other == 'change') {
                if (!isset($sale_change_ids)) {
                    $sale_change_ids = [];
                }
                if (isset($other_after_date)) {
                    $sale_changes = SaleChange::where('updated_at', '>=', $other_after_date)->get();
                } elseif (isset($other_before_date)) {
                    $sale_changes = SaleChange::where('updated_at', '<=', $other_before_date)->get();
                } elseif (isset($other_after_date) && isset($other_before_date)) {
                    $sale_changes = SaleChange::where('updated_at', '>=', $other_after_date)->where('updated_at', '<=', $other_before_date)->get();
                } else {
                    $sale_changes = SaleChange::get();
                }
                foreach ($sale_changes as $sale_change) {
                    $sale_change_ids[] = $sale_change->sale_id;
                }
                if (empty($sale_change_ids)) {
                    $sales = $sales->whereIn('id', $sale_change_ids);
                }
                $sales = $sales->whereIn('id', $sale_change_ids);
            } elseif ($other == 'split') {
                if (!isset($sale_split_ids)) {
                    $sale_split_ids = [];
                }
                if (isset($other_after_date)) {
                    $sale_splits = SaleSplit::where('updated_at', '>=', $other_after_date)->get();
                } elseif (isset($other_before_date)) {
                    $sale_splits = SaleSplit::where('updated_at', '<=', $other_before_date)->get();
                } elseif (isset($other_after_date) && isset($other_before_date)) {
                    $sale_splits = SaleSplit::where('updated_at', '>=', $other_after_date)->where('updated_at', '<=', $other_before_date)->get();
                } else {
                    $sale_splits = SaleSplit::get();
                }
                foreach ($sale_splits as $sale_split) {
                    $sale_split_ids[] = $sale_split->sale_id;
                }
                if (empty($sale_split_ids)) {
                    $sales = $sales->whereIn('id', $sale_split_ids);
                }
            }

            $sales = $sales->orderby('sale_date', 'desc')->orderby('user_id', 'desc')->orderby('sale_on', 'desc')->get();
        } else {
            $after_date = '';
            $before_date = '';
            $sales = [];
        }

        $fileName = '專員業務key單' . date('Y-m-d') . '.csv';

        $headers = array(
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        );
        $header = array('日期', $after_date . '~', $before_date);

        $callback = function () use ($sales, $selectedFields, $header) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF), 3);
            fputcsv($file, $header);
            fputcsv($file, $selectedFields);

            foreach ($sales as $key => $sale) {
                $row = [];

                // 根據選擇的欄位生成資料
                foreach ($selectedFields as $field) {
                    $row[$field] = $this->getFieldValue($sale, $field);
                }

                fputcsv($file, array_values($row));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getFieldValue($sale, $field)
    {
        switch ($field) {
            case '案件單類別':
                return $sale->type_list == 'dispatch' ? '派件單' : ($sale->type_list == 'memorial' ? '追思單' : '報廢單');
            case '單號':
                return $sale->sale_on;
            case '專員':
                return $sale->user_name->name ?? '';
            case '日期':
                return $sale->sale_date;
            case '客戶':
                if (isset($sale->customer_id)) {
                    if (isset($sale->cust_name)) {
                        return $sale->cust_name->name;
                    } else {
                        return $sale->customer_id . '（客戶姓名須重新登入）';
                    }
                } elseif ($sale->type_list == 'memorial') {
                    return '追思';
                }
                return '';
            case '寶貝名':
                return isset($sale->pet_name) ? '="' . $sale->pet_name . '"' : '';
            case '寵物品種':
                return isset($sale->variety) ? '="' . $sale->variety . '"' : '';
            case '公斤數':
                return isset($sale->kg) ? '="' . $sale->kg . '"' : '';
            case '方案':
                if (isset($sale->plan_id)) {
                    if (isset($sale->plan_name)) {
                        return $sale->plan_name->name;
                    }
                }
                return '';
            case '方案價格':
                if (isset($sale->plan_price)) {
                    return number_format($sale->plan_price);
                }
                return '';
            case '案件來源':
                if (isset($sale->type)) {
                    if (isset($sale->source_type)) {
                        return $sale->source_type->name;
                    } else {
                        return $sale->type;
                    }
                }
                return '';
            case '來源名稱':
                if (isset($sale->sale_company_commission)) {
                    if ($sale->sale_company_commission->type == 'self') {
                        return $sale->sale_company_commission->self_name->name;
                    } else {
                        return $sale->sale_company_commission->company_name->name;
                    }
                }
                return '';
            case '套裝':
                if (isset($sale->suit_id)) {
                    if (isset($sale->suit_name)) {
                        return $sale->suit_name->name;
                    }
                }
                return '';
            case '金紙':
                $gdpaper_text = '';
                foreach ($sale->gdpapers as $gdpaper) {
                    if (isset($gdpaper->gdpaper_id)) {
                        if (isset($gdpaper->gdpaper_name)) {
                            $gdpaper_text .= ($gdpaper_text == '' ? '' : "\r\n") . $gdpaper->gdpaper_name->name . ' ' . $gdpaper->gdpaper_num . '份';
                        }
                    } else {
                        $gdpaper_text = '無';
                    }
                }
                return $gdpaper_text;
            case '金紙總賣價':
                $total = 0;
                foreach ($sale->gdpapers as $gdpaper) {
                    if (isset($gdpaper->gdpaper_id)) {
                        $total += $gdpaper->gdpaper_total;
                    }
                }
                return $total;
            case '安葬方式':
                $text = '';
                if (isset($sale->before_prom_id)) {
                    if (isset($sale->PromA_name)) {
                        $text = $sale->PromA_name->name . '-' . $sale->before_prom_price;
                    }
                }
                foreach ($sale->proms as $prom) {
                    if ($prom->prom_type == 'A') {
                        if (isset($prom->prom_id)) {
                            $text .= ($text == '' ? '' : "\r\n") . $prom->prom_name->name . '-' . number_format($prom->prom_total);
                        } else {
                            $text = '無';
                        }
                    }
                }
                return $text;
            case '後續處理':
                $text = '';
                foreach ($sale->proms as $prom) {
                    if ($prom->prom_type == 'B') {
                        if (isset($prom->prom_id)) {
                            $text .= ($text == '' ? '' : "\r\n") . $prom->prom_name->name . '-' . number_format($prom->prom_total);
                        } else {
                            $text = '無';
                        }
                    }
                }
                return $text;
            case '其他處理':
                $text = '';
                foreach ($sale->proms as $prom) {
                    if ($prom->prom_type == 'C') {
                        if (isset($prom->prom_id)) {
                            $text .= ($text == '' ? '' : "\r\n") . $prom->prom_name->name . '-' . number_format($prom->prom_total);
                        } else {
                            $text = '無';
                        }
                    }
                }
                return $text;
            case '紀念品':
                $text = '';
                $sale_souvenirs = SaleSouvenir::where('sale_id', $sale->id)->get();
                if ($sale_souvenirs->count() > 0) {
                    foreach ($sale_souvenirs as $souvenir) {
                        $itemText = '';

                        if ($souvenir->souvenir_type != null) {
                            // 自訂商品：【type】-「紀念品品名」x「紀念品數量」（備註：「紀念品備註」）
                            // 取得類型名稱
                            $souvenirType = SouvenirType::find($souvenir->souvenir_type);
                            $typeName = $souvenirType ? $souvenirType->name : '無類型';

                            // 取得品名
                            $productName = $souvenir->product_name ?? '無';

                            // 取得數量
                            $quantity = $souvenir->product_num ?? '1';

                            // 組合格式：【type】-「品名」x「數量」
                            $itemText = '【' . $typeName . '】-' . $productName . 'x' . $quantity;
                        } else {
                            // 關聯商品：「紀念品品名」-「紀念品細項」x「紀念品數量」（備註：「紀念品備註」）
                            // 取得品名
                            $product = Product::find($souvenir->product_name);
                            $productName = $product ? $product->name : '無';

                            // 取得細項
                            $variantName = '';
                            if ($souvenir->product_variant_id) {
                                $variant = ProductVariant::find($souvenir->product_variant_id);
                                $variantName = $variant ? '-' . $variant->variant_name : '';
                            }

                            // 取得數量
                            $quantity = $souvenir->product_num ?? '1';

                            // 組合格式：「品名」-「細項」x「數量」
                            $itemText = $productName . $variantName . 'x' . $quantity;
                        }

                        // 取得備註（兩種類型都要顯示）
                        $comment = $souvenir->comment ?? '';
                        if (!empty($comment)) {
                            $itemText .= '（備註：' . $comment . '）';
                        }

                        $text .= ($text == '' ? '' : "\r\n") . $itemText;
                    }
                } else {
                    $text = '無';
                }
                return $text;
            case '付款類別':
                return isset($sale->pay_id) ? $sale->pay_type() : '';
            case '支付方式':
                return isset($sale->pay_method) ? $sale->pay_method() : '';
            case '實收價格':
                return number_format($sale->pay_price);
            case '狀態':
                return $sale->status();
            case '親送':
                return $sale->send == 1 ? '是' : '否';
            case '接體地址不為客戶地址':
                return $sale->connector_address == 1 ? $sale->connector_address_data->county . $sale->connector_address_data->district . $sale->connector_address_data->address : '';
            case '接體為醫院':
                return isset($sale->hospital_address) ? $sale->hospital_address_name->name : '';
            case '備註':
                return isset($sale->comm) ? $sale->comm : '';
            case '更改後方案':
                if (isset($sale->change_plan)) {
                    return '由「' . $sale->change_plan->old_plan_data->name . '」改為' . '「' . $sale->change_plan->new_plan_data->name . '」';
                }
                return '';
            case '確認對帳人員':
                return isset($sale->check_user_id) ? $sale->check_user_name->name : '';
            case '確認對帳時間':
                return isset($sale->check_user_id) ? $sale->updated_at : '';
            default:
                return '';
        }
    }

    public function history($id)
    {
        $sale = Sale::where('id', $id)->first();
        $datas = SaleHistory::where('sale_id', $id)->get();
        return view('sale.history')->with('sale', $sale)->with('datas', $datas);
    }
}

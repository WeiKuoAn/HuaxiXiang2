<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Gdpaper;
use App\Models\Plan;
use App\Models\PromB;
use App\Models\PromA;
use App\Models\Sale_gdpaper;
use App\Models\Sale_promB;
use App\Models\Sale;
use App\Models\User;
use App\Models\CustGroup;
use App\Models\PujaData;
use App\Models\Contract;
use App\Models\Lamp;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /*ajax*/
    public function customer(Request $request)
    {
        if ($request->ajax()) {
            $output = "";
            $custs = Customer::where('name', 'like', $request->cust_name . '%')->get();

            if ($custs) {
                foreach ($custs as $key => $cust) {
                    $output .=  '<option value="' . $cust->id . '" label="(' . $cust->name . ')-' . $cust->mobile . '">';
                }
            }
            return Response($output);
        }
    }

    public function customer_data(Request $request)
    {
        if ($request->ajax()) {
            $output = "";
            $cust = Customer::where('id',  $request->cust_id)->first();

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
        //縣市下拉市
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
        //結束
        if ($request) {
            $query = Customer::query(); // Start building the query

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
                $customer_ids = Sale::where('pet_name', 'like', '%' . $request->pet_name . '%')
                    ->pluck('customer_id');
                if ($customer_ids->isNotEmpty()) {
                    $query->whereIn('id', $customer_ids);
                } else {
                    $query->whereNull('id'); // Ensure no results if no sales match
                }
            }

            if ($county != "null") {
                if (isset($county)) {
                    $query = $query->where('county', $county);
                } else {
                    $query = $query;
                }
            }
            $district = $request->district;
            if ($district != "null") {
                if (isset($district)) {
                    $query = $query->where('district', $district);
                } else {
                    $query = $query;
                }
            }

            $address = $request->address;
            if ($district != "null") {
                $address = '%' . $request->address . '%';
                if (isset($address)) {
                    $query = $query->where('address', 'like', $address);
                } else {
                    $query = $query;
                }
            }

            $customers = $query->paginate(30);
            $condition = $request->all();
        } else {
            $customers = collect(); // Return an empty collection if no request
            $condition = '';
        }

        return view('customer.customers')->with('customers', $customers)
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
            $sales = Sale::where('customer_id',  $id)->whereIn('status', [9, 100]);

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
            if ($user != "null") {
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
        return view('cust_sale')->with('sales', $sales)
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
        $data = Customer::where('mobile', $request->mobile)->first();
        // dd($request->not_mobile);
        if ($request->not_mobile == '0' || $request->not_mobile == null) {
            if (isset($data)) {
                return view('customer.create')->with('groups', $groups)->with(['hint' => '1']);
            } else {
                $customer = new Customer;
                $customer->name = $request->name;
                if ($request->not_mobile == 1) {
                    $customer->mobile = '未提供電話';
                } else {
                    $customer->mobile = $request->mobile;
                }
                $customer->county = $request->county;
                $customer->district = $request->district;
                if ($request->not_address == 1) {
                    $customer->address  = '未提供地址';
                } else {
                    $customer->address = $request->address;
                }
                $customer->group_id = 1;
                $customer->created_up = Auth::user()->id;
                $customer->save();
                return redirect()->route('customer');
            }
        } else {
            $customer = new Customer;
            $customer->name = $request->name;
            if ($request->not_mobile == 1) {
                $customer->mobile = '未提供電話';
            } else {
                $customer->mobile = $request->mobile;
            }
            $customer->county = $request->county;
            $customer->district = $request->district;
            if ($request->not_address == 1) {
                $customer->address  = '未提供地址';
            } else {
                $customer->address = $request->address;
            }
            $customer->group_id = 1;
            $customer->created_up = Auth::user()->id;
            $customer->save();
            return redirect()->route('customer');
        }
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
        $customer = Customer::where('id', $id)->first();
        return view('customer.detail')->with('customer', $customer)->with('groups', $groups);
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
    public function update(Request $request, $id)
    {
        $customer = Customer::where('id', $id)->first();
        $customer->name = $request->name;
        $customer->mobile = $request->mobile;
        $customer->county = $request->county;
        $customer->district = $request->district;
        $customer->address = $request->address;
        if (isset($customer->group_id)) {
            $customer->group_id = $request->group_id;
        } else {
            $customer->group_id = 1;
        }
        $customer->save();
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
        $customer = Customer::where('id', $id)->first();
        return view('customer.del')->with('customer', $customer)->with('groups', $groups);
    }

    public function destroy($id)
    {
        $customer = Customer::where('id', $id)->first();
        $customer->delete();
        return redirect()->route('customer');
    }

    public function sales($id)
    {
        $sales = Sale::whereIn('status', ['9', '100'])->where('customer_id', $id)->orderby('sale_date', 'desc')->get();
        $customer = Customer::where('id', $id)->first();
        $contract_datas = Contract::where('customer_id', $id)->orderby('start_date','desc')->get();
        $lamp_datas = Lamp::where('customer_id', $id)->orderby('start_date','desc')->get();
        $puja_datas = PujaData::where('status','1')->where('customer_id', $id)->orderby('date', 'desc')->get();
        return view('customer.sales')->with('sales', $sales)
                                    ->with('customer', $customer)
                                    ->with('contract_datas', $contract_datas)
                                    ->with('lamp_datas', $lamp_datas)
                                    ->with('puja_datas', $puja_datas);
    }

    public function export(Request $request)
    {
        $customers = Customer::get();
        $customer_groups = CustGroup::get();
        if ($request) {
            $county = $request->county;
            $district = $request->district;
            $query = Customer::query(); // Start building the query

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
                    $query->whereNull('id'); // Ensure no results if no sales match
                }
            }

            if ($county != "null") {
                if (isset($county)) {
                    $query = $query->where('county', $county);
                } else {
                    $query = $query;
                }
            }
            $district = $request->district;
            if ($district != "null") {
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

            $customers = $query->paginate(30);
        }
        $fileName = '客戶資料匯出' . date("Y-m-d") . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        // $header = array('日期', $after_date.'~' ,  $before_date);
        $columns = array('編號', '姓名', '電話', '寶貝名稱', '地址', '群組', '新增時間');

        $callback = function () use ($customers, $columns) {

            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF), 3);
            fputcsv($file, $columns);

            foreach ($customers as $key => $customer) {
                $row['編號']  = $key + 1;
                $row['姓名']  = $customer->name;
                $row['電話']  = $customer->mobile;
                $row['寶貝名稱'] = '';
                if (isset($customer->sale_datas)) {
                    foreach ($customer->sale_datas as $sale_data) {
                        $row['寶貝名稱']  .= ($row['寶貝名稱'] == '' ? '' : "\r\n") . $sale_data->pet_name;
                    }
                }
                $row['群組'] = '';
                if (isset($customer->group)) {
                    $row['群組'] = $customer->group->name;
                }
                $row['地址']  = $customer->county . $customer->district . $customer->address;
                $row['新增時間'] = date('Y-m-d', strtotime($customer->created_at));
                fputcsv($file, array($row['編號'], $row['姓名'], $row['電話'], $row['寶貝名稱'], $row['地址'], $row['群組'], $row['新增時間']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

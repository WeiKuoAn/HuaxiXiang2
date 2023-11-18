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
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /*ajax*/
    public function customer(Request $request)
    {
        if ($request->ajax()) {
            $output = "";
            $custs = Customer::where('name', 'like', $request->cust_name . '%')->get();

            if($custs){
                foreach ($custs as $key => $cust) {
                    $output.=  '<option value="'.$cust->id.'" label="('.$cust->name.')-'.$cust->mobile.'">';
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

            if($cust){
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
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%'.$request->name . '%';
                $customers = Customer::where('name', 'like', $name)->paginate(30);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $customers = Customer::where('mobile', 'like', $mobile)->paginate(30);
            }
            $pet_name = $request->pet_name;
            if (!empty($pet_name)) {
                $pet_name = $request->pet_name . '%';
                $sales  = Sale::where('pet_name', 'like', $pet_name)->get();
                if(count($sales) > 0) {
                    foreach($sales as $sale){
                        $customer_ids[] = $sale->customer_id;
                    }
                }else{
                    $customer_ids = [];
                }
                $customers = Customer::whereIn('id', $customer_ids)->paginate(30);
            }
            $group_id = $request->group_id;
            if (!empty($group_id)) {
                $customers = Customer::where('group_id', 'like', $group_id)->paginate(30);
            }

            if (!empty($name) && !empty($mobile) && !empty($pet_name) && !empty($group_id)) { 
                $pet_name = $request->pet_name . '%';
                $sales  = Sale::where('pet_name', 'like', $pet_name)->get();
                foreach($sales as $sale){
                    $customer_ids[] = $sale->customer_id;
                }
                $customers = Customer::where('name', 'like', $name)->where('mobile', 'like', $mobile)->whereIn('id', $customer_ids)->where('group_id', 'like', $group_id)->paginate(30);
            }
            $condition = $request->all();
        } else {
            $condition = '';
        }
        return view('customer.customers')->with('customers', $customers)
                                    ->with('request', $request)
                                    ->with('condition', $condition)
                                    ->with('customer_groups',$customer_groups);
    }


    public function customer_sale($id, Request $request)
    {
        $customer = Customer::where('id', $id)->first();
        if ($request) {
            $sales = Sale::where('customer_id',  $id)->whereIn('status', [9,100]);

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
        $groups = CustGroup::where('status','up')->get();
        return view('customer.create')->with('groups',$groups)->with('hint',0);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $groups = CustGroup::where('status','up')->get();
        $data = Customer::where('mobile',$request->mobile)->first();
        if($request->not_mobile == 1){ //未提供電話
            $customer = new Customer;
                $customer->name = $request->name;
                $customer->mobile = '未提供電話';
                $customer->county = $request->county;
                $customer->district = $request->district;
                $customer->address = $request->address;
                $customer->group_id = 1;
                $customer->created_up = Auth::user()->id;
                $customer->save();
                return redirect()->route('customer');
        }else{
            if(isset($data)){
                return view('customer.create')->with('groups',$groups)->with(['hint' => '1']);
            }else{
                $customer = new Customer;
                $customer->name = $request->name;
                $customer->mobile = $request->mobile;
                $customer->county = $request->county;
                $customer->district = $request->district;
                $customer->address = $request->address;
                $customer->group_id = 1;
                $customer->created_up = Auth::user()->id;
                $customer->save();
                return redirect()->route('customer');
            }
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
        $groups = CustGroup::where('status','up')->get();
        $customer = Customer::where('id', $id)->first();
        return view('customer.edit')->with('customer', $customer)->with('groups',$groups);
    }

    public function detail($id)
    {
        $groups = CustGroup::where('status','up')->get();
        $customer = Customer::where('id', $id)->first();
        return view('customer.detail')->with('customer', $customer)->with('groups',$groups);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
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
        $customer = Customer::where('id', $id)->first();
        $customer->name = $request->name;
        $customer->mobile = $request->mobile;
        $customer->county = $request->county;
        $customer->district = $request->district;
        $customer->address = $request->address;
        if(isset($customer->group_id))
        {
            $customer->group_id = $request->group_id;
        }else{
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
        $groups = CustGroup::where('status','up')->get();
        $customer = Customer::where('id', $id)->first();
        return view('customer.del')->with('customer', $customer)->with('groups',$groups);
    }

    public function destroy($id)
    {
        $customer = Customer::where('id', $id)->first();
        $customer->delete();
        return redirect()->route('customer');
    }

    public function sales($id)
    {
        $sales = Sale::whereIn('status',['9','100'])->where('customer_id',$id)->orderby('sale_date','desc')->get();
        $customer = Customer::where('id',$id)->first();
        return view('customer.sales')->with('sales', $sales)->with('customer',$customer);
    }
}

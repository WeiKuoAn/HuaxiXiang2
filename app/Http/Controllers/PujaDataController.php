<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Puja;
use App\Models\PujaData;
use App\Models\Product;
use App\Models\Sale;
use App\Models\PujaProduct;
use App\Models\PujaPet;
use App\Models\PujaDataAttchProduct;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class PujaDataController extends Controller
{
    public function puja_search(Request $request)
    {
        if ($request->ajax()) {
            $output = "";

            $puja = Puja::where('id', $request->puja_id)->first();
            
            if(isset($puja)){
                $output = $puja->price;
            }else{
                $output=  0;
            }
            return Response($output);
        }
    }

    public function customer_pet_search(Request $request)
    {
        if ($request->ajax()) {
            $output = "";

            $pet_count = Sale::where('customer_id', $request->cust_id)->distinct('pet_name')->count();
            
            $sales = Sale::where('customer_id', $request->cust_id)->distinct('pet_name')->whereNotNull('pet_name')->get();
            
            if(isset($sales)){
                foreach ($sales as $key => $sale) {
                    $output.=  '<option value="'.$sale->pet_name.'">'.$sale->pet_name.'</option>';
                  }
            }else{
                $output.=  '<option value="">請選擇...</option>';
            }
            return response()->json([$pet_count, $output]);
        }
    }

    public function index(Request $request)
    {
        $years = range(Carbon::now()->year, 2023);

        $pujas = [];
        $year = $request->year;
        if ($year != "null") {
            if(isset($year)){
                $pujas = Puja::where('date','like',$year.'%')->get();
            }
        }
        

        $datas = PujaData::where('status','1');
        if ($request) {
            $after_date = $request->after_date;
            if ($after_date) {
                $datas = $datas->where('date', '>=', $after_date);
            }
            $before_date = $request->before_date;
            if ($before_date) {
                $datas = $datas->where('date', '<=', $before_date);
            }
            $cust_name = $request->cust_name;
            if ($cust_name) {
                $cust_name = $request->cust_name.'%';
                $customers = Customer::where('name', 'like' ,$cust_name)->get();
                foreach($customers as $customer) {
                    $customer_ids[] = $customer->id;
                }
                if(isset($customer_ids)){
                    $datas = $datas->whereIn('customer_id', $customer_ids);
                }else{
                    $datas = $datas;
                }
            }
            $puja_id = $request->puja_id;

            if ($puja_id != "null") {
                if (isset($puja_id)) {
                    $datas = $datas->where('puja_id',  $puja_id);
                }else{
                    $datas = $datas ;
                }
            }
                
            $datas = $datas->orderby('date', 'desc')->paginate(50);

            $condition = $request->all();
        } else {
            $condition = '';
            $datas = $datas->orderby('date', 'desc')->paginate(50);
        }
        // dd($pujas);

        $products = Product::where('status', 'up')->orderby('seq','asc')->orderby('price','desc')->get();
        foreach($products as $product)
        {
            $product_name[$product->id] = $product->name;
        }
        // dd($product_name);
        return view('puja_data.index')->with('datas',$datas)
                                      ->with('product_name',$product_name)
                                      ->with('request',$request)
                                      ->with('years',$years)
                                      ->with('pujas',$pujas)
                                      ->with('condition',$condition);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $products = Product::where('status', 'up')->orderby('seq','asc')->orderby('price','desc')->get();
        $pujas = Puja::get();
        return view('puja_data.create')->with('pujas',$pujas)->with('products',$products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = new PujaData;
        $data->date = $request->date;
        $data->puja_id = $request->puja_id;
        $data->customer_id = $request->cust_name_q;
        $data->pet_name = $request->pet_name;
        $data->pay_id = $request->pay_id;
        $data->user_id = Auth::user()->id;
        $data->pay_method = $request->pay_method;
        if($request->pay_method == 'C'){
            $data->cash_price = $request->cash_price;
            $data->transfer_price = $request->transfer_price;
            $data->transfer_number = $request->transfer_number;
        }
        $data->pay_price = $request->pay_price;
        $data->total = $request->total;
        $data->status = 1;
        $data->comm = $request->comm;
        $data->save();

        $puja_data = PujaData::orderby('id', 'desc')->first();
        foreach($request->gdpaper_ids as $key=>$gdpaper_id)
        {
            if(isset($gdpaper_id)){
                $gdpaper = new PujaDataAttchProduct();
                $gdpaper->puja_data_id = $puja_data->id;
                $gdpaper->product_id = $request->gdpaper_ids[$key];
                $gdpaper->product_num = $request->gdpaper_num[$key];
                $gdpaper->product_total = $request->gdpaper_total[$key];
                $gdpaper->save();
            }
        }
        
        return redirect()->route('puja_datas');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $products = Product::where('status', 'up')->orderby('seq','asc')->orderby('price','desc')->get();
        $data = PujaData::where('id',$id)->first();
        $pujas = Puja::get();
        $data_products = PujaDataAttchProduct::where('puja_data_id',$id)->get();

        return view('puja_data.edit')->with('data',$data)
                                ->with('pujas',$pujas)
                                ->with('data_products',$data_products)
                                ->with('products',$products);
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

        $data = PujaData::where('id',$id)->first();
        $data->date = $request->date;
        $data->puja_id = $request->puja_id;
        $data->customer_id = $request->cust_name_q;
        $data->pet_name = $request->pet_name;
        $data->pay_id = $request->pay_id;
        $data->user_id = Auth::user()->id;
        $data->pay_method = $request->pay_method;
        if($request->pay_method == 'C'){
            $data->cash_price = $request->cash_price;
            $data->transfer_price = $request->transfer_price;
            $data->transfer_number = $request->transfer_number;
        }else{
            $data->cash_price = null;
            $data->transfer_price = null;
            $data->transfer_number = null;
        }
        $data->pay_price = $request->pay_price;
        $data->total = $request->total;
        $data->status = 1;
        $data->comm = $request->comm;
        $data->save();
        
        PujaDataAttchProduct::where('puja_data_id', $data->id)->delete();

        if(isset($request->gdpaper_ids))
        {
            foreach($request->gdpaper_ids as $key=>$gdpaper_id)
            {
                if(isset($gdpaper_id)){
                    $gdpaper = new PujaDataAttchProduct();
                    $gdpaper->puja_data_id = $data->id;
                    $gdpaper->product_id = $request->gdpaper_ids[$key];
                    $gdpaper->product_num = $request->gdpaper_num[$key];
                    $gdpaper->product_total = $request->gdpaper_total[$key];
                    $gdpaper->save();
                }
            }
        }
        return redirect()->route('puja_datas');
    }

    public function delete($id)
    {
        $products = Product::where('status', 'up')->orderby('seq','asc')->orderby('price','desc')->get();
        $data = PujaData::where('id',$id)->first();
        $pujas = Puja::get();
        $data_products = PujaDataAttchProduct::where('puja_data_id',$id)->get();

        return view('puja_data.del')->with('data',$data)
                                ->with('pujas',$pujas)
                                ->with('data_products',$data_products)
                                ->with('products',$products);
    }

    public function destroy(Request $request, $id)
    {

        $data = PujaData::where('id',$id)->first();
        PujaData::where('id',$id)->delete();
        PujaDataAttchProduct::where('puja_data_id', $data->id)->delete();

        return redirect()->route('puja_datas');
    }
}

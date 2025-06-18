<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductRestock;
use App\Models\ProductRestockItem;
use App\Models\ProductRestockPayData;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\PreInc;

class RestockController extends Controller
{
    public function product_cost_search(Request $request)
    {
        if ($request->ajax()) {
            $output = "";
            $product = Product::where('id', $request->gdpaper_id)->first();
            

            if($product->cost){
                $output.=  $product->cost;
            }else{
                $output.= 0;
            }

            return Response($output);
        }
    }

    public function index(Request $request)
    {
        $datas = ProductRestock::where('status','1')->orderby('date','desc');
        if($request->input() != null){
            $after_date = $request->after_date;
            if($after_date){
                $datas = $datas->where('date','>=',$after_date);
            }
            $before_date = $request->before_date;
            if($before_date){
                $datas = $datas->where('date','<=',$before_date);
            }
            $datas = $datas->paginate(50);
        }else{
            $datas = $datas->paginate(50);
        }
        return view('restock.index')->with('request', $request)->with('datas',$datas);
    }

    public function create()
    {
        $products = Product::where('status', 'up')->orderby('seq','asc')->where('restock',1)->orderby('price','desc')->get();
        return view('restock.create')->with('products', $products);
    }

    public function store(Request $request)
    {
        // dd($request->total);
        $data = new ProductRestock;
        $data->date = $request->date;
        $data->user_id = Auth::user()->id;
        $data->total = $request->total;
        $data->pay_price = $request->pay_price;
        $data->pay_id = $request->pay_id;
        $data->pay_method = $request->pay_method;
        $data->cash_price = $request->cash_price;
        $data->transfer_price = $request->transfer_price;
        $data->comm = $request->comm;
        $data->save();

        $restock = ProductRestock::orderby('id','desc')->first();
        foreach($request->gdpaper_ids as $key=>$gdpaper_id)
        {
            if(isset($gdpaper_id)){
                $gdpaper = new ProductRestockItem;
                $gdpaper->restock_id = $restock->id;
                $gdpaper->date = $request->date;
                $gdpaper->product_id = $request->gdpaper_ids[$key];
                $gdpaper->product_num = $request->gdpaper_num[$key];
                $gdpaper->product_cost = $request->gdpaper_cost[$key];
                $gdpaper->product_total = $request->gdpaper_total[$key];
                $gdpaper->save();
            }
        }

        $pay_data = new ProductRestockPayData;
        $pay_data->date = $request->date; 
        $pay_data->restock_id = $restock->id;
        $pay_data->pay_method = $request->pay_method; 
        $pay_data->price = $request->pay_price; 
        $pay_data->save();

        return redirect()->route('product.restock');
    }

    public function show($id)
    {
        $data = ProductRestock::where('id',$id)->first();
        $items = ProductRestockItem::where('restock_id',$id)->get();
        $products = Product::where('status', 'up')->orderby('seq','asc')->orderby('price','desc')->get();
        return view('restock.edit')->with('data',$data)->with('products',$products)->with('items',$items);
    }

    public function update($id , Request $request)
    {
        $data = ProductRestock::where('id',$id)->first();
        $data->date = $request->date;
        $data->user_id = Auth::user()->id;
        $data->total = $request->total;
        $data->pay_price = $request->pay_price;
        $data->pay_id = $request->pay_id;
        $data->pay_method = $request->pay_method;
        $data->cash_price = $request->cash_price;
        $data->transfer_price = $request->transfer_price;
        $data->comm = $request->comm;
        $data->save();

        ProductRestockItem::where('restock_id',$id)->delete();

        foreach($request->gdpaper_ids as $key=>$gdpaper_id)
        {
            if(isset($gdpaper_id)){
                $gdpaper = new ProductRestockItem;
                $gdpaper->restock_id = $id;
                $gdpaper->date = $request->date;
                $gdpaper->product_id = $request->gdpaper_ids[$key];
                $gdpaper->product_num = $request->gdpaper_num[$key];
                $gdpaper->product_cost = $request->gdpaper_cost[$key];
                $gdpaper->product_total = $request->gdpaper_total[$key];
                $gdpaper->save();
            }
        }

        return redirect()->route('product.restock');
    }

    public function delete($id)
    {
        $data = ProductRestock::where('id',$id)->first();
        $items = ProductRestockItem::where('restock_id',$id)->get();
        $products = Product::where('status', 'up')->orderby('seq','asc')->orderby('price','desc')->get();
        return view('restock.del')->with('data',$data)->with('products',$products)->with('items',$items);
    }

    public function destroy($id , Request $request)
    {
        ProductRestock::where('id',$id)->delete();
        ProductRestockItem::where('restock_id',$id)->delete();
        ProductRestockPayData::where('restock_id',$id)->delete();

        return redirect()->route('product.restock');
    }


    public function pay_index($id)
    {
        $datas = ProductRestockPayData::where('restock_id',$id)->orderby('date','desc')->get();
        return view('restock.pay_index')->with('datas',$datas);
    }

    public function pay_create($id)
    {
        $data = ProductRestock::where('id',$id)->first();
        $pay_datas = ProductRestockPayData::where('restock_id',$id)->get();

        $last_price = intval($data->total); 
        foreach($pay_datas as $pay_data)
        {
            $last_price -= $pay_data->price;
        }

        return view('restock.pay_create')->with('last_price',$last_price)->with('data',$data);
    }

    public function pay_store($id , Request $request)
    {
        $data = new ProductRestockPayData;
        $data->restock_id = $id;
        $data->date = $request->date;
        $data->pay_method = $request->pay_method;
        $data->price = $request->price;
        $data->save();
        
        return redirect()->route('product.restock.pay',$id);
    } 

    public function pay_edit($id)
    {

        $data = ProductRestockPayData::where('id',$id)->first();
        $restock_data = ProductRestock::where('id',$data->restock_id)->first();
        $currently_price = ProductRestockPayData::where('restock_id',$data->restock_id)->sum('price');//目前總付款
        $total_price = $restock_data->total;
        
        $last_price = intval($total_price) - intval($currently_price);

        return view('restock.pay_edit')->with('currently_price',$currently_price)
                                       ->with('total_price',$total_price)
                                       ->with('last_price',$last_price)
                                       ->with('data',$data);
    }

    public function pay_update($id , Request $request)
    {
        $data = ProductRestockPayData::where('id',$id)->first();
        $data->date = $request->date;
        $data->pay_method = $request->pay_method;
        $data->price = $request->price;
        $data->save();
        
        return redirect()->route('product.restock.pay',$data->restock_id);
    } 

    public function pay_delete($id)
    {

        $data = ProductRestockPayData::where('id',$id)->first();
        $restock_data = ProductRestock::where('id',$data->restock_id)->first();
        $currently_price = ProductRestockPayData::where('restock_id',$data->restock_id)->sum('price');//目前總付款
        $total_price = $restock_data->total;
        
        $last_price = intval($total_price) - intval($currently_price);

        return view('restock.pay_del')->with('currently_price',$currently_price)
                                       ->with('total_price',$total_price)
                                       ->with('last_price',$last_price)
                                       ->with('data',$data);
    }

    public function pay_destroy($id , Request $request)
    {
        $data = ProductRestockPayData::where('id',$id)->first();
        ProductRestockPayData::where('id',$id)->delete();
        return redirect()->route('product.restock.pay',$data->restock_id);
    } 

}

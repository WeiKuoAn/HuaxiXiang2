<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale_gdpaper;
use App\Models\ComboProduct;
use Illuminate\Support\Facades\DB;
use App\Models\PujaData;
use App\Models\PujaDataAttchProduct;

class Rpg13Controller extends Controller
{
    public function rpg13(Request $request)
    {
        $years = range(Carbon::now()->year, 2022);
        if (isset($request->year)) {
            $search_year = $request->year;
            $search_month = $request->month;
            $firstDay = Carbon::createFromDate($search_year , $search_month,1)->firstOfMonth();
            $lastDay = Carbon::createFromDate($search_year , $search_month,1)->lastOfMonth();
        } else {
            $firstDay = Carbon::now()->firstOfMonth();
            $lastDay = Carbon::now()->lastOfMonth();
        }

        $products = Product::where('status','up')->where('type','normal')->get();

        $normal_sale_products = DB::table('sale_data')
                                    ->join('sale_gdpaper','sale_gdpaper.sale_id', '=' , 'sale_data.id')
                                    ->leftjoin('product','product.id', '=' , 'sale_gdpaper.gdpaper_id')
                                    ->leftjoin('category','category.id', '=', 'product.category_id')
                                    ->where('sale_data.sale_date','>=',$firstDay)
                                    ->where('sale_data.sale_date','<=',$lastDay)
                                    ->where('product.type','normal')
                                    ->where('sale_data.status','9')
                                    ->get();

        $combo_sale_products = DB::table('sale_data')
                                    ->join('sale_gdpaper','sale_gdpaper.sale_id', '=' , 'sale_data.id')
                                    ->leftjoin('product','product.id', '=' , 'sale_gdpaper.gdpaper_id')
                                    ->leftjoin('category','category.id', '=', 'product.category_id')
                                    ->where('sale_data.sale_date','>=',$firstDay)
                                    ->where('sale_data.sale_date','<=',$lastDay)
                                    ->where('product.type','combo')
                                    ->where('sale_data.status','9')
                                    ->get();

        $set_sale_products = DB::table('sale_data')
                                    ->join('sale_gdpaper','sale_gdpaper.sale_id', '=' , 'sale_data.id')
                                    ->leftjoin('product','product.id', '=' , 'sale_gdpaper.gdpaper_id')
                                    ->leftjoin('category','category.id', '=', 'product.category_id')
                                    ->where('sale_data.sale_date','>=',$firstDay)
                                    ->where('sale_data.sale_date','<=',$lastDay)
                                    ->where('product.type','set')
                                    ->where('sale_data.status','9')
                                    ->get();
        
        $puja_data_products = DB::table('puja_data')
                                  ->join('puja_product','puja_product.puja_id', '=' , 'puja_data.puja_id')
                                  ->leftjoin('product','product.id', '=' , 'puja_product.product_id')
                                  ->leftjoin('puja','puja.id', '=', 'puja_data.puja_id')
                                  ->where('puja.date','>=',$firstDay)
                                  ->where('puja.date','<=',$lastDay)
                                //   ->where('product.type','set')
                                  ->where('puja_data.status','1')
                                  ->get();

        //計算商品賣出的數量
        $datas = [];

        foreach($products as $product)
        {
            $datas['products'][$product->id]['name'] = $product->name;
            $datas['products'][$product->id]['num'] = 0;
            $datas['normals'] = []; //一般商品
            // $datas['normals'][$product->id]['num'] = 0;
            
            $datas['sets'] = []; //套組商品

            $datas['combos'] = []; //組合商品

            $datas['pujas'] = []; //法會資訊商品
        }
        // dd($datas);
        
        //計算業務單，商品類型是的產品
        foreach($normal_sale_products as $normal_sale_product)
        {
            if(isset($datas['products'][$normal_sale_product->gdpaper_id]['num'])){
                $datas['products'][$normal_sale_product->gdpaper_id]['num'] += $normal_sale_product->gdpaper_num;
            }

            if(isset($datas['normals'][$normal_sale_product->gdpaper_id]['num'])){
                $datas['normals'][$normal_sale_product->gdpaper_id]['num'] += $normal_sale_product->gdpaper_num;
            }else{
                $datas['normals'][$normal_sale_product->gdpaper_id]['num'] = $normal_sale_product->gdpaper_num;
            }
        }

        //計算業務單，商品類型是的套組
        foreach($set_sale_products as $set_sale_product)
        {
            $combos_products = ComboProduct::where('product_id',$set_sale_product->gdpaper_id)->get();
            $product_data =  Product::where('id',$set_sale_product->gdpaper_id)->first();
            $datas['sets'][$set_sale_product->gdpaper_id]['name'] = $product_data->name;
                if(isset($datas['sets'][$set_sale_product->gdpaper_id]['count'] )){
                    $datas['sets'][$set_sale_product->gdpaper_id]['count'] ++;
                }else{
                    $datas['sets'][$set_sale_product->gdpaper_id]['count'] = 1;
                }
            foreach($combos_products as $combos_product)
            {
                $combo_product_data =  Product::where('id',$combos_product->include_product_id)->first();
                $datas['sets'][$set_sale_product->gdpaper_id]['details'][$combos_product->include_product_id]['name'] = $combo_product_data->name;
                $datas['products'][$combos_product->include_product_id]['num'] += $combos_product->num;
                if(isset($datas['sets'][$set_sale_product->gdpaper_id]['details'][$combos_product->include_product_id]['num'])){
                    $datas['sets'][$set_sale_product->gdpaper_id]['details'][$combos_product->include_product_id]['num'] += $combos_product->num;
                }else{
                    $datas['sets'][$set_sale_product->gdpaper_id]['details'][$combos_product->include_product_id]['num'] = $combos_product->num;
                }
            }
        }

        //計算業務單，商品類型是的組合
        foreach($combo_sale_products as $combo_sale_product)
        {
            $combos_products = ComboProduct::where('product_id',$combo_sale_product->gdpaper_id)->get();
            $product_data =  Product::where('id',$combo_sale_product->gdpaper_id)->first();
            $datas['combos'][$combo_sale_product->gdpaper_id]['name'] = $product_data->name;
                if(isset($datas['combos'][$combo_sale_product->gdpaper_id]['count'] )){
                    $datas['combos'][$combo_sale_product->gdpaper_id]['count'] ++;
                }else{
                    $datas['combos'][$combo_sale_product->gdpaper_id]['count'] = 1;
                }
            foreach($combos_products as $combos_product)
            {
                $combo_product_data =  Product::where('id',$combos_product->include_product_id)->first();
                $datas['combos'][$combo_sale_product->gdpaper_id]['details'][$combos_product->include_product_id]['name'] = $combo_product_data->name;
                $datas['products'][$combos_product->include_product_id]['num'] += $combos_product->num;
                if(isset($datas['combos'][$combo_sale_product->gdpaper_id]['details'][$combos_product->include_product_id]['num'])){
                    $datas['combos'][$combo_sale_product->gdpaper_id]['details'][$combos_product->include_product_id]['num'] += $combos_product->num;
                }else{
                    $datas['combos'][$combo_sale_product->gdpaper_id]['details'][$combos_product->include_product_id]['num'] = $combos_product->num;
                }
            }
        }

        //法會商品
        foreach($puja_data_products as $puja_data_product)
        {
            $puja_product_data = Product::where('id',$puja_data_product->product_id)->first();
            $datas['pujas'][$puja_data_product->puja_id]['name'] = $puja_data_product->name;
            $datas['pujas'][$puja_data_product->puja_id]['count'] = PujaData::where('puja_id',$puja_data_product->puja_id)->count();
            $datas['products'][$puja_data_product->product_id]['num'] += $puja_data_product->product_num;
            $datas['pujas'][$puja_data_product->puja_id]['details'][$puja_data_product->product_id]['name'] = $puja_product_data->name;
            $datas['pujas'][$puja_data_product->puja_id]['details'][$puja_data_product->product_id]['num'] = intval($datas['pujas'][$puja_data_product->puja_id]['count']) * intval($puja_data_product->product_num);
            $datas['pujas'][$puja_data_product->puja_id]['attachs'] = [];
        }

        //額外購買
        $puja_attachs = DB::table('puja_data_attach_product')
                                ->join('puja_data','puja_data.id', '=' , 'puja_data_attach_product.puja_data_id')
                                ->leftjoin('product','product.id', '=' , 'puja_data_attach_product.product_id')
                                ->leftjoin('puja','puja.id', '=', 'puja_data.puja_id')
                                ->where('puja.date','>=',$firstDay)
                                ->where('puja.date','<=',$lastDay)
                                ->select('puja_data.*','puja_data.puja_id','product.type','product.name as product_name'
                                        ,'puja_data_attach_product.*')
                                ->get();

            foreach($puja_attachs as $puja_attach)
            {
                $attach_product_data =  Product::where('id',$puja_attach->product_id)->first();

                //非套組
                if($puja_attach->type == 'normal')
                {
                    $datas['pujas'][$puja_attach->puja_id]['attachs'][$puja_attach->product_id]['name'] = $puja_attach->product_name;
                    $datas['products'][$puja_attach->product_id]['num'] += $puja_attach->product_num;
                    if(isset($datas['pujas'][$puja_attach->puja_id]['attachs'][$puja_attach->product_id]['num'])){
                        $datas['pujas'][$puja_attach->puja_id]['attachs'][$puja_attach->product_id]['num'] += $puja_attach->product_num;
                    }else{
                        $datas['pujas'][$puja_attach->puja_id]['attachs'][$puja_attach->product_id]['num'] = $puja_attach->product_num;
                    }
                }else{
                    $attach_combos_products = ComboProduct::where('product_id',$puja_attach->product_id)->get();
                    foreach($attach_combos_products as $attach_combos_product)
                    {
                        $product_data =  Product::where('id',$attach_combos_product->include_product_id)->first();
                        $datas['pujas'][$puja_attach->puja_id]['attachs'][$attach_combos_product->include_product_id]['name']=$product_data->name;
                        if(isset($datas['pujas'][$puja_attach->puja_id]['attachs'][$attach_combos_product->include_product_id]['num'])){
                            $datas['pujas'][$puja_attach->puja_id]['attachs'][$attach_combos_product->include_product_id]['num'] += $attach_combos_product->num;
                        }else{
                            $datas['pujas'][$puja_attach->puja_id]['attachs'][$attach_combos_product->include_product_id]['num'] = $attach_combos_product->num;
                        }
                        $datas['products'][$attach_combos_product->include_product_id]['num'] += $attach_combos_product->num;
                    }
                }
                //套組、組合
            }

        // dd($datas);




        return view('rpg13.index')->with('years', $years)->with('request',$request)->with('datas',$datas)->with('products',$products);
    }

}

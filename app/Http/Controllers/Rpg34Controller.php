<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\ComboProduct;
use App\Models\PujaData;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Rpg34Controller extends Controller
{
    public function rpg34(Request $request)
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

        $products = Product::where('type','normal')->whereIn('category_id',[2,3])->get();

        $sale_products = DB::table('sale_data')
                                    ->join('sale_souvenir','sale_souvenir.sale_id', '=' , 'sale_data.id')
                                    ->leftjoin('product','product.id', '=' , 'sale_souvenir.product_name')
                                    ->leftjoin('category','category.id', '=', 'product.category_id')
                                    ->where('sale_data.sale_date','>=',$firstDay)
                                    ->where('sale_data.sale_date','<=',$lastDay)
                                    ->where('product.type','normal')
                                    ->where('sale_data.status','9')
                                    ->select('sale_souvenir.*', 'sale_data.type_list', 'product.category_id', 'product.id', 'product.name')
                                    ->get();

        $specify_products = DB::table('sale_data')
                                    ->join('sale_souvenir','sale_souvenir.sale_id', '=' , 'sale_data.id')
                                    ->leftjoin('souvenir_type','souvenir_type.id', '=' , 'sale_souvenir.souvenir_type')
                                    ->where('sale_data.sale_date','>=',$firstDay)
                                    ->where('sale_data.sale_date','<=',$lastDay)
                                    ->where('sale_data.status','9')
                                    ->whereNotNull('sale_souvenir.souvenir_type')
                                    ->select('sale_souvenir.*', 'sale_data.type_list' , 'souvenir_type.name as souvenir_type_name')
                                    ->get();
        //計算商品賣出的數量
        $datas = [
            'products' => [
                'specify' => [
                    'category_name' => '指定款紀念品',
                    'products' => [],
                    'category_total' => 0
                ]
            ]
        ];

        foreach($specify_products as $specify_product)
        {
            if(!isset($datas['products']['specify']['products'][$specify_product->id])){
                $datas['products']['specify']['products'][$specify_product->id]['name'] = $specify_product->souvenir_type_name . '-' . $specify_product->product_name;
                $datas['products']['specify']['products'][$specify_product->id]['num'] = 0;
                $datas['products']['specify']['products'][$specify_product->id]['has_variants'] = false;
                $datas['products']['specify']['products'][$specify_product->id]['comments'] = [];
            }
            $datas['products']['specify']['products'][$specify_product->id]['num'] += $specify_product->product_num;
            
            // 收集備註（如果有的話）
            if (!empty($specify_product->comment)) {
                $datas['products']['specify']['products'][$specify_product->id]['comments'][] = $specify_product->comment;
            }
        }

        // 初始化資料結構
        foreach($products as $product)
        {
            if (!isset($datas['products'][$product->category_id])) {
                $datas['products'][$product->category_id]['category_name'] = $product->category_data->name;
                $datas['products'][$product->category_id]['products'] = [];
            }
            
            $datas['products'][$product->category_id]['products'][$product->id]['name'] = $product->name;
            $datas['products'][$product->category_id]['products'][$product->id]['num'] = 0;
            $datas['products'][$product->category_id]['products'][$product->id]['has_variants'] = false;
            
            if(isset($product->variants) && $product->variants->count() > 0)
            {
                $datas['products'][$product->category_id]['products'][$product->id]['has_variants'] = true;
                $datas['products'][$product->category_id]['products'][$product->id]['variants'] = [];
                foreach($product->variants as $variant)
                {
                    $datas['products'][$product->category_id]['products'][$product->id]['variants'][$variant->id]['name'] = $variant->variant_name;
                    $datas['products'][$product->category_id]['products'][$product->id]['variants'][$variant->id]['num'] = 0;
                }
            }
        }

        // 統計銷售數量
        foreach($sale_products as $sale_product)
        {
            if (isset($datas['products'][$sale_product->category_id]['products'][$sale_product->id])) {
                // 累加商品總數
                $datas['products'][$sale_product->category_id]['products'][$sale_product->id]['num'] += $sale_product->product_num;
                
                // 如果有變體ID，也累加變體數量
                if ($sale_product->product_variant_id && isset($datas['products'][$sale_product->category_id]['products'][$sale_product->id]['variants'][$sale_product->product_variant_id])) {
                    $datas['products'][$sale_product->category_id]['products'][$sale_product->id]['variants'][$sale_product->product_variant_id]['num'] += $sale_product->product_num;
                }
            }
        }
        
        // 計算總計
        $datas['grand_total'] = 0;
        foreach($datas['products'] as $category_id => $category) {
            $datas['products'][$category_id]['category_total'] = 0;
            foreach($category['products'] as $product_id => $product) {
                $datas['products'][$category_id]['category_total'] += $product['num'];
                $datas['grand_total'] += $product['num'];
            }
            
            // 按數量由多到少排序商品
            uasort($datas['products'][$category_id]['products'], function($a, $b) {
                return $b['num'] - $a['num'];
            });
        }
        
        // 按分類總數排序分類（可選）
        uasort($datas['products'], function($a, $b) {
            return $b['category_total'] - $a['category_total'];
        });

        // dd($datas);
        return view('rpg34.index')->with('years', $years)->with('request',$request)->with('datas',$datas)->with('products',$products);
    }
}

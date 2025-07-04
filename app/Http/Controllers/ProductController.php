<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ComboProduct;
use App\Models\Category;
use Intervention\Image\Facades\Image;
use App\Models\ProductRestockItem;
use App\Models\GdpaperInventoryItem;
use App\Models\Prom;
use App\Models\Sale_gdpaper;
use App\Models\PujaProduct;
use App\Models\PujaData;
use App\Models\PujaDataAttchProduct;
use Carbon\Carbon;
use PhpParser\Node\Expr\Print_;
use App\Models\PromType;

class ProductController extends Controller
{
    /*ajax*/
    public function product_search(Request $request)
    {
        $query = $request->get('data'); // 获取搜索关键字
        $product = Product::where('name', $query)->first(); // 根据关键字查询数据库

        return Response($product);
    }

    public function prom_product_search(Request $request)
    {
        $query = $request->get('prom_id'); // 获取搜索关键字
        $products = Product::where('prom_id', $query)->get(); // 根据关键字查询数据库
        return response()->json($products);
    }
    /*ajax*/

    public function index(Request $request)
    {
        $categorys = Category::where('status', 'up')->get();
        $datas = Product::orderby('status', 'asc')->orderby('seq', 'asc')->orderby('price', 'desc');

        if ($request->input() != null) {
            $name = $request->name;
            if ($name) {
                $name = '%' . $request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $type = $request->type;
            if ($type != "null") {
                if (isset($type)) {
                    $datas = $datas->where('type', $type);
                } else {
                    $datas = $datas;
                }
            }
            $category_id = $request->category_id;
            if ($category_id != "null") {
                if (isset($category_id)) {
                    $datas = $datas->where('category_id', $category_id);
                } else {
                    $datas = $datas;
                }
            }
            $datas = $datas->get();
        } else {
            $datas = $datas->get();
        }

        $restocks = [];

        foreach ($datas as $data) {
            $restocks[$data->id]['name'] = $data->name;

            // 🔹 1. 取得最近一筆盤點紀錄
            $inventory_item = GdpaperInventoryItem::where('product_id', $data->id)
                ->join('gdpaper_inventory_data', 'gdpaper_inventory_item.gdpaper_inventory_id', '=', 'gdpaper_inventory_data.inventory_no')
                ->where('gdpaper_inventory_data.state', '1')
                ->where('gdpaper_inventory_item.created_at', '>', '2023-06-09 11:59:59')
                ->orderBy('gdpaper_inventory_item.updated_at', 'desc')
                ->select('gdpaper_inventory_item.*', 'gdpaper_inventory_data.date as inventory_date')
                ->first();

            if ($inventory_item) {
                $base_stock = $inventory_item->new_num ?? $inventory_item->old_num ?? 0;
                $base_date = $inventory_item->inventory_date;
            } else {
                $base_stock = 0;
                $base_date = '2023-06-09 11:59:59';
            }

            // 🔹 2. 進貨總量（盤點後）
            $restock_amount = ProductRestockItem::where('product_id', $data->id)
                ->where('created_at', '>', $base_date)
                ->sum('product_num');

            // 3-1. 一般銷售數量（直接賣這個商品）
            $direct_sale = Sale_gdpaper::where('gdpaper_id', $data->id)
                ->where('created_at', '>', $base_date)
                ->sum('gdpaper_num');

            // 3-2. 被組合商品帶出用量
            $combo_used = 0;
            $combo_relations = ComboProduct::where('include_product_id', $data->id)->get();

            foreach ($combo_relations as $rel) {
                $combo_id = $rel->product_id;
                $used_qty = $rel->num;

                $combo_sales = Sale_gdpaper::where('gdpaper_id', $combo_id)
                    ->where('created_at', '>', $base_date)
                    ->sum('gdpaper_num');

                $combo_used += $combo_sales * $used_qty;
            }

            // 3-3. 法會直接用這個商品的數量
            $puja_direct_used = PujaDataAttchProduct::where('product_id', $data->id)->where('created_at', '>', $base_date)->sum('product_num');

            // 3-4. 法會使用的組合商品也會帶出此商品
            $puja_combo_used = 0;
            foreach ($combo_relations as $rel) {
                $combo_id = $rel->product_id;
                $used_qty = $rel->num;

                $combo_puja_sales = PujaDataAttchProduct::where('product_id', $combo_id)->where('created_at', '>', $base_date)->sum('product_num');

                $puja_combo_used += $combo_puja_sales * $used_qty;
            }

            // 📦 實體商品總出貨數量：
            $total_sold = $direct_sale + $combo_used + $puja_direct_used + $puja_combo_used;

            // 🔹 5. 庫存計算：盤點 + 進貨 - 出貨
            $current_stock = intval($base_stock) + intval($restock_amount) - intval($total_sold);

            // 🔹 6. 存入結果
            $restocks[$data->id]['cur_num'] = $current_stock;
        }



        // foreach($datas as $data)
        // {
        //     $inventory_item = GdpaperInventoryItem::where('product_id',$data->id)->where('created_at','>','2023-06-09 11:59:59')->orderby('updated_at','desc')->first();
        //     $restock_items = ProductRestockItem::where('product_id',$data->id)->where('created_at','>','2023-06-09 11:59:59')->orderby('updated_at','desc')->get();

        //     $sale_gdpapers = Sale_gdpaper::where('gdpaper_id',$data->id)->where('created_at','>','2023-06-09 11:59:59')->orderby('updated_at','desc')->get();
        //     // dd($sale_gdpapers);
        //     //法會預設數量
        //     $puja_datas = PujaData::get();


        //     $combo_products = ComboProduct::where('product_id',$data->id)->get();


        //     //累加進貨數量
        //     foreach($restock_items as $restock_item)
        //     {

        //         if ($inventory_item !=null && $restock_item != null) {
        //             //如果盤點時間 大於 進貨時間
        //             if ($inventory_item->updated_at < $restock_item->updated_at) {
        //                 $restocks[$data->id]['cur_num'] += $restock_item->product_num;
        //             }
        //         }
        //     }

        //     foreach($sale_gdpapers as $sale_gdpaper)
        //     {
        //         if ($inventory_item != null && $sale_gdpaper != null) {
        //             if ($inventory_item->updated_at < $sale_gdpaper->updated_at) {
        //                 //如果不是組合商品，單純做扣掉單一數量
        //                 if($data->type == 'normal')
        //                 $restocks[$data->id]['cur_num'] -= $sale_gdpaper->gdpaper_num;
        //             }
        //         }
        //         if ($inventory_item == null && $sale_gdpaper != null) {
        //                 foreach($combo_products as $combo_product){
        //                     //查詢套組中每個盤點產品的數量
        //                     $combo_inventory_item = GdpaperInventoryItem::where('product_id',$combo_product->include_product_id)->where('created_at','>','2023-06-09 11:59:59')->orderby('updated_at','desc')->first();
        //                     if ($combo_inventory_item->updated_at < $sale_gdpaper->updated_at) {
        //                         $restocks[$combo_product->include_product_id]['cur_num'] -= intval($combo_product->num) * intval($sale_gdpaper->gdpaper_num);
        //                 }
        //             }
        //         }
        //     }

        //     $pujas = [];
        //     //抓取法會報名數量
        //     foreach($puja_datas as $puja_data){
        //         $pujas[$puja_data->puja_id]['nums'] = 0; 
        //     }

        //     foreach($puja_datas as $puja_data){
        //         $pujas[$puja_data->puja_id]['nums']++; 
        //     }


        //     foreach($pujas as $puja_id=>$puja)
        //     {
        //         $puja_products = PujaProduct::where('puja_id',$puja_id)->where('product_id',$data->id)->where('created_at','>','2023-06-09 11:59:59')->orderby('updated_at','desc')->get();

        //         //減去法會預設的商品數量
        //         foreach($puja_products as $puja_product)
        //         {
        //             if ($inventory_item != null && $puja_product != null) {
        //                 if ($inventory_item->updated_at < $puja_product->updated_at) {
        //                     //如果不是組合商品，單純做扣掉單一數量
        //                     if($data->type == 'normal')
        //                     $restocks[$data->id]['cur_num'] -= (intval($pujas[$puja_product->puja_id]['nums']) * intval($puja_product->product_num));
        //                 }
        //             }
        //             if ($inventory_item == null && $puja_product != null) {
        //                     foreach($combo_products as $combo_product){
        //                         //查詢套組中每個盤點產品的數量
        //                         $combo_inventory_item = GdpaperInventoryItem::where('product_id',$combo_product->include_product_id)->where('created_at','>','2023-06-09 11:59:59')->orderby('updated_at','desc')->first();
        //                         if ($combo_inventory_item->updated_at < $puja_product->updated_at) {
        //                             $restocks[$combo_product->include_product_id]['cur_num'] -= intval($combo_product->num) * intval($pujas[$puja_product->puja_id]['nums']) * $puja_product->product_num;
        //                     }
        //                 }
        //             }
        //         }
        //     }

        //     $puja_attach_products = PujaDataAttchProduct::where('product_id',$data->id)->where('created_at','>','2023-06-09 11:59:59')->orderby('updated_at','desc')->get();

        //     foreach($puja_attach_products as $puja_attach_product)
        //         {
        //             if ($inventory_item != null && $puja_attach_product != null) {
        //                 if ($inventory_item->updated_at < $puja_attach_product->updated_at) {
        //                     //如果不是組合商品，單純做扣掉單一數量
        //                     if($data->type == 'normal')
        //                     $restocks[$data->id]['cur_num'] -= $puja_attach_product->product_num;
        //                 }
        //             }
        //             if ($inventory_item == null && $puja_attach_product != null) {
        //                     foreach($combo_products as $combo_product){
        //                         //查詢套組中每個盤點產品的數量
        //                         $combo_inventory_item = GdpaperInventoryItem::where('product_id',$combo_product->include_product_id)->where('created_at','>','2023-06-09 11:59:59')->orderby('updated_at','desc')->first();
        //                         if ($combo_inventory_item->updated_at < $puja_attach_product->updated_at) {
        //                             $restocks[$combo_product->include_product_id]['cur_num'] -= intval($combo_product->num) * $puja_attach_product->product_num;
        //                     }
        //                 }
        //             }
        //         }
        // }
        // dd($restocks);

        return view('product.index')->with('datas', $datas)->with('categorys', $categorys)->with('request', $request)->with('restocks', $restocks);
    }

    public function create()
    {
        $products = Product::where('type', '=', 'normal')->orderby('seq', 'desc')->orderby('price', 'desc')->get();
        foreach ($products as $product) {
            $data[] = $product->name;
        }
        $categorys = Category::where('status', 'up')->get();
        $promTypes = PromType::where('status', 'up')->get();
        return view('product.create')->with('products', $data)->with('categorys', $categorys)->with('promTypes', $promTypes);
    }

    public function store(Request $request)
    {
        // dd($request->stock);
        $data = new Product;
        $data->type = $request->type;
        $data->category_id = $request->category_id;
        $data->number = $request->number;
        $data->name = $request->name;
        $data->description = $request->description;
        $data->price = $request->price;
        $data->seq = $request->seq;
        $data->cost = $request->cost;
        $data->alarm_num = $request->alarm_num;
        $data->status = $request->status;
        // $data->prom_id = $request->prom_id;
        if (isset($request->commission)) {
            $data->commission = $request->commission;
        } else {
            $data->commission = 0;
        }
        if (isset($request->stock)) {
            $data->stock = $request->stock;
        } else {
            $data->stock = 1;
        }
        if (isset($request->restock)) {
            $data->restock = $request->restock;
        } else {
            $data->restock = 1;
        }
        $data->save();
        // dd($data->type);
        if ($request->type == 'combo' || $request->type == 'set') {
            $data = Product::orderby('id', 'desc')->first();
            // dd($request->product_id);
            foreach ($request->product_id as $key => $value) {
                $combo_data = new ComboProduct;
                $combo_data->product_id = $data->id;
                $combo_data->include_product_id = $request->product_id[$key];
                $combo_data->num = $request->product_qty[$key];
                $combo_data->price = $request->unit_price[$key];
                $combo_data->save();
            }
        }

        return redirect()->route('product');

        //圖片
        // dd($request);
        // $imagename = Carbon::now();
        // $imagePath = request('po_image')->store("uploads/{$imagename}", 'public');
        // $image = Image::make(public_path("storage/{$imagePath}"))->resize(900, null, function ($constraint) {
        //     $constraint->aspectRatio();
        // });
        // $image->save(public_path("storage/{$imagePath}"), 60);
        // $image->save();
    }

    public function show($id)
    {
        $products = Product::where('type', '=', 'normal')->orderby('seq', 'desc')->orderby('price', 'desc')->get();
        foreach ($products as $product) {
            $datas[] = $product->name;
        }
        $categorys = Category::where('status', 'up')->get();

        $data = Product::where('id', $id)->first();
        $combo_datas = ComboProduct::where('product_id', $id)->get();
        $promTypes = PromType::where('status', 'up')->get();
        if (isset($data->prom_id)) {
            $proms = Prom::where('type', $data->prom_data->type)->get();
        } else {
            $proms = [];
        }
        return view('product.edit')->with('products', $datas)->with('categorys', $categorys)->with('data', $data)->with('combo_datas', $combo_datas)->with('promTypes', $promTypes)->with('proms', $proms);
    }

    public function update(Request $request, $id)
    {
        $data = Product::where('id', $id)->first();
        $data->type = $request->type;
        $data->category_id = $request->category_id;
        $data->number = $request->number;
        $data->name = $request->name;
        $data->description = $request->description;
        $data->price = $request->price;
        $data->seq = $request->seq;
        $data->cost = $request->cost;
        $data->alarm_num = $request->alarm_num;
        $data->status = $request->status;
        // $data->prom_id = $request->prom_id;
        if (isset($request->commission)) {
            $data->commission = $request->commission;
        } else {
            $data->commission = 0;
        }
        if (isset($request->stock)) {
            $data->stock = $request->stock;
        } else {
            $data->stock = 1;
        }
        if (isset($request->restock)) {
            $data->restock = $request->restock;
        } else {
            $data->restock = 1;
        }
        $data->save();

        if ($request->type == 'combo' || $request->type == 'set') {
            $old_combo_datas = ComboProduct::where('product_id', $id)->get();
            if (count($old_combo_datas) > 0) {
                foreach ($old_combo_datas as $old_combo_data) {
                    $old_combo_data->delete();
                }
            }

            foreach ($request->product_id as $key => $value) {
                $combo_data = new ComboProduct;
                $combo_data->product_id = $id;
                $combo_data->include_product_id = $request->product_id[$key];
                $combo_data->num = $request->product_qty[$key];
                $combo_data->price = $request->unit_price[$key];
                $combo_data->save();
            }
        }

        return redirect()->route('product');
    }

    public function delete($id)
    {
        $products = Product::orderby('seq', 'asc')->orderby('price', 'desc')->get();
        foreach ($products as $product) {
            $datas[] = $product->name;
        }
        $categorys = Category::where('status', 'up')->get();

        $data = Product::where('id', $id)->first();

        $combo_datas = ComboProduct::where('product_id', $id)->get();

        return view('product.delete')->with('products', $datas)->with('categorys', $categorys)->with('data', $data)->with('combo_datas', $combo_datas);
    }

    public function destroy($id)
    {
        $data = Product::where('id', $id)->first();
        $data->delete();
        $old_combo_datas = ComboProduct::where('product_id', $id)->get();
        if (count($old_combo_datas) > 0) {
            foreach ($old_combo_datas as $old_combo_data) {
                $old_combo_data->delete();
            }
        }
        return redirect()->route('product');
    }
}

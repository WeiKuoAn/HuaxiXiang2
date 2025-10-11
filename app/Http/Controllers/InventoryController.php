<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use Carbon\Carbon;
use App\Models\GdpaperInventoryData;
use App\Models\GdpaperInventoryItem;
use App\Models\IncomeData;
use App\Models\User;
use App\Models\PujaProduct;
use App\Models\ProductRestockItem;
use App\Models\Sale_gdpaper;
use App\Models\ComboProduct;
use App\Models\PujaData;
use App\Models\PujaDataAttchProduct;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;


class InventoryController extends Controller
{
  public function index(Request $request)
  {
    $products = Product::orderBy('price', 'desc')->where('type', '!=', 'combo')->where('status', 'up')->get();

    $datas = GdpaperInventoryData::orderby('id', 'desc');
    if ($request) {
      $after_date = $request->after_date;
      if ($after_date) {
        $datas = $datas->where('date', '>=', $after_date);
      }
      $before_date = $request->before_date;
      if ($before_date) {
        $datas = $datas->where('date', '<=', $before_date);
      }
      $user_id = $request->user_id;
      if ($user_id != "null") {
        if (isset($user_id)) {
          $datas = $datas->where('update_user_id', $user_id);
        } else {
          $datas = $datas;
        }
      }
      $state = $request->state;
      if (isset($state)) {
        $datas = $datas->where('state', $state);
      } else {
        $datas = $datas->where('state', '0');
      }
      $datas = $datas->get();
    } else {
      $datas = $datas->get();
    }

    $users = User::where('status', '0')->orderby('job_id', 'desc')->get();
    return view('inventory.index')->with('products', $products)->with('datas', $datas)->with('request', $request)->with('users', $users);
  }

  public function create(Request $request)
  {
    $categorys = Category::where('status', 'up')->get();
    $users = User::where('status', '0')->orderby('job_id', 'desc')->get();
    // dd($users);

    return view('inventory.create')->with('users', $users)->with('categorys', $categorys);
  }

  public function store(Request $request)
  {
    // 建立單號
    $today = Carbon::now()->locale('zh-tw')->format('Ymd');  // 取得當天日期，格式為 '20231225'
    $inventory_no = '';

    // 查詢當日是否已有單號，按照 inventory_no 降序排序，取最新的一筆
    $data = GdpaperInventoryData::where('inventory_no', 'like', "$today%")
      ->orderby('inventory_no', 'desc')
      ->first();

    // 自動計算單號序號，如果沒有則預設為 1，有則從最後一筆 +1
    $i = isset($data->inventory_no)
      ? intval(substr($data->inventory_no, 8, 2)) + 1  // 取得最後兩位作為序號，並轉為整數後 +1
      : 1;  // 如果查無資料，表示當日還沒有單號，從 1 開始

    // 格式化單號，確保序號部分為兩位數，避免 '1' 顯示成 '202312251' 這樣的格式問題
    $inventory_no = sprintf('%s%02d', $today, $i);

    // 建立庫存資料，使用 Eloquent create 方法直接存入資料庫
    $InventoryData = GdpaperInventoryData::create([
      'inventory_no' => $inventory_no,  // 自動生成的單號
      'type' => $request->category_id,  // 分類 ID
      'date' => Carbon::now()->locale('zh-tw')->format('Y-m-d H:i:s'),  // 當前日期，格式為 '2023-12-25 12:00:00'
      'state' => 0,  // 初始狀態設為 0 (未修改)
      'created_user_id' => Auth::id(),  // 登入用戶的 ID
      'update_user_id' => $request->update_user_id,  // 更新該筆資料的用戶 ID
    ]);

    // 查詢產品清單，根據分類和狀態篩選
    $query = Product::orderby('seq', 'asc')  // 依照序列排序 (升序)
      ->orderby('price', 'desc')  // 價格降序排列
      ->where('stock', '1')  // 庫存大於 0
      ->where('type', '!=', 'combo')  // 排除組合產品
      ->where('status', 'up');  // 只選取上架產品

    // 如果分類為 'all'，則選取所有符合條件的產品，否則根據分類篩選
    $products = $request->category_id === 'all'
      ? $query->get()
      : $query->where('category_id', $request->category_id)->get();

    // 新增庫存項目（支援細項）
    if ($InventoryData->type) {  // 確認有選擇類型後才執行
      foreach ($products as $product) {
        // 若此商品有細項，逐一建立細項的盤點項目
        if ($product->has_variants) {
          $variants = ProductVariant::where('product_id', $product->id)->where('status', 'active')->orderBy('sort_order', 'asc')->get();
          foreach ($variants as $variant) {
            GdpaperInventoryItem::create([
              'gdpaper_inventory_id' => $inventory_no,
              'product_id' => $product->id,
              'variant_id' => $variant->id,
              'is_variant' => 1,
              'type' => $request->category_id,
              'old_num' => $this->calculateCurrentStockForVariant($variant->id),
              'new_num' => null,
            ]);
          }
        } else {
          // 無細項，維持以商品為單位
          GdpaperInventoryItem::create([
            'gdpaper_inventory_id' => $inventory_no,
            'product_id' => $product->id,
            'is_variant' => 0,
            'type' => $request->category_id,
            'old_num' => $this->calculateCurrentStock($product->id),
            'new_num' => null,
          ]);
        }
      }
    }

    // 返回產品庫存列表頁面
    return redirect()->route('product.inventorys');
  }


  public function delete($id, Request $request)
  {
    $categorys = Category::where('status', 'up')->get();
    $users = User::where('status', '0')->orderby('job_id', 'desc')->get();
    $data = GdpaperInventoryData::where('id', $id)->first();
    return view('inventory.del')->with('data', $data)->with('categorys', $categorys)->with('users', $users);
  }

  public function destroy($id)
  {
    $data = GdpaperInventoryData::where('id', $id)->first();

    $items = GdpaperInventoryItem::where('gdpaper_inventory_id', $data->inventory_no)->get();
    foreach ($items as $item) {
      $item->delete();
    }
    $data->delete();

    return redirect()->route('product.inventorys');
  }

  //盤點細項
  public function inventoryItem_index(Request $request, $product_inventory_id)
  {
    $inventory_no = $product_inventory_id;
    $datas = GdpaperInventoryItem::where('gdpaper_inventory_id', $product_inventory_id)->get();
    
    // 取得盤點狀態
    $inventory_data = GdpaperInventoryData::where('inventory_no', $product_inventory_id)->first();

    return view('inventory.item')->with('datas', $datas)->with('inventory_no', $inventory_no)->with('inventory_data', $inventory_data);
  }

  public function inventoryItem_edit(Request $request, $product_inventory_id)
  {
    // 取得操作類型（暫存或送出）
    $action = $request->input('action', 'submit');
    
    $inventory_data = GdpaperInventoryData::orderby('inventory_no', 'desc')->where('inventory_no', $product_inventory_id)->first();
    $datas = GdpaperInventoryItem::where('gdpaper_inventory_id', $product_inventory_id)->get();

    foreach ($datas as $data) {
      // 細項記錄：從 variant[] 陣列取值；否則沿用 product[]
      if (!empty($data->variant_id)) {
        $data->new_num = $request->variant[$data->variant_id] ?? null;
        $data->comment = $request->comment_variant[$data->variant_id] ?? null;
        
        // 只有送出盤點時才同步更新細項的實際庫存量
        if ($action === 'submit' && $data->new_num !== null) {
          $variant = ProductVariant::find($data->variant_id);
          if ($variant) {
            $variant->stock_quantity = intval($data->new_num);
            $variant->save();
          }
        }
      } else {
        $data->new_num = $request->product[$data->product_id] ?? null;
        $data->comment = $request->comment[$data->product_id] ?? null;
      }
      $data->save();
    }

    // 根據操作類型決定是否改變盤點狀態
    if ($action === 'submit') {
      // 送出盤點：盤點狀況改為1（已盤點）
      $inventory_data->state = 1;
      $inventory_data->save();

      if (Auth::user()->level != 2) {
        return redirect()->route('product.inventorys');
      } else {
        return redirect()->route('person.inventory');
      }
    } else {
      // 暫存：保持狀態為0（未完成），按照用戶等級跳轉
      if (Auth::user()->level != 2) {
        return redirect()->route('product.inventorys');
      } else {
        return redirect()->route('person.inventory');
      }
    }
  }

  private function restocks()
  {

    $datas = Product::orderby('seq', 'desc')->orderby('price', 'desc')->get();
    $restocks = [];

    foreach ($datas as $data) {
      $inventory_item = GdpaperInventoryItem::where('product_id', $data->id)->where('created_at', '>', '2023-06-09 11:59:59')->orderby('updated_at', 'desc')->first();
      $restock_items = ProductRestockItem::where('product_id', $data->id)->where('created_at', '>', '2023-06-09 11:59:59')->orderby('updated_at', 'desc')->get();
      $sale_gdpapers = Sale_gdpaper::where('gdpaper_id', $data->id)->where('created_at', '>', '2023-06-09 11:59:59')->orderby('updated_at', 'desc')->get();
      $restocks[$data->id]['name'] = $data->name;

      //取得最新庫存盤點數量
      if (isset($inventory_item->new_num)) {
        $restocks[$data->id]['cur_num'] = intval($inventory_item->new_num);
      } elseif (isset($inventory_item->old_num)) {
        $restocks[$data->id]['cur_num'] = intval($inventory_item->old_num);
      } else {
        $restocks[$data->id]['cur_num'] = 0;
      }
    }

    foreach ($datas as $data) {
      $inventory_item = GdpaperInventoryItem::where('product_id', $data->id)->where('created_at', '>', '2023-06-09 11:59:59')->orderby('updated_at', 'desc')->first();
      $restock_items = ProductRestockItem::where('product_id', $data->id)->where('created_at', '>', '2023-06-09 11:59:59')->orderby('updated_at', 'desc')->get();

      $sale_gdpapers = Sale_gdpaper::where('gdpaper_id', $data->id)->where('created_at', '>', '2023-06-09 11:59:59')->orderby('updated_at', 'desc')->get();
      // dd($sale_gdpapers);
      //法會預設數量
      $puja_datas = PujaData::get();


      $combo_products = ComboProduct::where('product_id', $data->id)->get();


      //累加進貨數量
      foreach ($restock_items as $restock_item) {

        if ($inventory_item != null && $restock_item != null) {
          //如果盤點時間 大於 進貨時間
          if ($inventory_item->updated_at < $restock_item->updated_at) {
            $restocks[$data->id]['cur_num'] += $restock_item->product_num;
          }
        }
      }

      foreach ($sale_gdpapers as $sale_gdpaper) {
        if ($inventory_item != null && $sale_gdpaper != null) {
          if ($inventory_item->updated_at < $sale_gdpaper->updated_at) {
            //如果不是組合商品，單純做扣掉單一數量
            if ($data->type == 'normal')
              $restocks[$data->id]['cur_num'] -= $sale_gdpaper->gdpaper_num;
          }
        }
        if ($inventory_item == null && $sale_gdpaper != null) {
          foreach ($combo_products as $combo_product) {
            //查詢套組中每個盤點產品的數量
            $combo_inventory_item = GdpaperInventoryItem::where('product_id', $combo_product->include_product_id)->where('created_at', '>', '2023-06-09 11:59:59')->orderby('updated_at', 'desc')->first();
            if ($combo_inventory_item->updated_at < $sale_gdpaper->updated_at) {
              $restocks[$combo_product->include_product_id]['cur_num'] -= intval($combo_product->num) * intval($sale_gdpaper->gdpaper_num);
            }
          }
        }
      }

      $pujas = [];
      //抓取法會報名數量
      foreach ($puja_datas as $puja_data) {
        $pujas[$puja_data->puja_id]['nums'] = 0;
      }

      foreach ($puja_datas as $puja_data) {
        $pujas[$puja_data->puja_id]['nums']++;
      }


      foreach ($pujas as $puja_id => $puja) {
        $puja_products = PujaProduct::where('puja_id', $puja_id)->where('product_id', $data->id)->where('created_at', '>', '2023-06-09 11:59:59')->orderby('updated_at', 'desc')->get();

        //減去法會預設的商品數量
        foreach ($puja_products as $puja_product) {
          if ($inventory_item != null && $puja_product != null) {
            if ($inventory_item->updated_at < $puja_product->updated_at) {
              //如果不是組合商品，單純做扣掉單一數量
              if ($data->type == 'normal')
                $restocks[$data->id]['cur_num'] -= (intval($pujas[$puja_product->puja_id]['nums']) * intval($puja_product->product_num));
            }
          }
          if ($inventory_item == null && $puja_product != null) {
            foreach ($combo_products as $combo_product) {
              //查詢套組中每個盤點產品的數量
              $combo_inventory_item = GdpaperInventoryItem::where('product_id', $combo_product->include_product_id)->where('created_at', '>', '2023-06-09 11:59:59')->orderby('updated_at', 'desc')->first();
              if ($combo_inventory_item->updated_at < $puja_product->updated_at) {
                $restocks[$combo_product->include_product_id]['cur_num'] -= intval($combo_product->num) * intval($pujas[$puja_product->puja_id]['nums']) * $puja_product->product_num;
              }
            }
          }
        }
      }

      $puja_attach_products = PujaDataAttchProduct::where('product_id', $data->id)->where('created_at', '>', '2023-06-09 11:59:59')->orderby('updated_at', 'desc')->get();

      foreach ($puja_attach_products as $puja_attach_product) {
        if ($inventory_item != null && $puja_attach_product != null) {
          if ($inventory_item->updated_at < $puja_attach_product->updated_at) {
            //如果不是組合商品，單純做扣掉單一數量
            if ($data->type == 'normal')
              $restocks[$data->id]['cur_num'] -= $puja_attach_product->product_num;
          }
        }
        if ($inventory_item == null && $puja_attach_product != null) {
          foreach ($combo_products as $combo_product) {
            //查詢套組中每個盤點產品的數量
            $combo_inventory_item = GdpaperInventoryItem::where('product_id', $combo_product->include_product_id)->where('created_at', '>', '2023-06-09 11:59:59')->orderby('updated_at', 'desc')->first();
            if ($combo_inventory_item->updated_at < $puja_attach_product->updated_at) {
              $restocks[$combo_product->include_product_id]['cur_num'] -= intval($combo_product->num) * $puja_attach_product->product_num;
            }
          }
        }
      }
    }

    return $restocks;
  }

  public function calculateCurrentStock($productId)
  {
    // 取得商品資訊
    $product = Product::find($productId);
    if (!$product) return 0;

    // 取得最近一筆盤點（已完成）
    $inventory_item = GdpaperInventoryItem::where('product_id', $productId)
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
      // 沒有盤點記錄時，使用商品的初始庫存量
      $base_stock = $product->initial_stock ?? 0;
      $base_date = '2023-06-09 11:59:59';
    }

    $restock_amount = ProductRestockItem::where('product_id', $productId)
      ->where('created_at', '>', $base_date)
      ->sum('product_num');

    $direct_sale = Sale_gdpaper::where('gdpaper_id', $productId)
      ->where('created_at', '>', $base_date)
      ->sum('gdpaper_num');

    // 組合用掉的
    $combo_used = 0;
    $combo_relations = ComboProduct::where('include_product_id', $productId)->get();

    foreach ($combo_relations as $rel) {
      $combo_id = $rel->product_id;
      $used_qty = $rel->num;

      $combo_sales = Sale_gdpaper::where('gdpaper_id', $combo_id)
        ->where('created_at', '>', $base_date)
        ->sum('gdpaper_num');

      $combo_used += $combo_sales * $used_qty;
    }

    $puja_direct_used = PujaDataAttchProduct::where('product_id', $productId)
      ->where('created_at', '>', $base_date)
      ->sum('product_num');

    $puja_combo_used = 0;
    foreach ($combo_relations as $rel) {
      $combo_id = $rel->product_id;
      $used_qty = $rel->num;

      $combo_puja_sales = PujaDataAttchProduct::where('product_id', $combo_id)
        ->where('created_at', '>', $base_date)
        ->sum('product_num');

      $puja_combo_used += $combo_puja_sales * $used_qty;
    }

    $total_sold = $direct_sale + $combo_used + $puja_direct_used + $puja_combo_used;

    $current_stock = intval($base_stock) + intval($restock_amount) - intval($total_sold);

    return $current_stock;
  }

  /**
   * 計算細項的目前庫存
   * 規則：以最近一次「已完成盤點」的細項基準數為底，之後的進貨/銷售/法會/組合使用都以細項為單位
   * 若尚無細項盤點紀錄，基準視為 0，自 2023-06-09 11:59:59 開始累加
   */
  public function calculateCurrentStockForVariant($variantId)
  {
    $variant = ProductVariant::find($variantId);
    if (!$variant) return 0;

    // 取得最近一筆「細項」的盤點（已完成）
    $inventory_item = GdpaperInventoryItem::where('variant_id', $variantId)
      ->where('is_variant', 1)  // 確保是細項記錄
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
      // 沒有盤點記錄時，使用細項的庫存量
      $base_stock = $variant->stock_quantity ?? 0;
      $base_date = '2023-06-09 11:59:59';
    }

    // 進貨：目前進貨尚未做細項，暫以 0（未來導入細項進貨後，改查 variant 層）
    $restock_amount = 0;

    // 銷售：目前銷售也尚未細項化，暫以 0（未來導入再補）
    $direct_sale = 0;

    // 組合用掉、法會附加用掉：同上，暫以 0，避免錯扣
    $combo_used = 0;
    $puja_direct_used = 0;
    $puja_combo_used = 0;

    $total_sold = $direct_sale + $combo_used + $puja_direct_used + $puja_combo_used;
    $current_stock = intval($base_stock) + intval($restock_amount) - intval($total_sold);
    return $current_stock;
  }
}

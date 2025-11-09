<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sale;
use App\Models\SaleHistory;

class ScrappedController extends Controller
{
    /**
     * 顯示新增報廢單表單
     */
    public function create()
    {
        $date = date('Y-m-d');
        return view('sale.scrapped_create')->with('date', $date);
    }

    /**
     * 儲存新的報廢單
     */
    public function store(Request $request)
    {
        $request->validate([
            'sale_on' => 'required|string|max:255',
            'sale_date' => 'required|date',
            'comm' => 'required|string|max:500',
        ]);

        $sale = new Sale();
        $sale->sale_on = 'No.' .$request->sale_on;
        $sale->sale_date = $request->sale_date;
        $sale->type_list = 'scrapped';
        $sale->comm = $request->comm;
        $sale->user_id = Auth::user()->id;
        $sale->status = 1; // 預設狀態
        $sale->save();

        return redirect()->route('sale.scrapped.create')->with('success', '報廢單已成功建立！');
    }

    /**
     * 顯示編輯報廢單表單
     */
    public function edit($id)
    {
        $scrapped = Sale::where('id', $id)->where('type_list', 'scrapped')->firstOrFail();
        return view('sale.scrapped_edit')->with('scrapped', $scrapped);
    }

    /**
     * 更新報廢單
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'sale_on' => 'required|string|max:255',
            'sale_date' => 'required|date',
            'comm' => 'required|string|max:500',
        ]);

        $scrapped = Sale::where('id', $id)->where('type_list', 'scrapped')->firstOrFail();
        $scrapped->sale_on = 'No.' .$request->sale_on;
        $scrapped->sale_date = $request->sale_date;
        $scrapped->comm = $request->comm;
        $scrapped->save();

        return redirect()->route('sales')->with('success', '報廢單已成功更新！');
    }

    /**
     * 顯示刪除確認頁面
     */
    public function delete($id)
    {
        $scrapped = Sale::where('id', $id)->where('type_list', 'scrapped')->firstOrFail();
        return view('sale.scrapped_del')->with('scrapped', $scrapped);
    }

    /**
     * 刪除報廢單
     */
    public function destroy($id)
    {
        $scrapped = Sale::where('id', $id)->where('type_list', 'scrapped')->firstOrFail();
        $scrapped->delete();

        return redirect()->route('sales')->with('success', '報廢單已成功刪除！');
    }

    public function check_show($id)
    {
        $scrapped = Sale::where('id', $id)->where('type_list', 'scrapped')->firstOrFail();
        return view('sale.scrapped_check')->with('scrapped', $scrapped);
    }

    /**
     * Ajax 版本的 check_show - 用於 modal 顯示
     */
    public function check_show_ajax($id)
    {
        $scrapped = Sale::where('id', $id)->where('type_list', 'scrapped')->firstOrFail();
        return view('sale.scrapped_check_modal')->with('scrapped', $scrapped)->render();
    }

    public function check_data(Request $request, $id)
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
            
            // 檢查是否為 Ajax 請求
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => '報廢單對帳操作成功'
                ]);
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
            
            // 檢查是否為 Ajax 請求
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => '報廢單送出對帳成功'
                ]);
            }
            
            return redirect()->route('person.sales');
        }
    }

    /**
     * 檢查單號是否重複
     */
    public function check_sale_on(Request $request)
    {
        $sale_on = $request->input('sale_on');
        
        if (empty($sale_on)) {
            return response()->json([
                'exists' => false,
                'message' => '請輸入單號'
            ]);
        }

        $exists = Sale::where('sale_on', $sale_on)
                    ->where('type_list', 'scrapped')
                    ->exists();

        if ($exists) {
            return response()->json([
                'exists' => true,
                'message' => '此單號已存在'
            ]);
        } else {
            return response()->json([
                'exists' => false,
                'message' => '單號可用'
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deregistration; // 確保引入 Deregistration 模型
use App\Models\Customer;

class DeregistrationController extends Controller
{
    public function index(Request $request)
    {
        // 初始化查詢建構器
        $query = Deregistration::query();
        
        // 處理客戶姓名搜尋
        $cust_name = $request->cust_name;
        if ($cust_name) {
            $customers = Customer::where('name', 'like', '%' . $cust_name . '%')->get();
            foreach ($customers as $customer) {
                $query->orWhere('customer_id', $customer->id);
            }
        }
        
        // 處理申請人搜尋
        $registrant = $request->registrant;
        if ($registrant) {
            $query->where('registrant', 'like', '%' . $registrant . '%');
        }
        
        // 處理身分證號搜尋
        $ic_card = $request->ic_card;
        if ($ic_card) {
            $query->where('ic_card', 'like', '%' . $ic_card . '%');
        }
        
        // 執行分頁查詢
        $datas = $query->paginate(50);
        
        // 將搜尋參數合併到請求中，以便在視圖中保持搜尋條件
        $request->merge(['cust_name' => $cust_name, 'registrant' => $registrant, 'ic_card' => $ic_card]);
        
        return view('deregistration.index')->with('datas', $datas)->with('request', $request); // 返回除戶管理的視圖
    }

    public function create()
    {
        $customers = Customer::orderby('created_at', 'desc')->get();
        return view('deregistration.create')->with('customers', $customers); // 返回新增除戶記錄的視圖
    }

    public function store(Request $request)
    {
        $data = new Deregistration();
        $data->number = $request->input('number');
        $data->customer_id = $request->input('customer_id');
        $data->registrant = $request->input('registrant');
        $data->ic_card = $request->input('ic_card');
        $data->pet_name = $request->input('pet_name');
        $data->variety = $request->input('variety');
        $data->comment = $request->input('comment');
        $data->created_by = auth()->user()->id; // 設置創建者為當前用戶
        $data->save(); // 保存除戶記錄

        return redirect()->route('deregistration.index')->with('success', '新增除戶記錄成功');
    }

    public function edit($id)
    {
        $data = Deregistration::findOrFail($id); // 獲取指定 ID 的除戶記錄
        $customers = Customer::orderby('created_at', 'desc')->get();
        return view('deregistration.edit')->with('data', $data)->with('customers', $customers); // 返回編輯除戶記錄的視圖
    }

    public function update(Request $request, $id)
    {
        $data = Deregistration::findOrFail($id); // 獲取指定 ID 的除戶記錄
        $data->number = $request->input('number');
        $data->customer_id = $request->input('customer_id');
        $data->registrant = $request->input('registrant');
        $data->ic_card = $request->input('ic_card');
        $data->pet_name = $request->input('pet_name');
        $data->variety = $request->input('variety');
        $data->comment = $request->input('comment');
        $data->save(); // 更新除戶記錄

        return redirect()->route('deregistration.index')->with('success', '更新除戶記錄成功');
    }

    public function delete($id)
    {
        $data = Deregistration::findOrFail($id); // 獲取指定 ID 的除戶記錄
        $customers = Customer::orderby('created_at', 'desc')->get();
        return view('deregistration.del')->with('data', $data)->with('customers', $customers); // 返回編輯除戶記錄的視圖
    }

    public function destroy($id)
    {
        $data = Deregistration::findOrFail($id); // 獲取指定 ID 的除戶記錄
        $data->delete(); // 刪除除戶記錄

        return redirect()->route('deregistration.index')->with('success', '刪除除戶記錄成功');
    }
}

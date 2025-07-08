<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deregistration; // 確保引入 Deregistration 模型
use App\Models\Customer;

class DeregistrationController extends Controller
{
    public function index(Request $request)
    {
        $datas = Deregistration::paginate(50); // 獲取所有除戶資料        

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

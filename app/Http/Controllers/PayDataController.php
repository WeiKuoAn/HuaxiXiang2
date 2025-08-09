<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayData;
use App\Models\PayItem;
use App\Models\Pay;
use App\Models\Job;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Models\PayHistory;
use Illuminate\Support\Facades\DB;

class PayDataController extends Controller
{
    public function index(Request $request)
    {
        $pays = Pay::orderby('seq', 'asc')->get();
        if (Auth::user()->level == 2) {
            $users = User::where('status', '0')->whereNotIn('id', ['1', '10'])->get();
        } else {
            $users = User::where('status', '0')->get();
        }


        if ($request) {

            $status = $request->status;

            if ($status) {
                $datas = PayData::where('status',  $status);
            } else {
                $datas = PayData::where('status', 0);
            }

            // key單日期
            $after_date = $request->after_date;
            if ($after_date) {
                $datas =  $datas->where('pay_date', '>=', $after_date);
            }
            $before_date = $request->before_date;
            if ($before_date) {
                $datas =  $datas->where('pay_date', '<=', $before_date);
            }
            if ($after_date && $before_date) {
                $datas =  $datas->where('pay_date', '>=', $after_date)->where('pay_date', '<=', $before_date);
            }

            // User篩選
            $user = $request->user;
            if ($user != "null") {
                if (isset($user)) {
                    $datas =  $datas->where('user_id', $user);
                } else {
                    $datas =  $datas;
                }
            }

            if (Auth::user()->level == 2) {
                $datas = $datas->whereNotIn('user_id', ['1', '10']);
            }

            $comment = $request->comment;
            if ($comment) {
                $comment = '%' . $request->comment . '%';
                $datas =  $datas->where('comment', 'like', $comment);
            }

            // 先找出符合條件的 pay_item 的 pay_data_id
            $pay_items = PayItem::query();

            // 支出日期條件
            $pay_after_date = $request->pay_after_date;
            if ($pay_after_date) {
                $pay_items =  $pay_items->where('pay_date', '>=', $pay_after_date);
            }
            $pay_before_date = $request->pay_before_date;
            if ($pay_before_date) {
                $pay_items =  $pay_items->where('pay_date', '<=', $pay_before_date);
            }
            if ($pay_after_date && $pay_before_date) {
                $pay_items =  $pay_items->where('pay_date', '>=', $pay_after_date)->where('pay_date', '<=', $pay_before_date);
            }

            // pay篩選
            $pay = $request->pay;
            if ($pay != "null") {
                if (isset($pay)) {
                    $pay_items = $pay_items->where('pay_id', $pay);
                } else {
                    $datas =  $datas;
                }
            }

            // 獲取符合條件的 pay_data_id
            $pay_data_ids = $pay_items->pluck('pay_data_id')->toArray();

            // 使用 pay_data_id 來篩選 datas
            $datas = $datas->whereIn('id', $pay_data_ids)->orderby('pay_date', 'desc')->paginate(50);

            $condition = $request->all();
        } else {
            $datas = PayData::orderby('pay_date', 'desc')->paginate(50);
            $condition = '';
        }

        // 構建 pay_items 結構
        $pay_items = [];
        foreach ($datas as $data) {
            $items = PayItem::where('pay_data_id', $data->id);

            // 支出日期條件
            $pay_after_date = $request->pay_after_date;
            if ($pay_after_date) {
                $items =  $items->where('pay_date', '>=', $pay_after_date);
            }
            $pay_before_date = $request->pay_before_date;
            if ($pay_before_date) {
                $items =  $items->where('pay_date', '<=', $pay_before_date);
            }
            if ($pay_after_date && $pay_before_date) {
                $items =  $items->where('pay_date', '>=', $pay_after_date)->where('pay_date', '<=', $pay_before_date);
            }

            $pay = $request->pay;
            if ($pay != "null") {
                if (isset($pay)) {
                    $items = $items->where('pay_id', $pay);
                }
            }

            $pay_items[$data->id]['items'] = $items->get();
        }
        // dd($pay_items);
        // dd($pay_datas);
        return view('pay.index')->with('datas', $datas)->with('request', $request)
            ->with('pays', $pays)->with('users', $users)
            ->with('condition', $condition)->with('pay_items', $pay_items);
    }

    public function create()
    {
        //只取日期當數字
        $create_today = date('Y-m-d', strtotime(Carbon::now()->locale('zh-tw')));
        $today = date('Y-m-d', strtotime(Carbon::now()->locale('zh-tw')));
        $today = explode("-", $today);
        $today = $today[0] . $today[1] . $today[2];
        //查詢是否當日有無單號
        $data = PayData::orderby('pay_on', 'desc')->where('pay_on', 'like', $today . '%')->first();
        // dd(substr($data->pay_on,8,2));

        //單號自動計算
        if (!isset($data->pay_on)) {
            $i = 0;
        } else {
            //2023022201
            if (substr($data->pay_on, 8, 1) != 0) {
                $i = intval(substr($data->pay_on, 8, 2));
            } else {
                $i = intval(str_replace(0, '', substr($data->pay_on, 8, 2)));
            }
        }

        $i = $i + 1;

        if ($i <= 9) {
            $pay_on = $today . '0' . $i;
        } else {
            $pay_on = $today . $i;
        }

        // dd($pay_on);
        if (Auth::user()->job_id == 1 || Auth::user()->job_id == 2 || Auth::user()->job_id == 3 || Auth::user()->job_id == 7 || Auth::user()->job_id == 9) {
            $pays = Pay::where('status', 'up')->orderby('seq', 'asc')->get();
        } else {
            $pays = Pay::where('status', 'up')->where('view_status', '0')->orderby('seq', 'asc')->get();
        }

        return view('pay.create')->with('pays', $pays)->with('pay_on', $pay_on)->with('create_today', $create_today);
    }

    public function store(Request $request)
    {
        //只取日期當數字
        $today = date('Y-m-d', strtotime(Carbon::now()->locale('zh-tw')));
        $today = explode("-", $today);
        $today = $today[0] . $today[1] . $today[2];
        //查詢是否當日有無單號
        $data = PayData::orderby('pay_on', 'desc')->where('pay_on', 'like', $today . '%')->first();
        // dd(substr($data->pay_on,8,2));

        //單號自動計算
        if (!isset($data->pay_on)) {
            $i = 0;
        } else {
            //2023022201
            if (substr($data->pay_on, 8, 1) != 0) {
                $i = intval(substr($data->pay_on, 8, 2));
            } else {
                $i = intval(str_replace(0, '', substr($data->pay_on, 8, 2)));
            }
        }

        $i = $i + 1;

        if ($i <= 9) {
            $pay_on = $today . '0' . $i;
        } else {
            $pay_on = $today . $i;
        }
        $user = User::where('id', Auth::user()->id)->first();
        $PayData = new PayData();
        $PayData->pay_on = $pay_on;
        $PayData->pay_date = date('Y-m-d', strtotime(Carbon::now()->locale('zh-tw')));
        $PayData->price = $request->price;
        $PayData->comment = $request->comment;
        //是行政主管或行政就直接通過
        if ($user->job_id == '1' || $user->job_id == '2') {
            $PayData->status = 1;
        } else {
            $PayData->status = 0;
        }
        $PayData->user_id = Auth::user()->id;
        $PayData->save();

        $Pay_data_id = PayData::orderby('id', 'desc')->first();
        // dd($request->vender_id);
        if (isset($request->pay_data_date)) {
            foreach ($request->pay_data_date as $key => $data) {
                // dd($request->pay_text[$key]);
                $Pay_Item = new PayItem();
                $Pay_Item->pay_data_id = $Pay_data_id->id;
                $Pay_Item->pay_date = $request->pay_data_date[$key];
                $Pay_Item->pay_id = $request->pay_id[$key];
                $Pay_Item->price = $request->pay_price[$key];
                $Pay_Item->invoice_type = $request->pay_invoice_type[$key];
                if (isset($request->vender_id[$key])) {
                    $Pay_Item->vender_id = $request->vender_id[$key];
                } else {
                    $Pay_Item->vender_id = null;
                }
                if (isset($request->pay_invoice_number[$key])) {
                    $Pay_Item->invoice_number = $request->pay_invoice_number[$key];
                } else {
                    $Pay_Item->invoice_number = null;
                }
                if ($user->job_id == '1' || $user->job_id == '2') {
                    $Pay_Item->status = 1;
                } else {
                    $Pay_Item->status = 0;
                }
                $Pay_Item->comment = $request->pay_text[$key];
                $Pay_Item->save();
            }
        }

        //業務單軌跡-新增
        $sale_history = new PayHistory();
        $sale_history->pay_id = $Pay_data_id->id;
        $sale_history->user_id = Auth::user()->id;
        $sale_history->state = 'create';
        $sale_history->save();

        return redirect()->route('pay.create');
    }

    public function show($id)
    {
        $pays_name = Pay::where('status', 'up')->get();
        $data = PayData::where('id', $id)->first();
        $pays = Pay::where('status', 'up')->orderby('seq', 'asc')->get();
        // $pay_items = PayItem::where('pay_data_id',$id)->get();
        // dd(count($pay_items));
        return view('pay.edit')->with('pays', $pays)
            ->with('data', $data)
            ->with('pays_name', $pays_name);
    }


    public function update(Request $request, $id)
    {

        // dd($request->pay_data_date);

        $pay = PayData::where('id', $id)->first();
        $pay->pay_on = $request->pay_on;
        // $pay->pay_date = $request->pay_date;
        $pay->price = $request->price;
        $pay->comment = $request->comment;
        // $pay->user_id = Auth::user()->id;
        $pay->save();
        // dd($request->pay_invoice_number);
        PayItem::where('pay_data_id', $id)->delete();
        $user = User::where('id', Auth::user()->id)->first();
        if (isset($request->pay_data_date)) {
            foreach ($request->pay_data_date as $key => $data) {
                $Pay_Item = new PayItem();
                $Pay_Item->pay_data_id = $id;
                $Pay_Item->pay_id = $request->pay_id[$key];
                $Pay_Item->pay_date = $request->pay_data_date[$key];
                if (isset($request->pay_invoice_number[$key])) {
                    $Pay_Item->invoice_number = $request->pay_invoice_number[$key];
                } else {
                    $Pay_Item->invoice_number = null;
                }
                $Pay_Item->price = $request->pay_price[$key];
                $Pay_Item->invoice_type = $request->pay_invoice_type[$key];
                if (isset($request->vender_id[$key])) {
                    $Pay_Item->vender_id = $request->vender_id[$key];
                } else {
                    $Pay_Item->vender_id = null;
                }
                //權限修改問題
                if ($user->job_id == '1' || $user->job_id == '2' || $user->job_id == '7' || $user->job_id == '9') {
                    $Pay_Item->status = 1;
                } else {
                    $Pay_Item->status = 0;
                }
                $Pay_Item->comment = $request->pay_text[$key];
                $Pay_Item->save();
            }
        }

        //業務單軌跡-編輯
        if ($pay->user_id == Auth::user()->id) {
            $sale_history = new PayHistory();
            $sale_history->pay_id = $id;
            $sale_history->user_id = Auth::user()->id;
            $sale_history->state = 'update';
            $sale_history->save();
        } else {
            $sale_history = new PayHistory();
            $sale_history->pay_id = $id;
            $sale_history->user_id = Auth::user()->id;
            $sale_history->state = 'other_user_update';
            $sale_history->save();
        }

        if (Auth::user()->level != 2 || Auth::user()->job_id == '9') {
            return redirect()->route('pays');
        } else {
            return redirect()->route('person.pays');
        }
    }


    public function check($id)
    {
        $pays = Pay::where('status', 'up')->orderby('seq', 'asc')->get();
        $data = PayData::where('id', $id)->first();
        return view('pay.check')->with('data', $data)->with('pays', $pays);
    }

    public function check_data(Request $request, $id)
    {
        $data = PayData::where('id', $id)->first();
        $items = PayItem::where('pay_data_id', $id)->get();
        if (isset($request)) {
            // dd($request);
            if ($request->submit1 == 'true') {
                $data->status = 1;
                $data->save();
                foreach ($items as $item) {
                    $item->status = 1;
                    $item->save();
                }
                //業務單軌跡-確定審核
                $sale_history = new PayHistory();
                $sale_history->pay_id = $id;
                $sale_history->user_id = Auth::user()->id;
                $sale_history->state = 'check';
                $sale_history->save();
            } else {
                $data->status = 0;
                $data->save();
                foreach ($items as $item) {
                    $item->status = 0;
                    $item->save();
                }
                //業務單軌跡-未審核
                $sale_history = new PayHistory();
                $sale_history->pay_id = $id;
                $sale_history->user_id = Auth::user()->id;
                $sale_history->state = 'not_check';
                $sale_history->save();
            }
        }
        return redirect()->route('pays');
    }


    public function delshow($id)
    {
        $pays_name = Pay::where('status', 'up')->get();
        $data = PayData::where('id', $id)->first();
        $pays = Pay::where('status', 'up')->orderby('seq', 'asc')->get();
        return view('pay.del')->with('pays', $pays)
            ->with('pays_name', $pays_name)
            ->with('data', $data);
    }

    public function delete(Request $request, $id)
    {
        $pay = PayData::where('id', $id)->first();
        $pay->delete();

        $pay_items = PayItem::where('pay_data_id', $id)->get();
        foreach ($pay_items as $item) {
            $item->delete();
        }
        return redirect()->route('pays');
    }

    public function user_pay($id, Request $request)
    {
        $user = User::where('id', $id)->first();
        if ($request) {
            $status = $request->status;
            if ($status) {
                $datas = PayData::where('status',  $status);
                $sum_pay = PayData::where('status', $status);
            } else {
                $datas = PayData::where('status', 0);
                $sum_pay = PayData::where('status', 0);
            }
            $after_date = $request->after_date;
            if ($after_date) {
                $datas =  $datas->where('pay_date', '>=', $after_date);
                $sum_pay  = $sum_pay->where('pay_date', '>=', $after_date);
            }
            $before_date = $request->before_date;
            if ($before_date) {
                $datas =  $datas->where('pay_date', '<=', $before_date);
                $sum_pay  = $sum_pay->where('pay_date', '<=', $before_date);
            }
            if ($after_date && $before_date) {
                $datas =  $datas->where('pay_date', '>=', $after_date)->where('pay_date', '<=', $before_date);
                $sum_pay  = $sum_pay->where('pay_date', '>=', $after_date)->where('pay_date', '<=', $before_date);
            }
            $pay = $request->pay;
            if ($pay != "null") {
                if (isset($pay)) {
                    $datas =  $datas->where('pay_id', $pay);
                    $sum_pay  = $sum_pay->where('pay_id', $pay);
                } else {
                    $datas = $datas;
                    $sum_pay  = $sum_pay;
                }
            }
            $sum_pay  = $sum_pay->sum('price');
            $datas = $datas->orderby('pay_date', 'desc')->where('user_id', $id)->paginate(50);
            $condition = $request->all();
        } else {
            $datas = PayData::orderby('pay_date', 'desc')->where('user_id', $id)->paginate(50);
            $sum_pay  = PayData::sum('price');
            $condition = '';
        }
        return view('pay.user_index')->with('datas', $datas)->with('request', $request)->with('user', $user)->with('condition', $condition)
            ->with('sum_pay', $sum_pay);
    }

    public function history($id)
    {
        $pay_data = PayData::where('id', $id)->first();
        $datas = PayHistory::where('pay_id', $id)->get();
        return view('pay.history')->with('pay_data', $pay_data)->with('datas', $datas);
    }

    /**
     * 匯出支出資料為 CSV
     */
    public function export(Request $request)
    {
        // 驗證請求
        $request->validate([
            'columns' => 'required|array|min:1',
            'columns.*' => 'in:pay_date,pay_on,item_pay_date,pay_name,invoice_number,item_price,total_price,comment,user_name,status'
        ], [
            'columns.required' => '請至少選擇一個要匯出的欄位',
            'columns.min' => '請至少選擇一個要匯出的欄位',
            'columns.*.in' => '選擇的欄位不正確'
        ]);

        try {
            // 準備篩選條件
            $filters = $request->only([
                'after_date', 'before_date', 
                'pay_after_date', 'pay_before_date',
                'comment', 'pay', 'user', 'status'
            ]);

            // 獲取選擇的欄位
            $selectedColumns = $request->input('columns', []);

            // 欄位對應
            $columnMappings = [
                'pay_date' => 'Key單日期',
                'pay_on' => 'Key單單號',
                'item_pay_date' => '支出日期',
                'pay_name' => '支出科目',
                'invoice_number' => '發票號碼',
                'item_price' => '單項支出金額',
                'total_price' => '支出總價格',
                'comment' => '備註',
                'user_name' => 'Key單人員',
                'status' => '審核狀態'
            ];

            // 取得資料
            $query = PayData::with(['pay_items.pay_name', 'user_name']);
            $this->applyFilters($query, $filters);
            $payDatas = $query->orderBy('pay_date', 'desc')->get();

            // 生成檔案名稱
            $fileName = '支出資料_' . date('Y-m-d_H-i-s') . '.csv';

            // 建立 CSV 內容
            return response()->streamDownload(function() use ($payDatas, $selectedColumns, $columnMappings) {
                $handle = fopen('php://output', 'w');
                
                // 加入 BOM 以正確顯示中文
                fwrite($handle, "\xEF\xBB\xBF");
                
                // 寫入標題列
                $headers = [];
                foreach ($selectedColumns as $column) {
                    $headers[] = isset($columnMappings[$column]) ? $columnMappings[$column] : $column;
                }
                fputcsv($handle, $headers);
                
                // 寫入資料列
                foreach ($payDatas as $payData) {
                    $payItems = $payData->pay_items;
                    
                    if ($payItems->count() > 0) {
                        // 如果有支出項目，每個項目一行
                        $isFirstItem = true;
                        foreach ($payItems as $item) {
                            $row = $this->buildCsvRow($payData, $item, $selectedColumns, $isFirstItem);
                            fputcsv($handle, $row);
                            $isFirstItem = false; // 第一筆後都設為 false
                        }
                    } else {
                        // 如果沒有支出項目，只顯示主要資料
                        $row = $this->buildCsvRow($payData, null, $selectedColumns, true);
                        fputcsv($handle, $row);
                    }
                }
                
                fclose($handle);
            }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);

        } catch (\Exception $e) {
            // 錯誤處理
            return redirect()->back()
                ->with('error', '匯出失敗：' . $e->getMessage());
        }
    }

    /**
     * 建立 CSV 行資料
     */
        private function buildCsvRow($payData, $payItem = null, $selectedColumns = [], $isFirstItem = true)
    {
        $fullData = [
            'pay_date' => $isFirstItem ? $payData->pay_date : '',
            'pay_on' => $isFirstItem ? $payData->pay_on : '',
            'item_pay_date' => $payItem ? $payItem->pay_date : '',
            'pay_name' => $payItem && $payItem->pay_name ? $payItem->pay_name->name : '',
            'invoice_number' => $payItem ? $payItem->invoice_number : '',
            'item_price' => $payItem ? number_format($payItem->price) : '',
            'total_price' => $isFirstItem ? number_format($payData->price) : '',
            'comment' => $isFirstItem ? $payData->comment : '',
            'user_name' => $isFirstItem ? ($payData->user_name ? $payData->user_name->name : '') : '',
            'status' => $isFirstItem ? ($payData->status == 1 ? '已審核' : '未審核') : ''
        ];

        $row = [];
        foreach ($selectedColumns as $column) {
            $row[] = isset($fullData[$column]) ? $fullData[$column] : '';
        }
        
        return $row;
    }

    /**
     * 套用篩選條件
     */
    private function applyFilters($query, $filters)
    {
        // 狀態篩選
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            $query->where('status', 0);
        }

        // Key單日期範圍
        if (isset($filters['after_date']) && $filters['after_date']) {
            $query->where('pay_date', '>=', $filters['after_date']);
        }
        if (isset($filters['before_date']) && $filters['before_date']) {
            $query->where('pay_date', '<=', $filters['before_date']);
        }

        // 支付類別篩選
        if (isset($filters['pay']) && $filters['pay'] != "null" && $filters['pay']) {
            $query->where('pay_id', $filters['pay']);
        }

        // 使用者篩選
        if (isset($filters['user']) && $filters['user'] != "null" && $filters['user']) {
            $query->where('user_id', $filters['user']);
        }

        // 備註篩選
        if (isset($filters['comment']) && $filters['comment']) {
            $query->where('comment', 'like', '%' . $filters['comment'] . '%');
        }

        // 支出日期範圍篩選（需要透過 pay_items 關聯）
        if ((isset($filters['pay_after_date']) && $filters['pay_after_date']) || 
            (isset($filters['pay_before_date']) && $filters['pay_before_date'])) {
            
            $query->whereHas('pay_items', function($q) use ($filters) {
                if (isset($filters['pay_after_date']) && $filters['pay_after_date']) {
                    $q->where('pay_date', '>=', $filters['pay_after_date']);
                }
                if (isset($filters['pay_before_date']) && $filters['pay_before_date']) {
                    $q->where('pay_date', '<=', $filters['pay_before_date']);
                }
            });
        }
    }
}

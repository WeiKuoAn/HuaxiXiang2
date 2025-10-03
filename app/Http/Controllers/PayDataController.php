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

            if ($status !== null && $status !== '') {
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
        // 如果原本是退回狀態，編輯後重置為待審核
        if ($pay->status == 2) {
            $pay->status = 0;
        }
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
                //權限修改問題 - 如果原本是退回狀態，編輯後重置為待審核
                if ($pay->status == 0) {
                    $Pay_Item->status = 0;
                } else {
                    if ($user->job_id == '1' || $user->job_id == '2' || $user->job_id == '7' || $user->job_id == '9') {
                        $Pay_Item->status = 1;
                    } else {
                        $Pay_Item->status = 0;
                    }
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
            } elseif ($request->submit1 == 'return') {
                $data->status = 2;
                $data->save();
                foreach ($items as $item) {
                    $item->status = 2;
                    $item->save();
                }
                //業務單軌跡-退回
                $sale_history = new PayHistory();
                $sale_history->pay_id = $id;
                $sale_history->user_id = Auth::user()->id;
                $sale_history->state = 'return';
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
     * 匯出支出資料為 XLSX
     */
    public function export(Request $request)
    {
        // 驗證請求
        $request->validate([
            'columns' => 'required|array|min:1',
            'columns.*' => 'in:pay_date,pay_on,item_pay_date,pay_name,invoice_type,invoice_number,item_price,item_comment,total_price,comment,user_name,status,check_user'
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
                'invoice_type' => '發票類型',
                'invoice_number' => '發票號碼',
                'item_price' => '單項支出金額',
                'item_comment' => '單項支出備註',
                'total_price' => '支出總價格',
                'comment' => '備註',
                'user_name' => 'Key單人員',
                'status' => '審核狀態',
                'check_user' => '審核人'
            ];

            // 取得資料
            $query = PayData::with(['pay_items.pay_name', 'user_name', 'pay_history.user_name']);
            $this->applyFilters($query, $filters);
            $payDatas = $query->orderBy('pay_date', 'desc')->get();

            // 生成檔案名稱
            $fileName = '支出資料_' . date('Y-m-d_H-i-s') . '.xlsx';

            // 建立 XLSX 內容
            return response()->streamDownload(function() use ($payDatas, $selectedColumns, $columnMappings) {
                $this->generateXlsx($payDatas, $selectedColumns, $columnMappings);
            }, $fileName, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);

        } catch (\Exception $e) {
            // 錯誤處理
            return redirect()->back()
                ->with('error', '匯出失敗：' . $e->getMessage());
        }
    }

    /**
     * 生成 XLSX 檔案
     */
    private function generateXlsx($payDatas, $selectedColumns, $columnMappings)
    {
        // 建立 ZIP 檔案
        $zip = new \ZipArchive();
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_');
        $zip->open($tempFile, \ZipArchive::CREATE);

        // 準備工作表資料
        $worksheetData = $this->prepareWorksheetData($payDatas, $selectedColumns, $columnMappings);
        
        // 建立必要的檔案
        $this->createContentTypes($zip);
        $this->createRels($zip);
        $this->createWorkbookRels($zip);
        $this->createWorkbook($zip);
        $this->createWorksheet($zip, $worksheetData);
        $this->createStyles($zip);
        $this->createTheme($zip); // 新增主題檔案

        $zip->close();

        // 輸出檔案內容
        readfile($tempFile);
        unlink($tempFile);
    }

    /**
     * 準備工作表資料
     */
    private function prepareWorksheetData($payDatas, $selectedColumns, $columnMappings)
    {
        $data = [];
        $mergeCells = [];
        $currentRow = 2; // 從第2行開始（第1行是標題）

        // 標題列
        $headers = [];
        foreach ($selectedColumns as $column) {
            $headers[] = isset($columnMappings[$column]) ? $columnMappings[$column] : $column;
        }
        $data[] = $headers;

        foreach ($payDatas as $payData) {
            $payItems = $payData->pay_items;
            $startRow = $currentRow;
            
            if ($payItems->count() > 0) {
                // 如果有支出項目，每個項目一行
                $isFirstItem = true;
                foreach ($payItems as $item) {
                    $row = $this->buildXlsxRow($payData, $item, $selectedColumns, $isFirstItem);
                    $data[] = $row;
                    $currentRow++;
                    $isFirstItem = false;
                }
                
                // 記錄需要合併的儲存格
                if ($currentRow - $startRow > 1) {
                    $this->addMergeCells($mergeCells, $startRow, $currentRow - 1, $selectedColumns);
                }
            } else {
                // 如果沒有支出項目，只顯示主要資料
                $row = $this->buildXlsxRow($payData, null, $selectedColumns, true);
                $data[] = $row;
                $currentRow++;
            }
        }

        return [
            'data' => $data,
            'mergeCells' => $mergeCells
        ];
    }

    /**
     * 建立 XLSX 行資料
     */
    private function buildXlsxRow($payData, $payItem = null, $selectedColumns = [], $isFirstItem = true)
    {
        $fullData = [
            'pay_date' => $isFirstItem ? $payData->pay_date : '',
            'pay_on' => $isFirstItem ? $payData->pay_on : '',
            'item_pay_date' => $payItem ? $payItem->pay_date : '',
            'pay_name' => $payItem && $payItem->pay_name ? $payItem->pay_name->name : '',
            'invoice_type' => $payItem ? $this->getInvoiceTypeName($payItem->invoice_type) : '',
            'invoice_number' => $payItem ? $payItem->invoice_number : '',
            'item_price' => $payItem ? (float)$payItem->price : '',
            'item_comment' => $payItem ? $payItem->comment : '',
            'total_price' => $isFirstItem ? (float)$payData->price : '',
            'comment' => $isFirstItem ? $payData->comment : '',
            'user_name' => $isFirstItem ? ($payData->user_name ? $payData->user_name->name : '') : '',
            'status' => $isFirstItem ? ($payData->status == 1 ? '已審核' : '未審核') : '',
            'check_user' => $isFirstItem ? ($payData->pay_history->where('state', 'check')->last() ? $payData->pay_history->where('state', 'check')->last()->user_name->name : '') : ''
        ];

        $row = [];
        foreach ($selectedColumns as $column) {
            $row[] = isset($fullData[$column]) ? $fullData[$column] : '';
        }
        
        return $row;
    }

    /**
     * 新增合併儲存格
     */
    private function addMergeCells(&$mergeCells, $startRow, $endRow, $selectedColumns)
    {
        // 需要合併的欄位索引（從0開始）
        $mergeColumnIndexes = [];
        
        foreach ($selectedColumns as $index => $column) {
            if (in_array($column, ['pay_date', 'pay_on', 'total_price', 'comment', 'user_name', 'status', 'check_user'])) {
                $mergeColumnIndexes[] = $index;
            }
        }

        foreach ($mergeColumnIndexes as $colIndex) {
            $colLetter = $this->numberToLetter($colIndex + 1);
            $mergeCells[] = $colLetter . $startRow . ':' . $colLetter . $endRow;
        }
    }

    /**
     * 數字轉欄位字母
     */
    private function numberToLetter($number)
    {
        $letter = '';
        while ($number > 0) {
            $number--;
            $letter = chr(65 + ($number % 26)) . $letter;
            $number = intval($number / 26);
        }
        return $letter;
    }

    /**
     * 發票類型轉換
     */
    private function getInvoiceTypeName($invoiceType)
    {
        switch ($invoiceType) {
            case 'FreeUniform':
                return '免用統一發票';
            case 'Uniform':
                return '統一發票';
            case 'Other':
                return '其他';
            default:
                return $invoiceType; // 預設顯示原始值
        }
    }

    /**
     * 建立 Content Types
     */
    private function createContentTypes($zip)
    {
        $content = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
    <Override PartName="/xl/theme/theme1.xml" ContentType="application/vnd.openxmlformats-officedocument.theme+xml"/>
</Types>';
        $zip->addFromString('[Content_Types].xml', $content);
    }

    /**
     * 建立 Relationships
     */
    private function createRels($zip)
    {
        $content = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
        $zip->addFromString('_rels/.rels', $content);
    }

    /**
     * 建立 Workbook Relationships
     */
    private function createWorkbookRels($zip)
    {
        $content = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
    <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme" Target="theme/theme1.xml"/>
</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $content);
    }

    /**
     * 建立 Workbook
     */
    private function createWorkbook($zip)
    {
        $content = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <fileVersion appName="xl" lastEdited="6" lowestEdited="6" rupBuild="14420"/>
    <workbookPr defaultThemeVersion="164011"/>
    <sheets>
        <sheet name="支出資料" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $content);
    }

    /**
     * 建立 Worksheet
     */
    private function createWorksheet($zip, $worksheetData)
    {
        $data = $worksheetData['data'];
        $mergeCells = $worksheetData['mergeCells'];

        $sheetData = '';
        foreach ($data as $rowIndex => $row) {
            $sheetData .= '<row r="' . ($rowIndex + 1) . '">';
            foreach ($row as $colIndex => $cell) {
                $colLetter = $this->numberToLetter($colIndex + 1);
                $cellRef = $colLetter . ($rowIndex + 1);
                
                // 檢查是否為數字（包括浮點數）
                if (is_numeric($cell) && $cell !== '') {
                    $sheetData .= '<c r="' . $cellRef . '" t="n"><v>' . $cell . '</v></c>';
                } else {
                    $sheetData .= '<c r="' . $cellRef . '" t="inlineStr"><is><t>' . htmlspecialchars($cell) . '</t></is></c>';
                }
            }
            $sheetData .= '</row>';
        }

        $mergeCellsXml = '';
        if (!empty($mergeCells)) {
            $mergeCellsXml = '<mergeCells count="' . count($mergeCells) . '">';
            foreach ($mergeCells as $mergeCell) {
                $mergeCellsXml .= '<mergeCell ref="' . $mergeCell . '"/>';
            }
            $mergeCellsXml .= '</mergeCells>';
        }

        $content = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <dimension ref="A1:' . $this->numberToLetter(count($data[0])) . count($data) . '"/>
    <sheetViews>
        <sheetView workbookViewId="0"/>
    </sheetViews>
    <sheetFormatPr defaultRowHeight="15"/>
    <sheetData>' . $sheetData . '</sheetData>
    ' . $mergeCellsXml . '
    <pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>
</worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $content);
    }

    /**
     * 建立 Styles
     */
    private function createStyles($zip)
    {
        $content = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <numFmts count="1">
        <numFmt numFmtId="0" formatCode="General"/>
    </numFmts>
    <fonts count="1">
        <font>
            <name val="Calibri"/>
            <family val="2"/>
            <sz val="11"/>
            <color theme="1"/>
        </font>
    </fonts>
    <fills count="2">
        <fill>
            <patternFill patternType="none"/>
        </fill>
        <fill>
            <patternFill patternType="gray125"/>
        </fill>
    </fills>
    <borders count="1">
        <border>
            <left/>
            <right/>
            <top/>
            <bottom/>
            <diagonal/>
        </border>
    </borders>
    <cellStyleXfs count="1">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
    </cellStyleXfs>
    <cellXfs count="1">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    </cellXfs>
    <cellStyles count="1">
        <cellStyle name="Normal" xfId="0" builtinId="0"/>
    </cellStyles>
    <tableStyles count="0" defaultTableStyle="TableStyleMedium2" defaultPivotStyle="PivotStyleLight16"/>
</styleSheet>';
        $zip->addFromString('xl/styles.xml', $content);
    }

    /**
     * 建立 Theme
     */
    private function createTheme($zip)
    {
        $content = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<a:theme xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" name="Office Theme">
    <a:themeElements>
        <a:clrScheme name="Office">
            <a:dk1>
                <a:srgbClr val="000000"/>
            </a:dk1>
            <a:lt1>
                <a:srgbClr val="FFFFFF"/>
            </a:lt1>
            <a:dk2>
                <a:srgbClr val="1F497D"/>
            </a:dk2>
            <a:lt2>
                <a:srgbClr val="EEECE1"/>
            </a:lt2>
            <a:accent1>
                <a:srgbClr val="4F81BD"/>
            </a:accent1>
            <a:accent2>
                <a:srgbClr val="C0504D"/>
            </a:accent2>
            <a:accent3>
                <a:srgbClr val="9BBB59"/>
            </a:accent3>
            <a:accent4>
                <a:srgbClr val="8064A2"/>
            </a:accent4>
            <a:accent5>
                <a:srgbClr val="4BACC6"/>
            </a:accent5>
            <a:accent6>
                <a:srgbClr val="F79646"/>
            </a:accent6>
            <a:hlink>
                <a:srgbClr val="0000FF"/>
            </a:hlink>
            <a:folHlink>
                <a:srgbClr val="800080"/>
            </a:folHlink>
        </a:clrScheme>
        <a:fontScheme name="Office">
            <a:majorFont>
                <a:latin typeface="Calibri"/>
                <a:ea typeface=""/>
                <a:cs typeface=""/>
            </a:majorFont>
            <a:minorFont>
                <a:latin typeface="Calibri"/>
                <a:ea typeface=""/>
                <a:cs typeface=""/>
            </a:minorFont>
        </a:fontScheme>
        <a:fmtScheme name="Office">
            <a:fillStyleLst>
                <a:solidFill>
                    <a:schemeClr val="phClr"/>
                </a:solidFill>
                <a:gradFill rotWithShape="1">
                    <a:gsLst>
                        <a:gs pos="0">
                            <a:schemeClr val="phClr">
                                <a:tint val="50000"/>
                                <a:satMod val="300000"/>
                            </a:schemeClr>
                        </a:gs>
                        <a:gs pos="35000">
                            <a:schemeClr val="phClr">
                                <a:tint val="37000"/>
                                <a:satMod val="300000"/>
                            </a:schemeClr>
                        </a:gs>
                        <a:gs pos="100000">
                            <a:schemeClr val="phClr">
                                <a:tint val="15000"/>
                                <a:satMod val="350000"/>
                            </a:schemeClr>
                        </a:gs>
                    </a:gsLst>
                    <a:lin ang="16200000" scaled="1"/>
                </a:gradFill>
                <a:gradFill rotWithShape="1">
                    <a:gsLst>
                        <a:gs pos="0">
                            <a:schemeClr val="phClr">
                                <a:satMod val="310000"/>
                                <a:lumMod val="120000"/>
                                <a:tint val="40000"/>
                            </a:schemeClr>
                        </a:gs>
                        <a:gs pos="40000">
                            <a:schemeClr val="phClr">
                                <a:satMod val="310000"/>
                                <a:lumMod val="120000"/>
                                <a:tint val="40000"/>
                            </a:schemeClr>
                        </a:gs>
                        <a:gs pos="70000">
                            <a:schemeClr val="phClr">
                                <a:satMod val="310000"/>
                                <a:lumMod val="120000"/>
                                <a:shade val="80000"/>
                            </a:schemeClr>
                        </a:gs>
                        <a:gs pos="100000">
                            <a:schemeClr val="phClr">
                                <a:satMod val="310000"/>
                                <a:lumMod val="120000"/>
                                <a:shade val="80000"/>
                            </a:schemeClr>
                        </a:gs>
                    </a:gsLst>
                    <a:path path="circle">
                        <a:fillToRect l="50000" t="-80000" r="50000" b="180000"/>
                    </a:path>
                </a:gradFill>
            </a:fillStyleLst>
            <a:lnStyleLst>
                <a:ln w="9525" cap="flat" cmpd="sng" algn="ctr">
                    <a:solidFill>
                        <a:schemeClr val="phClr">
                            <a:shade val="95000"/>
                            <a:satMod val="105000"/>
                        </a:schemeClr>
                    </a:solidFill>
                    <a:prstDash val="solid"/>
                </a:ln>
                <a:ln w="25400" cap="flat" cmpd="sng" algn="ctr">
                    <a:solidFill>
                        <a:schemeClr val="phClr"/>
                    </a:solidFill>
                    <a:prstDash val="solid"/>
                </a:ln>
                <a:ln w="38100" cap="flat" cmpd="sng" algn="ctr">
                    <a:solidFill>
                        <a:schemeClr val="phClr"/>
                    </a:solidFill>
                    <a:prstDash val="solid"/>
                </a:ln>
            </a:lnStyleLst>
            <a:effectStyleLst>
                <a:effectStyle>
                    <a:effectLst>
                        <a:outerShdw blur="57150" dist="19050" dir="5400000" algn="ctr" rotWithShape="0">
                            <a:srgbClr val="000000">
                                <a:alpha val="63000"/>
                            </a:srgbClr>
                        </a:outerShdw>
                    </a:effectLst>
                </a:effectStyle>
                <a:effectStyle>
                    <a:effectLst>
                        <a:outerShdw blur="57150" dist="19050" dir="5400000" algn="ctr" rotWithShape="0">
                            <a:srgbClr val="000000">
                                <a:alpha val="63000"/>
                            </a:srgbClr>
                        </a:outerShdw>
                    </a:effectLst>
                </a:effectStyle>
                <a:effectStyle>
                    <a:effectLst>
                        <a:outerShdw blur="57150" dist="19050" dir="5400000" algn="ctr" rotWithShape="0">
                            <a:srgbClr val="000000">
                                <a:alpha val="63000"/>
                            </a:srgbClr>
                        </a:outerShdw>
                    </a:effectLst>
                </a:effectStyle>
            </a:effectStyleLst>
            <a:bgFillStyleLst>
                <a:solidFill>
                    <a:schemeClr val="phClr"/>
                </a:solidFill>
                <a:gradFill rotWithShape="1">
                    <a:gsLst>
                        <a:gs pos="0">
                            <a:schemeClr val="phClr">
                                <a:tint val="40000"/>
                                <a:satMod val="350000"/>
                            </a:schemeClr>
                        </a:gs>
                        <a:gs pos="40000">
                            <a:schemeClr val="phClr">
                                <a:tint val="45000"/>
                                <a:satMod val="350000"/>
                                <a:shade val="99000"/>
                            </a:schemeClr>
                        </a:gs>
                        <a:gs pos="100000">
                            <a:schemeClr val="phClr">
                                <a:shade val="20000"/>
                                <a:satMod val="255000"/>
                            </a:schemeClr>
                        </a:gs>
                    </a:gsLst>
                    <a:path path="circle">
                        <a:fillToRect l="50000" t="50000" r="50000" b="50000"/>
                    </a:path>
                </a:gradFill>
                <a:gradFill rotWithShape="1">
                    <a:gsLst>
                        <a:gs pos="0">
                            <a:schemeClr val="phClr">
                                <a:tint val="80000"/>
                                <a:satMod val="300000"/>
                            </a:schemeClr>
                        </a:gs>
                        <a:gs pos="100000">
                            <a:schemeClr val="phClr">
                                <a:shade val="30000"/>
                                <a:satMod val="200000"/>
                            </a:schemeClr>
                        </a:gs>
                    </a:gsLst>
                    <a:path path="circle">
                        <a:fillToRect l="50000" t="50000" r="50000" b="50000"/>
                    </a:path>
                </a:gradFill>
            </a:bgFillStyleLst>
        </a:fmtScheme>
    </a:themeElements>
    <a:objectDefaults/>
    <a:extraClrSchemeLst/>
</a:theme>';
        $zip->addFromString('xl/theme/theme1.xml', $content);
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
            $query->whereHas('pay_items', function($q) use ($filters) {
                $q->where('pay_id', $filters['pay']);
            });
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

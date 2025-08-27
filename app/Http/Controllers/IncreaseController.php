<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Increase;
use App\Models\IncreaseItem;
use App\Models\IncreaseSetting;
use App\Models\User;

class IncreaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Increase::with(['items.phonePerson', 'items.receivePerson', 'creator'])
            ->orderBy('increase_date', 'desc');

        // 日期篩選
        if ($request->filled('start_date')) {
            $query->where('increase_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('increase_date', '<=', $request->end_date);
        }

        $datas = $query->paginate(50);
            
        return view('increase.index', compact('datas'));
    }

    public function create()
    {
        $users = User::where('status', '0')->orderby('level')->orderby('seq')->whereNotIn('job_id', [1,4,8,9,6,11])->get();
        return view('increase.create', compact('users'));
    }

    public function store(Request $request)
    {
        // 驗證輸入
        $request->validate([
            'increase_date' => 'required|date',
            'comment' => 'nullable|string',
            'increase' => 'required|array',
            'increase.*.categories' => 'required|array|min:1',
            'increase.*.phone_person' => 'nullable|exists:users,id',
            'increase.*.receive_person' => 'nullable|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // 建立加成主檔
            $increase = Increase::create([
                'increase_date' => $request->increase_date,
                'comment' => $request->comment,
                'created_by' => Auth::id(),
            ]);

            // 取得加成設定
            $settings = IncreaseSetting::getActiveSettings();

            // 處理加成項目
            foreach ($request->increase as $itemData) {
                $phoneAmount = 0;
                $receiveAmount = 0;

                // 計算各類別金額
                foreach ($itemData['categories'] as $category) {
                    if (isset($settings[$category])) {
                        $phoneAmount += $settings[$category]->phone_bonus;
                        $receiveAmount += $settings[$category]->receive_bonus;
                    }
                }

                // 建立加成項目
                $increaseItem = new IncreaseItem([
                    'increase_id' => $increase->id,
                    'phone_person_id' => $itemData['phone_person'] ?? null,
                    'receive_person_id' => $itemData['receive_person'] ?? null,
                ]);

                // 設定各類別金額
                foreach ($itemData['categories'] as $category) {
                    if (isset($settings[$category])) {
                        switch ($category) {
                            case 'night':
                                $increaseItem->night_phone_amount = $settings[$category]->phone_bonus;
                                $increaseItem->night_receive_amount = $settings[$category]->receive_bonus;
                                break;
                            case 'evening':
                                $increaseItem->evening_phone_amount = $settings[$category]->phone_bonus;
                                $increaseItem->evening_receive_amount = $settings[$category]->receive_bonus;
                                break;
                            case 'typhoon':
                                $increaseItem->typhoon_phone_amount = $settings[$category]->phone_bonus;
                                $increaseItem->typhoon_receive_amount = $settings[$category]->receive_bonus;
                                break;
                        }
                    }
                }

                // 計算總金額
                $increaseItem->calculateTotalAmount();
                $increaseItem->save();
            }

            DB::commit();

            return redirect()->route('increase.index')->with('success', '加成記錄建立成功！');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', '建立失敗：' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $increase = Increase::with(['items.phonePerson', 'items.receivePerson'])->findOrFail($id);
        $users = User::where('status', '0')->orderby('level')->orderby('seq')->whereNotIn('job_id', [1,4,8,9,6,11])->get();
        
        return view('increase.edit', compact('increase', 'users'));
    }

    public function update(Request $request, $id)
    {
        // 驗證輸入
        $request->validate([
            'increase_date' => 'required|date',
            'comment' => 'nullable|string',
            'increase' => 'required|array',
            'increase.*.categories' => 'required|array|min:1',
            'increase.*.phone_person' => 'nullable|exists:users,id',
            'increase.*.receive_person' => 'nullable|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // 更新加成主檔
            $increase = Increase::findOrFail($id);
            $increase->update([
                'increase_date' => $request->increase_date,
                'comment' => $request->comment,
            ]);

            // 刪除現有的加成項目
            $increase->items()->delete();

            // 取得加成設定
            $settings = IncreaseSetting::getActiveSettings();

            // 處理新的加成項目
            foreach ($request->increase as $itemData) {
                // 建立加成項目
                $increaseItem = new IncreaseItem([
                    'increase_id' => $increase->id,
                    'phone_person_id' => $itemData['phone_person'] ?? null,
                    'receive_person_id' => $itemData['receive_person'] ?? null,
                ]);

                // 設定各類別金額
                foreach ($itemData['categories'] as $category) {
                    if (isset($settings[$category])) {
                        switch ($category) {
                            case 'night':
                                $increaseItem->night_phone_amount = $settings[$category]->phone_bonus;
                                $increaseItem->night_receive_amount = $settings[$category]->receive_bonus;
                                break;
                            case 'evening':
                                $increaseItem->evening_phone_amount = $settings[$category]->phone_bonus;
                                $increaseItem->evening_receive_amount = $settings[$category]->receive_bonus;
                                break;
                            case 'typhoon':
                                $increaseItem->typhoon_phone_amount = $settings[$category]->phone_bonus;
                                $increaseItem->typhoon_receive_amount = $settings[$category]->receive_bonus;
                                break;
                        }
                    }
                }

                // 計算總金額
                $increaseItem->calculateTotalAmount();
                $increaseItem->save();
            }

            DB::commit();

            return redirect()->route('increase.index')->with('success', '加成記錄更新成功！');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', '更新失敗：' . $e->getMessage())->withInput();
        }
    }
    
    public function delete($id)
    {
        $increase = Increase::with(['items.phonePerson', 'items.receivePerson', 'creator'])->findOrFail($id);
        return view('increase.delete', compact('increase'));
    }

    public function destroy($id)
    {
        try {
            $increase = Increase::findOrFail($id);
            $increase->delete(); // 會自動刪除關聯的 items
            
            return redirect()->route('increase.index')->with('success', '加成記錄刪除成功！');
        } catch (\Exception $e) {
            return back()->with('error', '刪除失敗：' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        // 取得查詢參數
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // 查詢資料
        $query = Increase::with(['items.phonePerson', 'items.receivePerson'])
            ->orderBy('increase_date', 'asc');

        if ($startDate) {
            $query->where('increase_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('increase_date', '<=', $endDate);
        }

        $increases = $query->get();

        // 準備 Excel 資料
        $excelData = [];
        $monthlyStats = [];
        $dailyStats = [];

        foreach ($increases as $increase) {
            $date = $increase->increase_date->format('Y-m-d');
            $month = $increase->increase_date->format('Y-m');
            
            // 初始化月度統計
            if (!isset($monthlyStats[$month])) {
                $monthlyStats[$month] = [];
            }

            // 初始化日統計
            if (!isset($dailyStats[$date])) {
                $dailyStats[$date] = [];
            }

            foreach ($increase->items as $item) {
                // 處理接電話人員
                if ($item->phone_person_id) {
                    $personName = $item->phonePerson->name ?? '未指定';
                    
                    // 月度統計
                    if (!isset($monthlyStats[$month][$personName])) {
                        $monthlyStats[$month][$personName] = [
                            'count' => 0,
                            'total_amount' => 0,
                            'night_count' => 0,
                            'evening_count' => 0,
                            'typhoon_count' => 0
                        ];
                    }
                    
                    $monthlyStats[$month][$personName]['count']++;
                    $monthlyStats[$month][$personName]['total_amount'] += $item->total_phone_amount;
                    
                    if ($item->night_phone_amount > 0) {
                        $monthlyStats[$month][$personName]['night_count']++;
                    }
                    if ($item->evening_phone_amount > 0) {
                        $monthlyStats[$month][$personName]['evening_count']++;
                    }
                    if ($item->typhoon_phone_amount > 0) {
                        $monthlyStats[$month][$personName]['typhoon_count']++;
                    }

                    // 日統計
                    if (!isset($dailyStats[$date][$personName])) {
                        $dailyStats[$date][$personName] = [
                            'count' => 0,
                            'total_amount' => 0,
                            'night_count' => 0,
                            'evening_count' => 0,
                            'typhoon_count' => 0
                        ];
                    }
                    
                    $dailyStats[$date][$personName]['count']++;
                    $dailyStats[$date][$personName]['total_amount'] += $item->total_phone_amount;
                    
                    if ($item->night_phone_amount > 0) {
                        $dailyStats[$date][$personName]['night_count']++;
                    }
                    if ($item->evening_phone_amount > 0) {
                        $dailyStats[$date][$personName]['evening_count']++;
                    }
                    if ($item->typhoon_phone_amount > 0) {
                        $dailyStats[$date][$personName]['typhoon_count']++;
                    }

                    // 準備詳細資料
                    $excelData[] = [
                        'date' => $date,
                        'person' => $personName,
                        'type' => '接電話',
                        'night_amount' => $item->night_phone_amount,
                        'evening_amount' => $item->evening_phone_amount,
                        'typhoon_amount' => $item->typhoon_phone_amount,
                        'total_amount' => $item->total_phone_amount
                    ];
                }

                // 處理接件人員
                if ($item->receive_person_id) {
                    $personName = $item->receivePerson->name ?? '未指定';
                    
                    // 月度統計
                    if (!isset($monthlyStats[$month][$personName])) {
                        $monthlyStats[$month][$personName] = [
                            'count' => 0,
                            'total_amount' => 0,
                            'night_count' => 0,
                            'evening_count' => 0,
                            'typhoon_count' => 0
                        ];
                    }
                    
                    $monthlyStats[$month][$personName]['count']++;
                    $monthlyStats[$month][$personName]['total_amount'] += $item->total_receive_amount;
                    
                    if ($item->night_receive_amount > 0) {
                        $monthlyStats[$month][$personName]['night_count']++;
                    }
                    if ($item->evening_receive_amount > 0) {
                        $monthlyStats[$month][$personName]['evening_count']++;
                    }
                    if ($item->typhoon_receive_amount > 0) {
                        $monthlyStats[$month][$personName]['typhoon_count']++;
                    }

                    // 日統計
                    if (!isset($dailyStats[$date][$personName])) {
                        $dailyStats[$date][$personName] = [
                            'count' => 0,
                            'total_amount' => 0,
                            'night_count' => 0,
                            'evening_count' => 0,
                            'typhoon_count' => 0
                        ];
                    }
                    
                    $dailyStats[$date][$personName]['count']++;
                    $dailyStats[$date][$personName]['total_amount'] += $item->total_receive_amount;
                    
                    if ($item->night_receive_amount > 0) {
                        $dailyStats[$date][$personName]['night_count']++;
                    }
                    if ($item->evening_receive_amount > 0) {
                        $dailyStats[$date][$personName]['evening_count']++;
                    }
                    if ($item->typhoon_receive_amount > 0) {
                        $dailyStats[$date][$personName]['typhoon_count']++;
                    }

                    // 準備詳細資料
                    $excelData[] = [
                        'date' => $date,
                        'person' => $personName,
                        'type' => '接件',
                        'night_amount' => $item->night_receive_amount,
                        'evening_amount' => $item->evening_receive_amount,
                        'typhoon_amount' => $item->typhoon_receive_amount,
                        'total_amount' => $item->total_receive_amount
                    ];
                }
            }
        }

        // 生成 CSV 檔案
        $filename = '加成統計_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($excelData, $monthlyStats, $dailyStats) {
            $file = fopen('php://output', 'w');
            
            // 寫入 BOM 以支援中文
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // 寫入標題
            fputcsv($file, ['加成日期', '人員', '類型', '夜間加成', '晚間加成', '颱風加成', '總金額']);
            
            // 寫入詳細資料
            foreach ($excelData as $row) {
                fputcsv($file, [
                    $row['date'],
                    $row['person'],
                    $row['type'],
                    $row['night_amount'],
                    $row['evening_amount'],
                    $row['typhoon_amount'],
                    $row['total_amount']
                ]);
            }
            
            // 寫入空行
            fputcsv($file, []);
            
            // 寫入日統計
            fputcsv($file, ['日統計']);
            fputcsv($file, ['日期', '人員', '記錄次數', '總金額', '夜間次數', '晚間次數', '颱風次數']);
            
            foreach ($dailyStats as $date => $persons) {
                foreach ($persons as $person => $stats) {
                    fputcsv($file, [
                        $date,
                        $person,
                        $stats['count'],
                        $stats['total_amount'],
                        $stats['night_count'],
                        $stats['evening_count'],
                        $stats['typhoon_count']
                    ]);
                }
            }
            
            // 寫入空行
            fputcsv($file, []);
            
            // 寫入月度統計
            fputcsv($file, ['月度統計']);
            fputcsv($file, ['月份', '人員', '記錄次數', '總金額', '夜間次數', '晚間次數', '颱風次數']);
            
            foreach ($monthlyStats as $month => $persons) {
                foreach ($persons as $person => $stats) {
                    fputcsv($file, [
                        $month,
                        $person,
                        $stats['count'],
                        $stats['total_amount'],
                        $stats['night_count'],
                        $stats['evening_count'],
                        $stats['typhoon_count']
                    ]);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

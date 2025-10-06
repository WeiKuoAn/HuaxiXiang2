<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Increase;
use App\Models\IncreaseItem;
use App\Models\IncreaseSetting;
use App\Models\NightShiftTimeSlot;
use App\Models\User;
use App\Models\OvertimeRecord;

class IncreaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Increase::with([
            'items.phonePerson', 
            'items.receivePerson', 
            'items.furnacePerson',
            'items.timeSlot',
            'items.overtimeRecord.user',
            'creator'
        ])->orderBy('increase_date', 'desc');

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
        $users = User::where('status', '0')->orderby('level')->orderby('seq')->whereNotIn('job_id', [4,8,9,6,11])->get();
        $timeSlots = NightShiftTimeSlot::getActiveTimeSlots();
        return view('increase.create', compact('users', 'timeSlots'));
    }

    public function store(Request $request)
    {
        // 驗證輸入
        $request->validate([
            'increase_date' => 'required|date',
            'comment' => 'nullable|string',
            'increase' => 'nullable|array',
            'increase.*.categories' => 'required|array|min:1',
            'increase.*.phone_person' => 'nullable|exists:users,id',
            'increase.*.receive_person' => 'nullable|exists:users,id',
            'furnace' => 'nullable|array',
            'furnace.*.time_slot_id' => 'required|exists:night_shift_time_slots,id',
            'furnace.*.furnace_person' => 'required|exists:users,id',
            'overtime' => 'nullable|array',
            'overtime.*.overtime_record' => 'required|exists:overtime_records,id',
            'overtime.*.overtime_amount' => 'nullable|numeric|min:0',
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

            // 1. 處理傳統加成項目（夜間、晚間、颱風）
            if ($request->filled('increase')) {
                foreach ($request->increase as $itemData) {
                    $increaseItem = new IncreaseItem([
                        'increase_id' => $increase->id,
                        'item_type' => 'traditional',
                        'phone_person_id' => $itemData['phone_person'] ?? null,
                        'receive_person_id' => $itemData['receive_person'] ?? null,
                        'furnace_person_id' => null,
                        'phone_exclude_bonus' => isset($itemData['phone_exclude_bonus']) && $itemData['phone_exclude_bonus'] == '1',
                        'time_slot_id' => null,
                    ]);

                    // 處理各類別加成
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
            }

            // 2. 處理夜間開爐項目
            if ($request->filled('furnace')) {
                foreach ($request->furnace as $furnaceData) {
                    $furnaceItem = new IncreaseItem([
                        'increase_id' => $increase->id,
                        'item_type' => 'furnace',
                        'phone_person_id' => null,
                        'receive_person_id' => null,
                        'furnace_person_id' => $furnaceData['furnace_person'],
                        'phone_exclude_bonus' => false,
                        'time_slot_id' => $furnaceData['time_slot_id'],
                    ]);

                    // 計算總金額（使用時段價格）
                    $furnaceItem->calculateTotalAmount();
                    $furnaceItem->save();
                }
            }

            // 3. 處理加班費項目
            if ($request->filled('overtime')) {
                foreach ($request->overtime as $overtimeData) {
                    $overtimeRecord = OvertimeRecord::find($overtimeData['overtime_record']);
                    
                    if ($overtimeRecord) {
                        // 使用自定義金額或原始金額
                        $customAmount = $overtimeData['overtime_amount'] ?? $overtimeRecord->overtime_pay;
                        
                        $overtimeItem = new IncreaseItem([
                            'increase_id' => $increase->id,
                            'item_type' => 'overtime',
                            'phone_person_id' => null,
                            'receive_person_id' => $overtimeRecord->user_id,
                            'furnace_person_id' => null,
                            'phone_exclude_bonus' => false,
                            'time_slot_id' => null,
                            'overtime_record_id' => $overtimeData['overtime_record'],
                            'custom_amount' => $customAmount,
                        ]);

                        // 使用模型的計算方法
                        $overtimeItem->calculateOvertimeAmount();
                        $overtimeItem->save();
                    }
                }
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
        $increase = Increase::with([
            'items.phonePerson', 
            'items.receivePerson', 
            'items.furnacePerson',
            'items.timeSlot',
            'items.overtimeRecord.user'
        ])->findOrFail($id);
        $users = User::where('status', '0')->orderby('level')->orderby('seq')->whereNotIn('job_id', [4,8,9,6,11])->get();
        $timeSlots = NightShiftTimeSlot::getActiveTimeSlots();
        
        return view('increase.edit', compact('increase', 'users', 'timeSlots'));
    }

    public function update(Request $request, $id)
    {
        // 驗證輸入
        $request->validate([
            'increase_date' => 'required|date',
            'comment' => 'nullable|string',
            'increase' => 'nullable|array',
            'increase.*.categories' => 'required|array|min:1',
            'increase.*.phone_person' => 'nullable|exists:users,id',
            'increase.*.receive_person' => 'nullable|exists:users,id',
            'furnace' => 'nullable|array',
            'furnace.*.time_slot_id' => 'required|exists:night_shift_time_slots,id',
            'furnace.*.furnace_person' => 'required|exists:users,id',
            'overtime' => 'nullable|array',
            'overtime.*.overtime_record' => 'required|exists:overtime_records,id',
            'overtime.*.overtime_amount' => 'nullable|numeric|min:0',
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

            // 1. 處理傳統加成項目（夜間、晚間、颱風）
            if ($request->filled('increase')) {
                foreach ($request->increase as $itemData) {
                    $increaseItem = new IncreaseItem([
                        'increase_id' => $increase->id,
                        'item_type' => 'traditional',
                        'phone_person_id' => $itemData['phone_person'] ?? null,
                        'receive_person_id' => $itemData['receive_person'] ?? null,
                        'furnace_person_id' => null,
                        'phone_exclude_bonus' => isset($itemData['phone_exclude_bonus']) && $itemData['phone_exclude_bonus'] == '1',
                        'time_slot_id' => null,
                    ]);

                    // 處理各類別加成
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
            }

            // 2. 處理夜間開爐項目
            if ($request->filled('furnace')) {
                foreach ($request->furnace as $furnaceData) {
                    $furnaceItem = new IncreaseItem([
                        'increase_id' => $increase->id,
                        'item_type' => 'furnace',
                        'phone_person_id' => null,
                        'receive_person_id' => null,
                        'furnace_person_id' => $furnaceData['furnace_person'],
                        'phone_exclude_bonus' => false,
                        'time_slot_id' => $furnaceData['time_slot_id'],
                    ]);

                    // 計算總金額（使用時段價格）
                    $furnaceItem->calculateTotalAmount();
                    $furnaceItem->save();
                }
            }

            // 3. 處理加班費項目
            if ($request->filled('overtime')) {
                foreach ($request->overtime as $overtimeData) {
                    $overtimeRecord = OvertimeRecord::find($overtimeData['overtime_record']);
                    
                    if ($overtimeRecord) {
                        // 使用自定義金額或原始金額
                        $customAmount = $overtimeData['overtime_amount'] ?? $overtimeRecord->overtime_pay;
                        
                        $overtimeItem = new IncreaseItem([
                            'increase_id' => $increase->id,
                            'item_type' => 'overtime',
                            'phone_person_id' => null,
                            'receive_person_id' => $overtimeRecord->user_id,
                            'furnace_person_id' => null,
                            'phone_exclude_bonus' => false,
                            'time_slot_id' => null,
                            'overtime_record_id' => $overtimeData['overtime_record'],
                            'custom_amount' => $customAmount,
                        ]);

                        // 使用模型的計算方法
                        $overtimeItem->calculateOvertimeAmount();
                        $overtimeItem->save();
                    }
                }
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
        $query = Increase::with([
            'items.phonePerson', 
            'items.receivePerson', 
            'items.furnacePerson',
            'items.overtimeRecord.user'
        ])->orderBy('increase_date', 'asc');

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
                // 根據項目類型處理
                switch ($item->item_type) {
                    case 'traditional':
                        // 處理傳統加成（接電話人員）
                        if ($item->phone_person_id) {
                            $personName = $item->phonePerson->name ?? '未指定';
                            $this->updateStats($monthlyStats, $dailyStats, $month, $date, $personName, 'traditional', '接電話', $item->total_phone_amount, $item);
                            
                            $excelData[] = [
                                'date' => $date,
                                'person' => $personName,
                                'item_type' => '傳統加成',
                                'category' => $this->getCategoryString($item, 'phone'),
                                'phone_amount' => $item->phone_exclude_bonus ? 0 : $item->total_phone_amount,
                                'receive_amount' => 0,
                                'furnace_amount' => 0,
                                'overtime_amount' => 0,
                                'total_amount' => $item->phone_exclude_bonus ? 0 : $item->total_phone_amount
                            ];
                        }

                        // 處理傳統加成（接件人員）
                        if ($item->receive_person_id) {
                            $personName = $item->receivePerson->name ?? '未指定';
                            $this->updateStats($monthlyStats, $dailyStats, $month, $date, $personName, 'traditional', '接件', $item->total_receive_amount, $item);
                            
                            $excelData[] = [
                                'date' => $date,
                                'person' => $personName,
                                'item_type' => '傳統加成',
                                'category' => $this->getCategoryString($item, 'receive'),
                                'phone_amount' => 0,
                                'receive_amount' => $item->total_receive_amount,
                                'furnace_amount' => 0,
                                'overtime_amount' => 0,
                                'total_amount' => $item->total_receive_amount
                            ];
                        }
                        break;

                    case 'furnace':
                        // 處理夜間開爐
                        if ($item->furnace_person_id) {
                            $personName = $item->furnacePerson->name ?? '未指定';
                            $this->updateStats($monthlyStats, $dailyStats, $month, $date, $personName, 'furnace', '夜間開爐', $item->total_amount, $item);
                            
                            $excelData[] = [
                                'date' => $date,
                                'person' => $personName,
                                'item_type' => '夜間開爐',
                                'category' => '夜間開爐',
                                'phone_amount' => 0,
                                'receive_amount' => 0,
                                'furnace_amount' => $item->total_amount,
                                'overtime_amount' => 0,
                                'total_amount' => $item->total_amount
                            ];
                        }
                        break;

                    case 'overtime':
                        // 處理加班費
                        if ($item->overtime_record_id) {
                            $personName = $item->overtimeRecord->user->name ?? '未指定';
                            $overtimeAmount = $item->custom_amount ?? $item->total_amount;
                            $this->updateStats($monthlyStats, $dailyStats, $month, $date, $personName, 'overtime', '加班費', $overtimeAmount, $item);
                            
                            $excelData[] = [
                                'date' => $date,
                                'person' => $personName,
                                'item_type' => '加班費',
                                'category' => '加班費',
                                'phone_amount' => 0,
                                'receive_amount' => 0,
                                'furnace_amount' => 0,
                                'overtime_amount' => $overtimeAmount,
                                'total_amount' => $overtimeAmount
                            ];
                        }
                        break;
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
            fputcsv($file, ['加成日期', '人員', '項目類型', '加成類別', '接電話', '接件', '夜間開爐', '加班費', '總金額']);
            
            // 寫入詳細資料
            foreach ($excelData as $row) {
                fputcsv($file, [
                    $row['date'],
                    $row['person'],
                    $row['item_type'],
                    $row['category'],
                    $row['phone_amount'],
                    $row['receive_amount'],
                    $row['furnace_amount'],
                    $row['overtime_amount'],
                    $row['total_amount']
                ]);
            }
            
            // 寫入空行
            fputcsv($file, []);
            
            // 寫入日統計
            fputcsv($file, ['日統計']);
            fputcsv($file, ['日期', '人員', '記錄次數', '總金額', '傳統加成次數', '夜間開爐次數', '加班費次數']);
            
            foreach ($dailyStats as $date => $persons) {
                foreach ($persons as $person => $stats) {
                    fputcsv($file, [
                        $date,
                        $person,
                        $stats['count'],
                        $stats['total_amount'],
                        $stats['traditional_count'],
                        $stats['furnace_count'],
                        $stats['overtime_count']
                    ]);
                }
            }
            
            // 寫入空行
            fputcsv($file, []);
            
            // 寫入月度統計
            fputcsv($file, ['月度統計']);
            fputcsv($file, ['月份', '人員', '記錄次數', '總金額', '傳統加成次數', '夜間開爐次數', '加班費次數']);
            
            foreach ($monthlyStats as $month => $persons) {
                foreach ($persons as $person => $stats) {
                    fputcsv($file, [
                        $month,
                        $person,
                        $stats['count'],
                        $stats['total_amount'],
                        $stats['traditional_count'],
                        $stats['furnace_count'],
                        $stats['overtime_count']
                    ]);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * 更新統計資料
     */
    private function updateStats(&$monthlyStats, &$dailyStats, $month, $date, $personName, $type, $subType, $amount, $item)
    {
        // 月度統計
        if (!isset($monthlyStats[$month][$personName])) {
            $monthlyStats[$month][$personName] = [
                'count' => 0,
                'total_amount' => 0,
                'traditional_count' => 0,
                'furnace_count' => 0,
                'overtime_count' => 0
            ];
        }
        
        $monthlyStats[$month][$personName]['count']++;
        $monthlyStats[$month][$personName]['total_amount'] += $amount;
        
        switch ($type) {
            case 'traditional':
                $monthlyStats[$month][$personName]['traditional_count']++;
                break;
            case 'furnace':
                $monthlyStats[$month][$personName]['furnace_count']++;
                break;
            case 'overtime':
                $monthlyStats[$month][$personName]['overtime_count']++;
                break;
        }

        // 日統計
        if (!isset($dailyStats[$date][$personName])) {
            $dailyStats[$date][$personName] = [
                'count' => 0,
                'total_amount' => 0,
                'traditional_count' => 0,
                'furnace_count' => 0,
                'overtime_count' => 0
            ];
        }
        
        $dailyStats[$date][$personName]['count']++;
        $dailyStats[$date][$personName]['total_amount'] += $amount;
        
        switch ($type) {
            case 'traditional':
                $dailyStats[$date][$personName]['traditional_count']++;
                break;
            case 'furnace':
                $dailyStats[$date][$personName]['furnace_count']++;
                break;
            case 'overtime':
                $dailyStats[$date][$personName]['overtime_count']++;
                break;
        }
    }

    /**
     * 取得類別字串
     */
    private function getCategoryString($item, $type)
    {
        $categories = [];
        
        if ($type === 'phone') {
            if ($item->night_phone_amount > 0) $categories[] = '夜間';
            if ($item->evening_phone_amount > 0) $categories[] = '晚間';
            if ($item->typhoon_phone_amount > 0) $categories[] = '颱風';
        } else {
            if ($item->night_receive_amount > 0) $categories[] = '夜間';
            if ($item->evening_receive_amount > 0) $categories[] = '晚間';
            if ($item->typhoon_receive_amount > 0) $categories[] = '颱風';
        }
        
        return implode('、', $categories) ?: '無';
    }

    /**
     * 取得指定日期的加班記錄
     */
    public function getOvertimeRecords($date)
    {
        try {
            $overtimeRecords = OvertimeRecord::with('user')
                ->where('overtime_date', $date)
                ->get()
                ->map(function ($record) {
                    return [
                        'id' => $record->id,
                        'user_id' => $record->user_id,
                        'user_name' => $record->user->name ?? '未知人員',
                        'minutes' => $record->minutes,
                        'formatted_hours' => $record->formatted_hours,
                        'overtime_pay' => $record->overtime_pay,
                        'reason' => $record->reason,
                        'first_two_hours' => $record->first_two_hours,
                        'remaining_hours' => $record->remaining_hours,
                        'first_two_hours_pay' => $record->first_two_hours_pay,
                        'remaining_hours_pay' => $record->remaining_hours_pay,
                    ];
                });

            return response()->json([
                'success' => true,
                'records' => $overtimeRecords
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '取得加班記錄失敗：' . $e->getMessage()
            ], 500);
        }
    }
}

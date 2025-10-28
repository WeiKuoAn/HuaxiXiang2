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
        // 調試信息
        \Log::info('Increase Form Submit:', [
            'all_data' => $request->all(),
            'overtime' => $request->overtime,
            'increase' => $request->increase,
            'furnace' => $request->furnace,
        ]);
        
        // 驗證輸入
        try {
            $request->validate([
                'increase_date' => 'required|date',
                'comment' => 'nullable|string',
                'increase' => 'nullable|array',
                'increase.*.categories' => 'nullable|array',
                'increase.*.phone_person' => 'nullable|exists:users,id',
                'increase.*.receive_person' => 'nullable|exists:users,id',
                'furnace' => 'nullable|array',
                'furnace.*.time_slot_id' => 'nullable|exists:night_shift_time_slots,id',
                'furnace.*.furnace_person' => 'nullable|exists:users,id',
                'overtime' => 'nullable|array',
                'overtime.*.overtime_record' => 'nullable|exists:overtime_records,id',
                'overtime.*.overtime_amount' => 'nullable|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', [
                'errors' => $e->errors(),
                'input' => $request->all(),
            ]);
            throw $e;
        }

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
                    // 跳過沒有類別的項目
                    if (empty($itemData['categories']) || !is_array($itemData['categories'])) {
                        continue;
                    }
                    
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
                    // 跳過沒有時段或人員的項目
                    if (empty($furnaceData['time_slot_id']) || empty($furnaceData['furnace_person'])) {
                        continue;
                    }
                    
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
                foreach ($request->overtime as $index => $overtimeData) {
                    \Log::info("處理加班費項目 {$index}:", [
                        'overtime_record_id' => $overtimeData['overtime_record'] ?? 'null',
                        'all_data' => $overtimeData,
                    ]);
                    
                    // 跳過沒有 overtime_record 的項目
                    if (empty($overtimeData['overtime_record'])) {
                        \Log::info("跳過加班費項目 {$index}：沒有 overtime_record");
                        continue;
                    }
                    
                    $overtimeRecord = OvertimeRecord::find($overtimeData['overtime_record']);
                    
                    if ($overtimeRecord) {
                        \Log::info("找到加班記錄:", [
                            'record_id' => $overtimeRecord->id,
                            'user_id' => $overtimeRecord->user_id,
                            'user_name' => $overtimeRecord->user->name ?? 'N/A',
                        ]);
                        
                        // 不再使用自定義金額，改為記錄小時數統計
                        $customAmount = null;
                        
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
                        
                        \Log::info("儲存加班費項目:", [
                            'increase_item_id' => $overtimeItem->id,
                            'receive_person_id' => $overtimeItem->receive_person_id,
                            'overtime_record_id' => $overtimeItem->overtime_record_id,
                        ]);
                    } else {
                        \Log::warning("找不到加班記錄:", ['record_id' => $overtimeData['overtime_record']]);
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
                    // 跳過沒有類別的項目
                    if (empty($itemData['categories']) || !is_array($itemData['categories'])) {
                        continue;
                    }
                    
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
                    // 跳過沒有時段或人員的項目
                    if (empty($furnaceData['time_slot_id']) || empty($furnaceData['furnace_person'])) {
                        continue;
                    }
                    
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
                    // 跳過沒有 overtime_record 的項目
                    if (empty($overtimeData['overtime_record'])) {
                        continue;
                    }
                    
                    $overtimeRecord = OvertimeRecord::find($overtimeData['overtime_record']);
                    
                    if ($overtimeRecord) {
                        $overtimeItem = new IncreaseItem([
                            'increase_id' => $increase->id,
                            'item_type' => 'overtime',
                            'phone_person_id' => null,
                            'receive_person_id' => $overtimeRecord->user_id,
                            'furnace_person_id' => null,
                            'phone_exclude_bonus' => false,
                            'time_slot_id' => null,
                            'overtime_record_id' => $overtimeData['overtime_record'],
                            'custom_amount' => null, // 不再使用自定義金額
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

    public function statistics(Request $request)
    {
        // 取得查詢參數
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));
        
        // 計算月份的第一天和最後一天
        $startDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        
        // 查詢該月份的所有加成記錄
        $query = Increase::with([
            'items.phonePerson', 
            'items.receivePerson', 
            'items.furnacePerson',
            'items.timeSlot',
            'items.overtimeRecord.user',
            'creator'
        ])->whereBetween('increase_date', [$startDate, $endDate])
          ->orderBy('increase_date', 'asc');

        $increases = $query->get();
        
        // 取得所有專員列表
        $users = User::where('status', '0')
                    ->orderby('level')
                    ->orderby('seq')
                    ->whereNotIn('job_id', [4,8,9,6,11])
                    ->get();
        
        // 統計資料結構
        $statistics = [];
        $dailyStats = [];
        $monthlyTotals = [];
        
        // 初始化每個專員的統計資料
        foreach ($users as $user) {
            $statistics[$user->id] = [
                'user' => $user,
                'daily' => [],
                'monthly_total' => [
                    'phone_amount' => 0,
                    'receive_amount' => 0,
                    'furnace_amount' => 0,
                    'overtime_amount' => 0,
                    'total_amount' => 0,
                    'overtime_134_hours' => 0,
                    'overtime_167_hours' => 0,
                    'night_phone_amount' => 0,
                    'evening_phone_amount' => 0,
                    'typhoon_phone_amount' => 0,
                    'night_receive_amount' => 0,
                    'evening_receive_amount' => 0,
                    'typhoon_receive_amount' => 0,
                ]
            ];
        }
        
        // 處理每一天的資料
        $currentDate = $startDate;
        while ($currentDate <= $endDate) {
            $dateKey = $currentDate;
            $dailyStats[$dateKey] = [
                'date' => $currentDate,
                'users' => [],
                'daily_total' => [
                    'phone_amount' => 0,
                    'receive_amount' => 0,
                    'furnace_amount' => 0,
                    'overtime_amount' => 0,
                    'total_amount' => 0,
                    'overtime_134_hours' => 0,
                    'overtime_167_hours' => 0,
                ]
            ];
            
            // 初始化每個專員當日的資料
            foreach ($users as $user) {
                $dailyStats[$dateKey]['users'][$user->id] = [
                    'phone_amount' => 0,
                    'receive_amount' => 0,
                    'furnace_amount' => 0,
                    'overtime_amount' => 0,
                    'total_amount' => 0,
                    'overtime_134_hours' => 0,
                    'overtime_167_hours' => 0,
                    'night_phone_amount' => 0,
                    'evening_phone_amount' => 0,
                    'typhoon_phone_amount' => 0,
                    'night_receive_amount' => 0,
                    'evening_receive_amount' => 0,
                    'typhoon_receive_amount' => 0,
                    'categories' => [],
                    'items_count' => 0
                ];
            }
            
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }
        
        // 處理每個加成記錄
        foreach ($increases as $increase) {
            $dateKey = $increase->increase_date->format('Y-m-d');
            
            foreach ($increase->items as $item) {
                // 處理接電話人員
                if ($item->phone_person_id && $item->phonePerson) {
                    $userId = $item->phone_person_id;
                    $itemData = [
                        'phone_amount' => $item->phone_exclude_bonus ? 0 : $item->total_phone_amount,
                        'receive_amount' => 0,
                        'furnace_amount' => 0,
                        'overtime_amount' => 0,
                        'total_amount' => 0,
                        'overtime_134_hours' => 0,
                        'overtime_167_hours' => 0,
                        'night_phone_amount' => $item->night_phone_amount ?? 0,
                        'evening_phone_amount' => $item->evening_phone_amount ?? 0,
                        'typhoon_phone_amount' => $item->typhoon_phone_amount ?? 0,
                        'night_receive_amount' => 0,
                        'evening_receive_amount' => 0,
                        'typhoon_receive_amount' => 0,
                        'categories' => [],
                        'items_count' => 1
                    ];
                    
                    // 記錄類別
                    if ($item->night_phone_amount > 0) $itemData['categories'][] = '夜間';
                    if ($item->evening_phone_amount > 0) $itemData['categories'][] = '晚間';
                    if ($item->typhoon_phone_amount > 0) $itemData['categories'][] = '颱風';
                    
                    // 計算總計
                    $itemData['total_amount'] = $itemData['phone_amount'];
                    
                    // 更新統計資料
                    if (isset($dailyStats[$dateKey]['users'][$userId]) && isset($statistics[$userId])) {
                        $this->updateStatistics($dailyStats, $statistics, $dateKey, $userId, $itemData);
                    }
                }
                
                // 處理接件人員
                if ($item->receive_person_id && $item->receivePerson) {
                    $userId = $item->receive_person_id;
                    $itemData = [
                        'phone_amount' => 0,
                        'receive_amount' => $item->total_receive_amount,
                        'furnace_amount' => 0,
                        'overtime_amount' => 0,
                        'total_amount' => 0,
                        'overtime_134_hours' => 0,
                        'overtime_167_hours' => 0,
                        'night_phone_amount' => 0,
                        'evening_phone_amount' => 0,
                        'typhoon_phone_amount' => 0,
                        'night_receive_amount' => $item->night_receive_amount ?? 0,
                        'evening_receive_amount' => $item->evening_receive_amount ?? 0,
                        'typhoon_receive_amount' => $item->typhoon_receive_amount ?? 0,
                        'categories' => [],
                        'items_count' => 1
                    ];
                    
                    // 記錄類別
                    if ($item->night_receive_amount > 0) $itemData['categories'][] = '夜間';
                    if ($item->evening_receive_amount > 0) $itemData['categories'][] = '晚間';
                    if ($item->typhoon_receive_amount > 0) $itemData['categories'][] = '颱風';
                    
                    // 計算總計
                    $itemData['total_amount'] = $itemData['receive_amount'];
                    
                    // 更新統計資料
                    if (isset($dailyStats[$dateKey]['users'][$userId]) && isset($statistics[$userId])) {
                        $this->updateStatistics($dailyStats, $statistics, $dateKey, $userId, $itemData);
                    }
                }
                
                // 處理夜間開爐人員
                if ($item->furnace_person_id && $item->furnacePerson) {
                    $userId = $item->furnace_person_id;
                    $itemData = [
                        'phone_amount' => 0,
                        'receive_amount' => 0,
                        'furnace_amount' => $item->total_amount,
                        'overtime_amount' => 0,
                        'total_amount' => 0,
                        'overtime_134_hours' => 0,
                        'overtime_167_hours' => 0,
                        'night_phone_amount' => 0,
                        'evening_phone_amount' => 0,
                        'typhoon_phone_amount' => 0,
                        'night_receive_amount' => 0,
                        'evening_receive_amount' => 0,
                        'typhoon_receive_amount' => 0,
                        'categories' => ['夜間開爐'],
                        'items_count' => 1
                    ];
                    
                    // 計算總計
                    $itemData['total_amount'] = $itemData['furnace_amount'];
                    
                    // 更新統計資料
                    if (isset($dailyStats[$dateKey]['users'][$userId]) && isset($statistics[$userId])) {
                        $this->updateStatistics($dailyStats, $statistics, $dateKey, $userId, $itemData);
                    }
                }
                
                // 處理加班費人員
                if ($item->overtime_record_id && $item->overtimeRecord) {
                    $userId = $item->overtimeRecord->user_id;
                    $itemData = [
                        'phone_amount' => 0,
                        'receive_amount' => 0,
                        'furnace_amount' => 0,
                        'overtime_amount' => $item->custom_amount ?? $item->total_amount,
                        'total_amount' => 0,
                        'overtime_134_hours' => $item->overtimeRecord->first_two_hours ?? 0,
                        'overtime_167_hours' => $item->overtimeRecord->remaining_hours ?? 0,
                        'night_phone_amount' => 0,
                        'evening_phone_amount' => 0,
                        'typhoon_phone_amount' => 0,
                        'night_receive_amount' => 0,
                        'evening_receive_amount' => 0,
                        'typhoon_receive_amount' => 0,
                        'categories' => ['加班費'],
                        'items_count' => 1
                    ];
                    
                    // 計算總計
                    $itemData['total_amount'] = $itemData['overtime_amount'];
                    
                    // 更新統計資料
                    if (isset($dailyStats[$dateKey]['users'][$userId]) && isset($statistics[$userId])) {
                        $this->updateStatistics($dailyStats, $statistics, $dateKey, $userId, $itemData);
                    }
                }
            }
        }
        
        // 計算月度總計
        $monthlyTotals = [
            'phone_amount' => 0,
            'receive_amount' => 0,
            'furnace_amount' => 0,
            'overtime_amount' => 0,
            'total_amount' => 0,
            'overtime_134_hours' => 0,
            'overtime_167_hours' => 0,
        ];
        
        foreach ($statistics as $userStats) {
            $monthlyTotals['phone_amount'] += $userStats['monthly_total']['phone_amount'];
            $monthlyTotals['receive_amount'] += $userStats['monthly_total']['receive_amount'];
            $monthlyTotals['furnace_amount'] += $userStats['monthly_total']['furnace_amount'];
            $monthlyTotals['overtime_amount'] += $userStats['monthly_total']['overtime_amount'];
            $monthlyTotals['total_amount'] += $userStats['monthly_total']['total_amount'];
            $monthlyTotals['overtime_134_hours'] += $userStats['monthly_total']['overtime_134_hours'];
            $monthlyTotals['overtime_167_hours'] += $userStats['monthly_total']['overtime_167_hours'];
        }
        
        return view('increase.statistics', compact(
            'statistics', 
            'dailyStats', 
            'monthlyTotals', 
            'users', 
            'year', 
            'month', 
            'startDate', 
            'endDate'
        ));
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
            $overtimeRecords = OvertimeRecord::with(['user', 'creator'])
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
                        'created_by' => $record->created_by,
                        'created_by_name' => $record->creator->name ?? '未知人員',
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
    
    /**
     * 更新統計資料
     */
    private function updateStatistics(&$dailyStats, &$statistics, $dateKey, $userId, $itemData)
    {
        // 累加到當日統計
        $dailyStats[$dateKey]['users'][$userId]['phone_amount'] += $itemData['phone_amount'];
        $dailyStats[$dateKey]['users'][$userId]['receive_amount'] += $itemData['receive_amount'];
        $dailyStats[$dateKey]['users'][$userId]['furnace_amount'] += $itemData['furnace_amount'];
        $dailyStats[$dateKey]['users'][$userId]['overtime_amount'] += $itemData['overtime_amount'];
        $dailyStats[$dateKey]['users'][$userId]['total_amount'] += $itemData['total_amount'];
        $dailyStats[$dateKey]['users'][$userId]['overtime_134_hours'] += $itemData['overtime_134_hours'];
        $dailyStats[$dateKey]['users'][$userId]['overtime_167_hours'] += $itemData['overtime_167_hours'];
        $dailyStats[$dateKey]['users'][$userId]['items_count'] += $itemData['items_count'];
        
        // 累加詳細金額
        $dailyStats[$dateKey]['users'][$userId]['night_phone_amount'] += $itemData['night_phone_amount'] ?? 0;
        $dailyStats[$dateKey]['users'][$userId]['evening_phone_amount'] += $itemData['evening_phone_amount'] ?? 0;
        $dailyStats[$dateKey]['users'][$userId]['typhoon_phone_amount'] += $itemData['typhoon_phone_amount'] ?? 0;
        $dailyStats[$dateKey]['users'][$userId]['night_receive_amount'] += $itemData['night_receive_amount'] ?? 0;
        $dailyStats[$dateKey]['users'][$userId]['evening_receive_amount'] += $itemData['evening_receive_amount'] ?? 0;
        $dailyStats[$dateKey]['users'][$userId]['typhoon_receive_amount'] += $itemData['typhoon_receive_amount'] ?? 0;
        
        // 合併類別
        $dailyStats[$dateKey]['users'][$userId]['categories'] = array_unique(array_merge(
            $dailyStats[$dateKey]['users'][$userId]['categories'], 
            $itemData['categories']
        ));
        $dailyStats[$dateKey]['users'][$userId]['categories'] = array_values($dailyStats[$dateKey]['users'][$userId]['categories']);
        
        // 累加到當日總計
        $dailyStats[$dateKey]['daily_total']['phone_amount'] += $itemData['phone_amount'];
        $dailyStats[$dateKey]['daily_total']['receive_amount'] += $itemData['receive_amount'];
        $dailyStats[$dateKey]['daily_total']['furnace_amount'] += $itemData['furnace_amount'];
        $dailyStats[$dateKey]['daily_total']['overtime_amount'] += $itemData['overtime_amount'];
        $dailyStats[$dateKey]['daily_total']['total_amount'] += $itemData['total_amount'];
        $dailyStats[$dateKey]['daily_total']['overtime_134_hours'] += $itemData['overtime_134_hours'];
        $dailyStats[$dateKey]['daily_total']['overtime_167_hours'] += $itemData['overtime_167_hours'];
        
        // 累加到月度統計
        $statistics[$userId]['monthly_total']['phone_amount'] += $itemData['phone_amount'];
        $statistics[$userId]['monthly_total']['receive_amount'] += $itemData['receive_amount'];
        $statistics[$userId]['monthly_total']['furnace_amount'] += $itemData['furnace_amount'];
        $statistics[$userId]['monthly_total']['overtime_amount'] += $itemData['overtime_amount'];
        $statistics[$userId]['monthly_total']['total_amount'] += $itemData['total_amount'];
        $statistics[$userId]['monthly_total']['overtime_134_hours'] += $itemData['overtime_134_hours'];
        $statistics[$userId]['monthly_total']['overtime_167_hours'] += $itemData['overtime_167_hours'];
        
        // 累加月度詳細金額
        $statistics[$userId]['monthly_total']['night_phone_amount'] += $itemData['night_phone_amount'] ?? 0;
        $statistics[$userId]['monthly_total']['evening_phone_amount'] += $itemData['evening_phone_amount'] ?? 0;
        $statistics[$userId]['monthly_total']['typhoon_phone_amount'] += $itemData['typhoon_phone_amount'] ?? 0;
        $statistics[$userId]['monthly_total']['night_receive_amount'] += $itemData['night_receive_amount'] ?? 0;
        $statistics[$userId]['monthly_total']['evening_receive_amount'] += $itemData['evening_receive_amount'] ?? 0;
        $statistics[$userId]['monthly_total']['typhoon_receive_amount'] += $itemData['typhoon_receive_amount'] ?? 0;
    }

    /**
     * 匯出整合出勤與加成的月報表
     */
    public function exportCombined(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));
        
        // 計算月份的第一天和最後一天
        $startDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        
        // 查詢該月份的所有專員
        $users = User::where('status', '0')
                    ->orderby('level')
                    ->orderby('seq')
                    ->whereNotIn('job_id', [4,8,9,6,11])
                    ->get();
        
        // 查詢該月份的所有出勤記錄
        $works = \App\Models\Works::whereBetween(DB::raw('DATE(worktime)'), [$startDate, $endDate])
                    ->orderBy('worktime', 'asc')
                    ->get()
                    ->groupBy(function($item) {
                        return date('Y-m-d', strtotime($item->worktime));
                    });
        
        // 查詢該月份的所有加成記錄
        $increases = Increase::with([
            'items.phonePerson', 
            'items.receivePerson', 
            'items.furnacePerson',
            'items.timeSlot',
            'items.overtimeRecord.user'
        ])->whereBetween('increase_date', [$startDate, $endDate])
          ->orderBy('increase_date', 'asc')
          ->get()
          ->groupBy(function($item) {
              return $item->increase_date->format('Y-m-d');
          });
        
        // 生成所有日期列表
        $dates = [];
        $currentDate = $startDate;
        while ($currentDate <= $endDate) {
            $dates[] = $currentDate;
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }
        
        // 準備 CSV 文件
        $fileName = $year . '年' . $month . '月出勤加成報表_' . date("Y-m-d") . '.csv';
        
        $headers = array(
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=" . $fileName,
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        
        $callback = function() use($users, $dates, $works, $increases, $year, $month) {
            $file = fopen('php://output', 'w');
            
            // 輸出 BOM 以支持 UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // 初始化每個專員的統計數據
            $userStats = [];
            foreach ($users as $user) {
                $userStats[$user->id] = [
                    'overtime_134_hours' => 0,
                    'overtime_167_hours' => 0,
                    'phone_amount' => 0,
                    'receive_amount' => 0,
                    'furnace_amount' => 0,
                ];
            }
            
            // 輸出標題
            fputcsv($file, [$year . '年' . $month . '月 出勤與加成統計表']);
            fputcsv($file, []);
            
            // 輸出表頭 - 第一行：專員編號
            $headerRow1 = ['日期'];
            foreach ($users as $user) {
                $headerRow1[] = $user->id . '：' . $user->name;
                $headerRow1[] = '';
                $headerRow1[] = '';
            }
            fputcsv($file, $headerRow1);
            
            // 輸出表頭 - 第二行：簽到、簽退、加成
            $headerRow2 = [''];
            foreach ($users as $user) {
                $headerRow2[] = '簽到';
                $headerRow2[] = '簽退';
                $headerRow2[] = '加成內容';
            }
            fputcsv($file, $headerRow2);
            
            // 輸出每日資料
            foreach ($dates as $date) {
                $row = [date('n/j', strtotime($date))];
                
                foreach ($users as $user) {
                    $checkIn = '';
                    $checkOut = '';
                    $increaseContent = [];
                    
                    // 查找該日期該專員的出勤記錄
                    if (isset($works[$date])) {
                        $userWorks = $works[$date]->where('user_id', $user->id);
                        if ($userWorks->count() > 0) {
                            $work = $userWorks->first();
                            $checkIn = date('H:i', strtotime($work->worktime));
                            $checkOut = $work->dutytime ? date('H:i', strtotime($work->dutytime)) : '';
                        }
                    }
                    
                    // 查找該日期該專員的加成記錄
                    if (isset($increases[$date])) {
                        foreach ($increases[$date] as $increase) {
                            foreach ($increase->items as $item) {
                                $userIncreases = [];
                                
                                // 處理接電話人員
                                if ($item->phone_person_id == $user->id && !$item->phone_exclude_bonus && $item->total_phone_amount > 0) {
                                    if ($item->night_phone_amount > 0) {
                                        $userIncreases[] = '夜電×1 $' . number_format($item->night_phone_amount, 0);
                                        $userStats[$user->id]['phone_amount'] += $item->night_phone_amount;
                                    }
                                    if ($item->evening_phone_amount > 0) {
                                        $userIncreases[] = '晚電×1 $' . number_format($item->evening_phone_amount, 0);
                                        $userStats[$user->id]['phone_amount'] += $item->evening_phone_amount;
                                    }
                                    if ($item->typhoon_phone_amount > 0) {
                                        $userIncreases[] = '颱電×1 $' . number_format($item->typhoon_phone_amount, 0);
                                        $userStats[$user->id]['phone_amount'] += $item->typhoon_phone_amount;
                                    }
                                }
                                
                                // 處理接件人員
                                if ($item->receive_person_id == $user->id && $item->total_receive_amount > 0) {
                                    if ($item->night_receive_amount > 0) {
                                        $userIncreases[] = '夜間×1 $' . number_format($item->night_receive_amount, 0);
                                        $userStats[$user->id]['receive_amount'] += $item->night_receive_amount;
                                    }
                                    if ($item->evening_receive_amount > 0) {
                                        $userIncreases[] = '晚間×1 $' . number_format($item->evening_receive_amount, 0);
                                        $userStats[$user->id]['receive_amount'] += $item->evening_receive_amount;
                                    }
                                    if ($item->typhoon_receive_amount > 0) {
                                        $userIncreases[] = '颱風×1 $' . number_format($item->typhoon_receive_amount, 0);
                                        $userStats[$user->id]['receive_amount'] += $item->typhoon_receive_amount;
                                    }
                                }
                                
                                // 處理夜間開爐
                                if ($item->furnace_person_id == $user->id && $item->total_amount > 0) {
                                    $userIncreases[] = '夜間開爐 $' . number_format($item->total_amount, 0);
                                    $userStats[$user->id]['furnace_amount'] += $item->total_amount;
                                }
                                
                                // 處理加班費
                                if ($item->overtime_record_id && $item->overtimeRecord && $item->overtimeRecord->user_id == $user->id) {
                                    if ($item->overtimeRecord->first_two_hours > 0) {
                                        $userIncreases[] = '加班1.34×' . number_format($item->overtimeRecord->first_two_hours, 1) . 'h';
                                        $userStats[$user->id]['overtime_134_hours'] += $item->overtimeRecord->first_two_hours;
                                    }
                                    if ($item->overtimeRecord->remaining_hours > 0) {
                                        $userIncreases[] = '加班1.67×' . number_format($item->overtimeRecord->remaining_hours, 1) . 'h';
                                        $userStats[$user->id]['overtime_167_hours'] += $item->overtimeRecord->remaining_hours;
                                    }
                                }
                                
                                $increaseContent = array_merge($increaseContent, $userIncreases);
                            }
                        }
                    }
                    
                    $row[] = $checkIn;
                    $row[] = $checkOut;
                    $row[] = implode("\n", $increaseContent);
                }
                
                fputcsv($file, $row);
            }
            
            // 輸出統計列
            fputcsv($file, []);
            
            // 1.34倍加班小時數統計
            $row134 = ['1.34'];
            foreach ($users as $user) {
                $hours = $userStats[$user->id]['overtime_134_hours'];
                $row134[] = '';
                $row134[] = '';
                $row134[] = $hours > 0 ? number_format($hours, 1) : '';
            }
            fputcsv($file, $row134);
            
            // 1.67倍加班小時數統計
            $row167 = ['1.67'];
            foreach ($users as $user) {
                $hours = $userStats[$user->id]['overtime_167_hours'];
                $row167[] = '';
                $row167[] = '';
                $row167[] = $hours > 0 ? number_format($hours, 1) : '';
            }
            fputcsv($file, $row167);
            
            // 電話獎金統計
            $rowPhone = ['電話獎金'];
            foreach ($users as $user) {
                $amount = $userStats[$user->id]['phone_amount'];
                $rowPhone[] = '';
                $rowPhone[] = '';
                $rowPhone[] = $amount > 0 ? number_format($amount, 0) : '';
            }
            fputcsv($file, $rowPhone);
            
            // 接件獎金統計
            $rowReceive = ['接件獎金'];
            foreach ($users as $user) {
                $amount = $userStats[$user->id]['receive_amount'];
                $rowReceive[] = '';
                $rowReceive[] = '';
                $rowReceive[] = $amount > 0 ? number_format($amount, 0) : '';
            }
            fputcsv($file, $rowReceive);
            
            // 夜間開爐統計
            $rowFurnace = ['夜間開爐'];
            foreach ($users as $user) {
                $amount = $userStats[$user->id]['furnace_amount'];
                $rowFurnace[] = '';
                $rowFurnace[] = '';
                $rowFurnace[] = $amount > 0 ? number_format($amount, 0) : '';
            }
            fputcsv($file, $rowFurnace);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}

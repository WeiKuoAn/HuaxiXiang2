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
use App\Models\Works;
use Carbon\Carbon;

class IncreaseController extends Controller
{
    public function personnel_index()
    {
        $users = User::where('status', '0')->orderby('level')->orderby('seq')->get();
        return view('increase.personnel_index', compact('users'));
    }
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
        ]);
        
        // 驗證輸入
        try {
            $request->validate([
                'increase_date' => 'required|date',
                'comment' => 'nullable|string',
                'evening_is_typhoon' => 'nullable|boolean',
                'evening_is_newyear' => 'nullable|boolean',
                'night_is_typhoon' => 'nullable|boolean',
                'night_is_newyear' => 'nullable|boolean',
                'evening_phone' => 'nullable|array',
                'evening_phone.*.person' => 'nullable|exists:users,id',
                'evening_phone.*.count' => 'nullable|integer|min:1',
                'evening_receive' => 'nullable|array',
                'evening_receive.*.person' => 'nullable|exists:users,id',
                'evening_receive.*.count' => 'nullable|integer|min:1',
                'night_phone' => 'nullable|array',
                'night_phone.*.person' => 'nullable|exists:users,id',
                'night_phone.*.count' => 'nullable|integer|min:1',
                'night_receive' => 'nullable|array',
                'night_receive.*.person' => 'nullable|exists:users,id',
                'night_receive.*.count' => 'nullable|integer|min:1',
                'furnace' => 'nullable|array',
                'furnace.*.time_slot_id' => 'nullable|exists:night_shift_time_slots,id',
                'furnace.*.furnace_person' => 'nullable|exists:users,id',
                'overtime' => 'nullable|array',
                'overtime.*.overtime_record' => 'nullable|exists:overtime_records,id',
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
                'evening_is_typhoon' => $request->boolean('evening_is_typhoon'),
                'evening_is_newyear' => $request->boolean('evening_is_newyear'),
                'night_is_typhoon' => $request->boolean('night_is_typhoon'),
                'night_is_newyear' => $request->boolean('night_is_newyear'),
                'created_by' => Auth::id(),
            ]);

            // 1. 處理晚間加成 - 電話人員
            if ($request->has('evening_phone')) {
                $isSpecial = $increase->evening_is_typhoon || $increase->evening_is_newyear;
                $unitPrice = $isSpecial ? 100 : 50; // 颱風/過年 $100，一般 $50
                
                foreach ($request->evening_phone as $itemData) {
                    if (empty($itemData['person'])) continue;
                    
                    $count = $itemData['count'] ?? 1;
                    $totalAmount = $unitPrice * $count;
                    
                    IncreaseItem::create([
                        'increase_id' => $increase->id,
                        'item_type' => 'traditional',
                        'category' => 'evening',
                        'role' => 'phone',
                        'phone_person_id' => $itemData['person'],
                        'count' => $count,
                        'unit_price' => $unitPrice,
                        'evening_phone_amount' => $totalAmount,
                        'total_phone_amount' => $totalAmount,
                        'total_amount' => $totalAmount,
                    ]);
                }
            }

            // 2. 處理晚間加成 - 接件人員
            if ($request->has('evening_receive')) {
                $isSpecial = $increase->evening_is_typhoon || $increase->evening_is_newyear;
                $unitPrice = $isSpecial ? 500 : 250; // 颱風/過年 $500，一般 $250
                
                foreach ($request->evening_receive as $itemData) {
                    if (empty($itemData['person'])) continue;
                    
                    $count = $itemData['count'] ?? 1;
                    $totalAmount = $unitPrice * $count;
                    
                    IncreaseItem::create([
                        'increase_id' => $increase->id,
                        'item_type' => 'traditional',
                        'category' => 'evening',
                        'role' => 'receive',
                        'receive_person_id' => $itemData['person'],
                        'count' => $count,
                        'unit_price' => $unitPrice,
                        'evening_receive_amount' => $totalAmount,
                        'total_receive_amount' => $totalAmount,
                        'total_amount' => $totalAmount,
                    ]);
                }
            }

            // 3. 處理夜間加成 - 電話人員
            if ($request->has('night_phone')) {
                $unitPrice = 100; // 夜間加成固定 $100（不受颱風/過年影響）
                
                foreach ($request->night_phone as $itemData) {
                    if (empty($itemData['person'])) continue;
                    
                    $count = $itemData['count'] ?? 1;
                    $totalAmount = $unitPrice * $count;
                    
                    IncreaseItem::create([
                        'increase_id' => $increase->id,
                        'item_type' => 'traditional',
                        'category' => 'night',
                        'role' => 'phone',
                        'phone_person_id' => $itemData['person'],
                        'count' => $count,
                        'unit_price' => $unitPrice,
                        'night_phone_amount' => $totalAmount,
                        'total_phone_amount' => $totalAmount,
                        'total_amount' => $totalAmount,
                    ]);
                }
            }

            // 4. 處理夜間加成 - 接件人員
            if ($request->has('night_receive')) {
                $unitPrice = 500; // 夜間加成固定 $500（不受颱風/過年影響）
                
                foreach ($request->night_receive as $itemData) {
                    if (empty($itemData['person'])) continue;
                    
                    $count = $itemData['count'] ?? 1;
                    $totalAmount = $unitPrice * $count;
                    
                    IncreaseItem::create([
                        'increase_id' => $increase->id,
                        'item_type' => 'traditional',
                        'category' => 'night',
                        'role' => 'receive',
                        'receive_person_id' => $itemData['person'],
                        'count' => $count,
                        'unit_price' => $unitPrice,
                        'night_receive_amount' => $totalAmount,
                        'total_receive_amount' => $totalAmount,
                        'total_amount' => $totalAmount,
                    ]);
                }
            }

            // 5. 處理夜間開爐項目
            if ($request->has('furnace')) {
                foreach ($request->furnace as $furnaceData) {
                    // 跳過沒有時段或人員的項目
                    if (empty($furnaceData['time_slot_id']) || empty($furnaceData['furnace_person'])) {
                        continue;
                    }
                    
                    $furnaceItem = new IncreaseItem([
                        'increase_id' => $increase->id,
                        'item_type' => 'furnace',
                        'category' => 'furnace',
                        'role' => 'furnace_person',
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

            // 6. 處理加班費項目
            if ($request->has('overtime')) {
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
                            'category' => 'overtime',
                            'role' => 'receive',
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
            'evening_is_typhoon' => 'nullable|boolean',
            'evening_is_newyear' => 'nullable|boolean',
            'night_is_typhoon' => 'nullable|boolean',
            'night_is_newyear' => 'nullable|boolean',
            'evening_phone' => 'nullable|array',
            'evening_phone.*.person' => 'nullable|exists:users,id',
            'evening_phone.*.count' => 'nullable|integer|min:1',
            'evening_receive' => 'nullable|array',
            'evening_receive.*.person' => 'nullable|exists:users,id',
            'evening_receive.*.count' => 'nullable|integer|min:1',
            'night_phone' => 'nullable|array',
            'night_phone.*.person' => 'nullable|exists:users,id',
            'night_phone.*.count' => 'nullable|integer|min:1',
            'night_receive' => 'nullable|array',
            'night_receive.*.person' => 'nullable|exists:users,id',
            'night_receive.*.count' => 'nullable|integer|min:1',
            'furnace' => 'nullable|array',
            'furnace.*.time_slot_id' => 'nullable|exists:night_shift_time_slots,id',
            'furnace.*.furnace_person' => 'nullable|exists:users,id',
            'overtime' => 'nullable|array',
            'overtime.*.overtime_record' => 'nullable|exists:overtime_records,id',
        ]);

        try {
            DB::beginTransaction();

            // 更新加成主檔
            $increase = Increase::findOrFail($id);
            $increase->update([
                'increase_date' => $request->increase_date,
                'comment' => $request->comment,
                'evening_is_typhoon' => $request->boolean('evening_is_typhoon'),
                'evening_is_newyear' => $request->boolean('evening_is_newyear'),
                'night_is_typhoon' => $request->boolean('night_is_typhoon'),
                'night_is_newyear' => $request->boolean('night_is_newyear'),
            ]);

            // 刪除現有的加成項目
            $increase->items()->delete();

            // 1. 處理晚間加成 - 電話人員
            if ($request->has('evening_phone')) {
                $isSpecial = $increase->evening_is_typhoon || $increase->evening_is_newyear;
                $unitPrice = $isSpecial ? 100 : 50;
                
                foreach ($request->evening_phone as $itemData) {
                    if (empty($itemData['person'])) continue;
                    
                    $count = $itemData['count'] ?? 1;
                    $totalAmount = $unitPrice * $count;
                    
                    IncreaseItem::create([
                        'increase_id' => $increase->id,
                        'item_type' => 'traditional',
                        'category' => 'evening',
                        'role' => 'phone',
                        'phone_person_id' => $itemData['person'],
                        'count' => $count,
                        'unit_price' => $unitPrice,
                        'evening_phone_amount' => $totalAmount,
                        'total_phone_amount' => $totalAmount,
                        'total_amount' => $totalAmount,
                    ]);
                }
            }

            // 2. 處理晚間加成 - 接件人員
            if ($request->has('evening_receive')) {
                $isSpecial = $increase->evening_is_typhoon || $increase->evening_is_newyear;
                $unitPrice = $isSpecial ? 500 : 250;
                
                foreach ($request->evening_receive as $itemData) {
                    if (empty($itemData['person'])) continue;
                    
                    $count = $itemData['count'] ?? 1;
                    $totalAmount = $unitPrice * $count;
                    
                    IncreaseItem::create([
                        'increase_id' => $increase->id,
                        'item_type' => 'traditional',
                        'category' => 'evening',
                        'role' => 'receive',
                        'receive_person_id' => $itemData['person'],
                        'count' => $count,
                        'unit_price' => $unitPrice,
                        'evening_receive_amount' => $totalAmount,
                        'total_receive_amount' => $totalAmount,
                        'total_amount' => $totalAmount,
                    ]);
                }
            }

            // 3. 處理夜間加成 - 電話人員
            if ($request->has('night_phone')) {
                $unitPrice = 100;
                
                foreach ($request->night_phone as $itemData) {
                    if (empty($itemData['person'])) continue;
                    
                    $count = $itemData['count'] ?? 1;
                    $totalAmount = $unitPrice * $count;
                    
                    IncreaseItem::create([
                        'increase_id' => $increase->id,
                        'item_type' => 'traditional',
                        'category' => 'night',
                        'role' => 'phone',
                        'phone_person_id' => $itemData['person'],
                        'count' => $count,
                        'unit_price' => $unitPrice,
                        'night_phone_amount' => $totalAmount,
                        'total_phone_amount' => $totalAmount,
                        'total_amount' => $totalAmount,
                    ]);
                }
            }

            // 4. 處理夜間加成 - 接件人員
            if ($request->has('night_receive')) {
                $unitPrice = 500;
                
                foreach ($request->night_receive as $itemData) {
                    if (empty($itemData['person'])) continue;
                    
                    $count = $itemData['count'] ?? 1;
                    $totalAmount = $unitPrice * $count;
                    
                    IncreaseItem::create([
                        'increase_id' => $increase->id,
                        'item_type' => 'traditional',
                        'category' => 'night',
                        'role' => 'receive',
                        'receive_person_id' => $itemData['person'],
                        'count' => $count,
                        'unit_price' => $unitPrice,
                        'night_receive_amount' => $totalAmount,
                        'total_receive_amount' => $totalAmount,
                        'total_amount' => $totalAmount,
                    ]);
                }
            }

            // 5. 處理夜間開爐項目
            if ($request->has('furnace')) {
                foreach ($request->furnace as $furnaceData) {
                    // 跳過沒有時段或人員的項目
                    if (empty($furnaceData['time_slot_id']) || empty($furnaceData['furnace_person'])) {
                        continue;
                    }
                    
                    $furnaceItem = new IncreaseItem([
                        'increase_id' => $increase->id,
                        'item_type' => 'furnace',
                        'category' => 'furnace',
                        'role' => 'furnace_person',
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

            // 6. 處理加班費項目
            if ($request->has('overtime')) {
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
                            'category' => 'overtime',
                            'role' => 'receive',
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
                    'evening_phone_count' => 0,
                    'evening_phone_amount' => 0,
                    'evening_receive_count' => 0,
                    'evening_receive_amount' => 0,
                    'night_phone_count' => 0,
                    'night_phone_amount' => 0,
                    'night_receive_count' => 0,
                    'night_receive_amount' => 0,
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
                    'evening_phone_count' => 0,
                    'evening_phone_amount' => 0,
                    'evening_receive_count' => 0,
                    'evening_receive_amount' => 0,
                    'night_phone_count' => 0,
                    'night_phone_amount' => 0,
                    'night_receive_count' => 0,
                    'night_receive_amount' => 0,
                    'categories' => [],
                    'items_count' => 0,
                    'increase_tags' => [] // 儲存颱風/過年標記
                ];
            }
            
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }
        
        // 處理每個加成記錄
        foreach ($increases as $increase) {
            $dateKey = $increase->increase_date->format('Y-m-d');
            
            // 準備颱風/過年標記
            $tags = [];
            if ($increase->evening_is_typhoon || $increase->night_is_typhoon) {
                $tags[] = '颱風';
            }
            if ($increase->evening_is_newyear || $increase->night_is_newyear) {
                $tags[] = '過年';
            }
            
            foreach ($increase->items as $item) {
                $userId = null;
                $itemData = [
                    'phone_amount' => 0,
                    'receive_amount' => 0,
                    'furnace_amount' => 0,
                    'overtime_amount' => 0,
                    'total_amount' => 0,
                    'overtime_134_hours' => 0,
                    'overtime_167_hours' => 0,
                    'evening_phone_count' => 0,
                    'evening_phone_amount' => 0,
                    'evening_receive_count' => 0,
                    'evening_receive_amount' => 0,
                    'night_phone_count' => 0,
                    'night_phone_amount' => 0,
                    'night_receive_count' => 0,
                    'night_receive_amount' => 0,
                    'categories' => [],
                    'items_count' => 1,
                    'increase_tags' => $tags
                ];
                
                // 根據新的資料結構處理
                if ($item->category === 'evening' && $item->role === 'phone' && $item->phone_person_id) {
                    // 晚間加成 - 電話人員
                    $userId = $item->phone_person_id;
                    $itemData['evening_phone_count'] = $item->count ?? 1;
                    $itemData['evening_phone_amount'] = $item->total_amount;
                    $itemData['phone_amount'] = $item->total_amount;
                    $itemData['total_amount'] = $item->total_amount;
                    $itemData['categories'][] = '晚間電話';
                    
                } elseif ($item->category === 'evening' && $item->role === 'receive' && $item->receive_person_id) {
                    // 晚間加成 - 接件人員
                    $userId = $item->receive_person_id;
                    $itemData['evening_receive_count'] = $item->count ?? 1;
                    $itemData['evening_receive_amount'] = $item->total_amount;
                    $itemData['receive_amount'] = $item->total_amount;
                    $itemData['total_amount'] = $item->total_amount;
                    $itemData['categories'][] = '晚間接件';
                    
                } elseif ($item->category === 'night' && $item->role === 'phone' && $item->phone_person_id) {
                    // 夜間加成 - 電話人員
                    $userId = $item->phone_person_id;
                    $itemData['night_phone_count'] = $item->count ?? 1;
                    $itemData['night_phone_amount'] = $item->total_amount;
                    $itemData['phone_amount'] = $item->total_amount;
                    $itemData['total_amount'] = $item->total_amount;
                    $itemData['categories'][] = '夜間電話';
                    
                } elseif ($item->category === 'night' && $item->role === 'receive' && $item->receive_person_id) {
                    // 夜間加成 - 接件人員
                    $userId = $item->receive_person_id;
                    $itemData['night_receive_count'] = $item->count ?? 1;
                    $itemData['night_receive_amount'] = $item->total_amount;
                    $itemData['receive_amount'] = $item->total_amount;
                    $itemData['total_amount'] = $item->total_amount;
                    $itemData['categories'][] = '夜間接件';
                    
                } elseif ($item->item_type === 'furnace' && $item->furnace_person_id) {
                    // 夜間開爐人員
                    $userId = $item->furnace_person_id;
                    $itemData['furnace_amount'] = $item->total_amount;
                    $itemData['total_amount'] = $item->total_amount;
                    $itemData['categories'][] = '夜間開爐';
                    
                } elseif ($item->item_type === 'overtime' && $item->overtime_record_id && $item->overtimeRecord) {
                    // 加班費人員
                    $userId = $item->overtimeRecord->user_id;
                    $itemData['overtime_amount'] = $item->custom_amount ?? $item->total_amount;
                    $itemData['overtime_134_hours'] = $item->overtimeRecord->first_two_hours ?? 0;
                    $itemData['overtime_167_hours'] = $item->overtimeRecord->remaining_hours ?? 0;
                    $itemData['total_amount'] = $itemData['overtime_amount'];
                    $itemData['categories'][] = '加班費';
                }
                
                // 更新統計資料
                if ($userId && isset($dailyStats[$dateKey]['users'][$userId]) && isset($statistics[$userId])) {
                    $this->updateStatistics($dailyStats, $statistics, $dateKey, $userId, $itemData);
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

    public function getDayWorks($date)
    {
        try {
            $works = Works::with('user')
                ->where(function ($query) use ($date) {
                    $query->whereDate('worktime', $date)
                          ->orWhereDate('dutytime', $date);
                })
                ->orderBy('worktime')
                ->orderBy('dutytime')
                ->get();

            $records = $works->map(function ($work) {
                return [
                    'user_id' => $work->user_id,
                    'user_name' => $work->user->name ?? '未知人員',
                    'worktime_raw' => $work->worktime,
                    'dutytime_raw' => $work->dutytime,
                    'worktime_formatted' => $work->worktime ? Carbon::parse($work->worktime)->format('H:i') : null,
                    'dutytime_formatted' => $work->dutytime ? Carbon::parse($work->dutytime)->format('H:i') : null,
                ];
            });

            return response()->json([
                'success' => true,
                'records' => $records,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '取得出勤記錄失敗：' . $e->getMessage(),
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
        
        // 累加詳細次數和金額
        $dailyStats[$dateKey]['users'][$userId]['evening_phone_count'] += $itemData['evening_phone_count'] ?? 0;
        $dailyStats[$dateKey]['users'][$userId]['evening_phone_amount'] += $itemData['evening_phone_amount'] ?? 0;
        $dailyStats[$dateKey]['users'][$userId]['evening_receive_count'] += $itemData['evening_receive_count'] ?? 0;
        $dailyStats[$dateKey]['users'][$userId]['evening_receive_amount'] += $itemData['evening_receive_amount'] ?? 0;
        $dailyStats[$dateKey]['users'][$userId]['night_phone_count'] += $itemData['night_phone_count'] ?? 0;
        $dailyStats[$dateKey]['users'][$userId]['night_phone_amount'] += $itemData['night_phone_amount'] ?? 0;
        $dailyStats[$dateKey]['users'][$userId]['night_receive_count'] += $itemData['night_receive_count'] ?? 0;
        $dailyStats[$dateKey]['users'][$userId]['night_receive_amount'] += $itemData['night_receive_amount'] ?? 0;
        
        // 合併類別
        $dailyStats[$dateKey]['users'][$userId]['categories'] = array_unique(array_merge(
            $dailyStats[$dateKey]['users'][$userId]['categories'], 
            $itemData['categories']
        ));
        $dailyStats[$dateKey]['users'][$userId]['categories'] = array_values($dailyStats[$dateKey]['users'][$userId]['categories']);
        
        // 合併標記
        if (!empty($itemData['increase_tags'])) {
            $dailyStats[$dateKey]['users'][$userId]['increase_tags'] = array_unique(array_merge(
                $dailyStats[$dateKey]['users'][$userId]['increase_tags'] ?? [],
                $itemData['increase_tags']
            ));
        }
        
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
        
        // 累加月度詳細次數和金額
        $statistics[$userId]['monthly_total']['evening_phone_count'] += $itemData['evening_phone_count'] ?? 0;
        $statistics[$userId]['monthly_total']['evening_phone_amount'] += $itemData['evening_phone_amount'] ?? 0;
        $statistics[$userId]['monthly_total']['evening_receive_count'] += $itemData['evening_receive_count'] ?? 0;
        $statistics[$userId]['monthly_total']['evening_receive_amount'] += $itemData['evening_receive_amount'] ?? 0;
        $statistics[$userId]['monthly_total']['night_phone_count'] += $itemData['night_phone_count'] ?? 0;
        $statistics[$userId]['monthly_total']['night_phone_amount'] += $itemData['night_phone_amount'] ?? 0;
        $statistics[$userId]['monthly_total']['night_receive_count'] += $itemData['night_receive_count'] ?? 0;
        $statistics[$userId]['monthly_total']['night_receive_amount'] += $itemData['night_receive_amount'] ?? 0;
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
                    ->whereNotIn('job_id', [6,8,9])
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
                            // 檢查是否為未打卡的標記時間
                            $checkIn = (date('H:i:s', strtotime($work->worktime)) == '00:00:01') 
                                ? '未打卡' 
                                : date('H:i', strtotime($work->worktime));
                            
                            if ($work->dutytime) {
                                $checkOut = (date('H:i:s', strtotime($work->dutytime)) == '23:59:59')
                                    ? '未打卡'
                                    : date('H:i', strtotime($work->dutytime));
                            } else {
                                $checkOut = '';
                            }
                        }
                    }
                    
                    // 查找該日期該專員的加成記錄
                    if (isset($increases[$date])) {
                        // 用於累加相同類型的項目
                        $dailyIncreaseSummary = [
                            'evening_phone' => ['count' => 0, 'amount' => 0],
                            'evening_receive' => ['count' => 0, 'amount' => 0],
                            'night_phone' => ['count' => 0, 'amount' => 0],
                            'night_receive' => ['count' => 0, 'amount' => 0],
                            'furnace' => ['count' => 0, 'amount' => 0],
                            'overtime_134' => ['hours' => 0],
                            'overtime_167' => ['hours' => 0],
                        ];
                        
                        // 收集當日的颱風/過年標記
                        $tags = [];
                        
                        foreach ($increases[$date] as $increase) {
                            // 收集標記
                            if ($increase->evening_is_typhoon || $increase->night_is_typhoon) {
                                $tags[] = '颱風';
                            }
                            if ($increase->evening_is_newyear || $increase->night_is_newyear) {
                                $tags[] = '過年';
                            }
                            
                            foreach ($increase->items as $item) {
                                // 根據新的資料結構處理
                                if ($item->category === 'evening' && $item->role === 'phone' && $item->phone_person_id == $user->id) {
                                    // 晚間加成 - 電話人員
                                    $dailyIncreaseSummary['evening_phone']['count'] += ($item->count ?? 1);
                                    $dailyIncreaseSummary['evening_phone']['amount'] += $item->total_amount;
                                    $userStats[$user->id]['phone_amount'] += $item->total_amount;
                                    
                                } elseif ($item->category === 'evening' && $item->role === 'receive' && $item->receive_person_id == $user->id) {
                                    // 晚間加成 - 接件人員
                                    $dailyIncreaseSummary['evening_receive']['count'] += ($item->count ?? 1);
                                    $dailyIncreaseSummary['evening_receive']['amount'] += $item->total_amount;
                                    $userStats[$user->id]['receive_amount'] += $item->total_amount;
                                    
                                } elseif ($item->category === 'night' && $item->role === 'phone' && $item->phone_person_id == $user->id) {
                                    // 夜間加成 - 電話人員
                                    $dailyIncreaseSummary['night_phone']['count'] += ($item->count ?? 1);
                                    $dailyIncreaseSummary['night_phone']['amount'] += $item->total_amount;
                                    $userStats[$user->id]['phone_amount'] += $item->total_amount;
                                    
                                } elseif ($item->category === 'night' && $item->role === 'receive' && $item->receive_person_id == $user->id) {
                                    // 夜間加成 - 接件人員
                                    $dailyIncreaseSummary['night_receive']['count'] += ($item->count ?? 1);
                                    $dailyIncreaseSummary['night_receive']['amount'] += $item->total_amount;
                                    $userStats[$user->id]['receive_amount'] += $item->total_amount;
                                    
                                } elseif ($item->item_type === 'furnace' && $item->furnace_person_id == $user->id) {
                                    // 夜間開爐
                                    $dailyIncreaseSummary['furnace']['count']++;
                                    $dailyIncreaseSummary['furnace']['amount'] += $item->total_amount;
                                    $userStats[$user->id]['furnace_amount'] += $item->total_amount;
                                    
                                } elseif ($item->item_type === 'overtime' && $item->overtime_record_id && $item->overtimeRecord && $item->overtimeRecord->user_id == $user->id) {
                                    // 加班費
                                    if ($item->overtimeRecord->first_two_hours > 0) {
                                        $dailyIncreaseSummary['overtime_134']['hours'] += $item->overtimeRecord->first_two_hours;
                                        $userStats[$user->id]['overtime_134_hours'] += $item->overtimeRecord->first_two_hours;
                                    }
                                    if ($item->overtimeRecord->remaining_hours > 0) {
                                        $dailyIncreaseSummary['overtime_167']['hours'] += $item->overtimeRecord->remaining_hours;
                                        $userStats[$user->id]['overtime_167_hours'] += $item->overtimeRecord->remaining_hours;
                                    }
                                }
                            }
                        }
                        
                        // 準備標記文字
                        $tags = array_unique($tags);
                        $tagText = '';
                        if (in_array('颱風', $tags)) $tagText .= '(颱風)';
                        if (in_array('過年', $tags)) $tagText .= '(過年)';
                        
                        // 格式化輸出累加後的結果
                        if ($dailyIncreaseSummary['evening_phone']['count'] > 0) {
                            $increaseContent[] = '晚電×' . $dailyIncreaseSummary['evening_phone']['count'] . ' $' . number_format($dailyIncreaseSummary['evening_phone']['amount'], 0) . $tagText;
                        }
                        if ($dailyIncreaseSummary['evening_receive']['count'] > 0) {
                            $increaseContent[] = '晚間×' . $dailyIncreaseSummary['evening_receive']['count'] . ' $' . number_format($dailyIncreaseSummary['evening_receive']['amount'], 0) . $tagText;
                        }
                        if ($dailyIncreaseSummary['night_phone']['count'] > 0) {
                            $increaseContent[] = '夜電×' . $dailyIncreaseSummary['night_phone']['count'] . ' $' . number_format($dailyIncreaseSummary['night_phone']['amount'], 0) . $tagText;
                        }
                        if ($dailyIncreaseSummary['night_receive']['count'] > 0) {
                            $increaseContent[] = '夜間×' . $dailyIncreaseSummary['night_receive']['count'] . ' $' . number_format($dailyIncreaseSummary['night_receive']['amount'], 0) . $tagText;
                        }
                        if ($dailyIncreaseSummary['furnace']['count'] > 0) {
                            $increaseContent[] = '夜間開爐×' . $dailyIncreaseSummary['furnace']['count'] . ' $' . number_format($dailyIncreaseSummary['furnace']['amount'], 0);
                        }
                        if ($dailyIncreaseSummary['overtime_134']['hours'] > 0) {
                            $increaseContent[] = '加班1.34×' . number_format($dailyIncreaseSummary['overtime_134']['hours'], 1) . 'h';
                        }
                        if ($dailyIncreaseSummary['overtime_167']['hours'] > 0) {
                            $increaseContent[] = '加班1.67×' . number_format($dailyIncreaseSummary['overtime_167']['hours'], 1) . 'h';
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

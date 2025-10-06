<?php

namespace App\Http\Controllers;

use App\Models\MemorialDate;
use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MemorialDateController extends Controller
{
    /**
     * 紀念日管理列表頁面
     */
    public function index(Request $request)
    {
        $query = MemorialDate::with(['sale.cust_name', 'logs.user'])
            ->whereHas('sale', function($q) {
                $q->where('religion', 'buddhism_taoism');
            });

        // 搜尋條件
        if ($request->filled('customer_name')) {
            $query->whereHas('sale.cust_name', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%');
            });
        }

        if ($request->filled('pet_name')) {
            $query->whereHas('sale', function($q) use ($request) {
                $q->where('pet_name', 'like', '%' . $request->pet_name . '%');
            });
        }

        if ($request->filled('sale_on')) {
            $query->whereHas('sale', function($q) use ($request) {
                $q->where('sale_on', 'like', '%' . $request->sale_on . '%');
            });
        }

        // 日期範圍篩選（只有在沒有指定紀念日類型時才使用）
        if (!$request->filled('memorial_type')) {
            if ($request->filled('date_from')) {
                $query->where(function($q) use ($request) {
                    $q->where('seventh_day', '>=', $request->date_from)
                      ->orWhere('forty_ninth_day', '>=', $request->date_from)
                      ->orWhere('hundredth_day', '>=', $request->date_from)
                      ->orWhere('anniversary_day', '>=', $request->date_from);
                });
            }

            if ($request->filled('date_to')) {
                $query->where(function($q) use ($request) {
                    $q->where('seventh_day', '<=', $request->date_to)
                      ->orWhere('forty_ninth_day', '<=', $request->date_to)
                      ->orWhere('hundredth_day', '<=', $request->date_to)
                      ->orWhere('anniversary_day', '<=', $request->date_to);
                });
            }
        }

        // 紀念日類型篩選
        if ($request->filled('memorial_type')) {
            $memorialType = $request->memorial_type;
            switch ($memorialType) {
                case 'seventh':
                    // 篩選有頭七日期的記錄（排除浪浪方案）
                    $query->whereNotNull('seventh_day');
                    
                    // 如果有日期範圍，則進一步篩選頭七日期
                    if ($request->filled('date_from') || $request->filled('date_to')) {
                        if ($request->filled('date_from') && $request->filled('date_to')) {
                            $query->whereBetween('seventh_day', [$request->date_from, $request->date_to]);
                        } elseif ($request->filled('date_from')) {
                            $query->where('seventh_day', '>=', $request->date_from);
                        } elseif ($request->filled('date_to')) {
                            $query->where('seventh_day', '<=', $request->date_to);
                        }
                    } else {
                        // 如果沒有日期範圍，顯示未來30天內的頭七
                        $query->where('seventh_day', '>=', now()->format('Y-m-d'))
                              ->where('seventh_day', '<=', now()->addDays(30)->format('Y-m-d'));
                    }
                    break;
                case 'forty_ninth':
                    // 篩選有四十九日日期的記錄
                    $query->whereNotNull('forty_ninth_day');
                    
                    // 如果有日期範圍，則進一步篩選四十九日日期
                    if ($request->filled('date_from') || $request->filled('date_to')) {
                        if ($request->filled('date_from') && $request->filled('date_to')) {
                            $query->whereBetween('forty_ninth_day', [$request->date_from, $request->date_to]);
                        } elseif ($request->filled('date_from')) {
                            $query->where('forty_ninth_day', '>=', $request->date_from);
                        } elseif ($request->filled('date_to')) {
                            $query->where('forty_ninth_day', '<=', $request->date_to);
                        }
                    } else {
                        // 如果沒有日期範圍，顯示未來30天內的四十九日
                        $query->where('forty_ninth_day', '>=', now()->format('Y-m-d'))
                              ->where('forty_ninth_day', '<=', now()->addDays(30)->format('Y-m-d'));
                    }
                    break;
                case 'hundredth':
                    // 篩選有百日日期的記錄
                    $query->whereNotNull('hundredth_day');
                    
                    // 如果有日期範圍，則進一步篩選百日日期
                    if ($request->filled('date_from') || $request->filled('date_to')) {
                        if ($request->filled('date_from') && $request->filled('date_to')) {
                            $query->whereBetween('hundredth_day', [$request->date_from, $request->date_to]);
                        } elseif ($request->filled('date_from')) {
                            $query->where('hundredth_day', '>=', $request->date_from);
                        } elseif ($request->filled('date_to')) {
                            $query->where('hundredth_day', '<=', $request->date_to);
                        }
                    } else {
                        // 如果沒有日期範圍，顯示未來30天內的百日
                        $query->where('hundredth_day', '>=', now()->format('Y-m-d'))
                              ->where('hundredth_day', '<=', now()->addDays(30)->format('Y-m-d'));
                    }
                    break;
                case 'anniversary':
                    // 篩選有對年日期的記錄
                    $query->whereNotNull('anniversary_day');
                    
                    // 如果有日期範圍，則進一步篩選對年日期
                    if ($request->filled('date_from') || $request->filled('date_to')) {
                        if ($request->filled('date_from') && $request->filled('date_to')) {
                            $query->whereBetween('anniversary_day', [$request->date_from, $request->date_to]);
                        } elseif ($request->filled('date_from')) {
                            $query->where('anniversary_day', '>=', $request->date_from);
                        } elseif ($request->filled('date_to')) {
                            $query->where('anniversary_day', '<=', $request->date_to);
                        }
                    } else {
                        // 如果沒有日期範圍，顯示未來30天內的對年
                        $query->where('anniversary_day', '>=', now()->format('Y-m-d'))
                              ->where('anniversary_day', '<=', now()->addDays(30)->format('Y-m-d'));
                    }
                    break;
            }
        }

        // 預約狀態篩選
        if ($request->filled('reservation_status')) {
            $reservationStatus = $request->reservation_status;
            $memorialType = $request->memorial_type;
            
            if ($reservationStatus === 'reserved') {
                // 如果指定了紀念日類型，只篩選該類型的預約狀態
                if ($memorialType) {
                    switch ($memorialType) {
                        case 'seventh':
                            $query->where('seventh_reserved', true);
                            break;
                        case 'forty_ninth':
                            $query->where('forty_ninth_reserved', true);
                            break;
                        case 'hundredth':
                            $query->where('hundredth_reserved', true);
                            break;
                        case 'anniversary':
                            $query->where('anniversary_reserved', true);
                            break;
                    }
                } else {
                    // 如果沒有指定紀念日類型，顯示任何類型已預約的記錄
                    $query->where(function($q) {
                        $q->where('seventh_reserved', true)
                          ->orWhere('forty_ninth_reserved', true)
                          ->orWhere('hundredth_reserved', true)
                          ->orWhere('anniversary_reserved', true);
                    });
                }
            } elseif ($reservationStatus === 'not_reserved') {
                // 如果指定了紀念日類型，只篩選該類型的未預約狀態
                if ($memorialType) {
                    switch ($memorialType) {
                        case 'seventh':
                            $query->where('seventh_reserved', false);
                            break;
                        case 'forty_ninth':
                            $query->where('forty_ninth_reserved', false);
                            break;
                        case 'hundredth':
                            $query->where('hundredth_reserved', false);
                            break;
                        case 'anniversary':
                            $query->where('anniversary_reserved', false);
                            break;
                    }
                } else {
                    // 如果沒有指定紀念日類型，顯示所有類型都未預約的記錄
                    $query->where(function($q) {
                        $q->where('seventh_reserved', false)
                          ->where('forty_ninth_reserved', false)
                          ->where('hundredth_reserved', false)
                          ->where('anniversary_reserved', false);
                    });
                }
            }
        }

        $memorialDates = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('memorial_dates.index', compact('memorialDates', 'request'));
    }

    /**
     * 創建紀念日頁面
     */
    public function create()
    {
        // 取得所有佛教/道教的業務單，且尚未建立紀念日的
        $sales = Sale::with(['cust_name', 'memorialDate'])
            ->where('religion', 'buddhism_taoism')
            ->whereNull('death_date')
            ->orWhere(function($query) {
                $query->where('religion', 'buddhism_taoism')
                      ->whereNotNull('death_date')
                      ->whereDoesntHave('memorialDate');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('memorial_dates.create', compact('sales'));
    }

    /**
     * 編輯紀念日頁面
     */
    public function edit($id)
    {
        $memorialDate = MemorialDate::with(['sale.cust_name'])->findOrFail($id);
        
        return view('memorial_dates.edit', compact('memorialDate'));
    }

    /**
     * 更新紀念日
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'seventh_day' => 'nullable|date',
            'seventh_reserved' => 'nullable|boolean',
            'seventh_reserved_at' => 'nullable|date',
            'forty_ninth_day' => 'required|date',
            'forty_ninth_reserved' => 'nullable|boolean',
            'forty_ninth_reserved_at' => 'nullable|date',
            'hundredth_day' => 'required|date',
            'hundredth_reserved' => 'nullable|boolean',
            'hundredth_reserved_at' => 'nullable|date',
            'anniversary_day' => 'nullable|date',
            'anniversary_reserved' => 'nullable|boolean',
            'anniversary_reserved_at' => 'nullable|date',
            'notes' => 'nullable|string|max:2000'
        ]);

        $memorialDate = MemorialDate::findOrFail($id);

        // 獲取原始值
        $original = $memorialDate->getOriginal();

        $payload = [
            'seventh_day' => $request->seventh_day,
            'seventh_reserved' => (bool) $request->seventh_reserved,
            'seventh_reserved_at' => $request->seventh_reserved ? $request->seventh_reserved_at : null,
            'forty_ninth_day' => $request->forty_ninth_day,
            'forty_ninth_reserved' => (bool) $request->forty_ninth_reserved,
            'forty_ninth_reserved_at' => $request->forty_ninth_reserved ? $request->forty_ninth_reserved_at : null,
            'hundredth_day' => $request->hundredth_day,
            'hundredth_reserved' => (bool) $request->hundredth_reserved,
            'hundredth_reserved_at' => $request->hundredth_reserved ? $request->hundredth_reserved_at : null,
            'anniversary_day' => $request->anniversary_day,
            'anniversary_reserved' => (bool) $request->anniversary_reserved,
            'anniversary_reserved_at' => $request->anniversary_reserved ? $request->anniversary_reserved_at : null,
            'notes' => $request->notes,
        ];

        // 建立變更日誌 - 在更新前比較
        try {
            $changes = [];
            
            // 定義需要記錄的欄位（排除系統欄位）
            $trackableFields = [
                'seventh_day', 'seventh_reserved', 'seventh_reserved_at',
                'forty_ninth_day', 'forty_ninth_reserved', 'forty_ninth_reserved_at',
                'hundredth_day', 'hundredth_reserved', 'hundredth_reserved_at',
                'anniversary_day', 'anniversary_reserved', 'anniversary_reserved_at',
                'notes', 'general_notes'
            ];
            
            // 比較變更並記錄（只記錄有意義的欄位）
            foreach ($trackableFields as $field) {
                $oldValue = $original[$field] ?? null;
                $newValue = $payload[$field] ?? null;
                
                // 處理日期格式的比較
                if (strpos($field, '_day') !== false || strpos($field, '_at') !== false) {
                    // 日期欄位：轉換為字串格式進行比較
                    $oldFormatted = $oldValue ? \Carbon\Carbon::parse($oldValue)->format('Y-m-d') : null;
                    $newFormatted = $newValue ? \Carbon\Carbon::parse($newValue)->format('Y-m-d') : null;
                    
                    // 只有當格式化後的值真正不同時才記錄
                    if ($oldFormatted !== $newFormatted) {
                        $changes[$field] = [
                            'old' => $oldValue,
                            'new' => $newValue,
                        ];
                    }
                } else {
                    // 非日期欄位：直接比較
                    if ($oldValue !== $newValue) {
                        $changes[$field] = [
                            'old' => $oldValue,
                            'new' => $newValue,
                        ];
                    }
                }
            }
            
            // 只有當有實際變更時才記錄
            if (!empty($changes)) {
                \App\Models\MemorialDateLog::create([
                    'memorial_date_id' => $memorialDate->id,
                    'user_id' => auth()->id(),
                    'action' => 'update',
                    'changes' => $changes,
                ]);
            }
        } catch (\Throwable $e) {
            // 忽略日誌寫入錯誤，避免影響主流程
        }

        $memorialDate->update($payload);

        return redirect()->route('memorial.dates')
            ->with('success', '紀念日已成功更新');
    }

    /**
     * 儲存或更新重要日期
     */
    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sale_data,id',
            'death_date' => 'required|date|before_or_equal:today',
            'religion' => 'required|in:buddhism,taoism,buddhism_taoism,christianity,catholicism,none,other',
            'plan_id' => 'nullable|exists:plans,id',
            'seventh_day' => 'nullable|date',
            'seventh_reserved' => 'nullable|boolean',
            'seventh_reserved_at' => 'nullable|date',
            'forty_ninth_day' => 'nullable|date',
            'forty_ninth_reserved' => 'nullable|boolean',
            'forty_ninth_reserved_at' => 'nullable|date',
            'hundredth_day' => 'nullable|date',
            'hundredth_reserved' => 'nullable|boolean',
            'hundredth_reserved_at' => 'nullable|date',
            'anniversary_day' => 'nullable|date',
            'anniversary_reserved' => 'nullable|boolean',
            'anniversary_reserved_at' => 'nullable|date',
            'notes' => 'nullable|string|max:2000'
        ]);

        try {
            // 更新業務單的往生日期和宗教資訊
            $sale = Sale::findOrFail($request->sale_id);
            $sale->update([
                'death_date' => $request->death_date,
                'religion' => $request->religion,
                'plan_id' => $request->plan_id
            ]);

            // 計算重要日期（如果沒有手動輸入）
            $calculatedDates = MemorialDate::calculateMemorialDates(
                $request->death_date,
                $request->plan_id
            );

            // 準備儲存數據
            $memorialData = [
                'sale_id' => $request->sale_id,
                'seventh_day' => $request->seventh_day ?: $calculatedDates['seventh_day'],
                'seventh_reserved' => (bool) $request->seventh_reserved,
                'seventh_reserved_at' => $request->seventh_reserved ? $request->seventh_reserved_at : null,
                'forty_ninth_day' => $request->forty_ninth_day ?: $calculatedDates['forty_ninth_day'],
                'forty_ninth_reserved' => (bool) $request->forty_ninth_reserved,
                'forty_ninth_reserved_at' => $request->forty_ninth_reserved ? $request->forty_ninth_reserved_at : null,
                'hundredth_day' => $request->hundredth_day ?: $calculatedDates['hundredth_day'],
                'hundredth_reserved' => (bool) $request->hundredth_reserved,
                'hundredth_reserved_at' => $request->hundredth_reserved ? $request->hundredth_reserved_at : null,
                'anniversary_day' => $request->anniversary_day ?: $calculatedDates['anniversary_day'],
                'anniversary_reserved' => (bool) $request->anniversary_reserved,
                'anniversary_reserved_at' => $request->anniversary_reserved ? $request->anniversary_reserved_at : null,
                'notes' => $request->notes
            ];

            // 儲存或更新重要日期記錄
            $memorialDate = MemorialDate::updateOrCreate(
                ['sale_id' => $request->sale_id],
                $memorialData
            );

            // 建立變更日誌
            try {
                \App\Models\MemorialDateLog::create([
                    'memorial_date_id' => $memorialDate->id,
                    'user_id' => auth()->id(),
                    'action' => 'create',
                    'changes' => $memorialData,
                ]);
            } catch (\Throwable $e) {
                // 忽略日誌寫入錯誤，避免影響主流程
            }

            return redirect()->route('memorial.dates')
                ->with('success', '紀念日已成功建立');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', '儲存失敗：' . $e->getMessage());
        }
    }

    /**
     * 取得重要日期
     */
    public function show($saleId)
    {
        $memorialDate = MemorialDate::where('sale_id', $saleId)->first();

        if (!$memorialDate) {
            return response()->json([
                'success' => false,
                'message' => '找不到重要日期記錄'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'memorial_date' => $memorialDate,
                'formatted_dates' => $memorialDate->formatted_dates,
                'religion_name' => $memorialDate->religion_name,
                'should_display_seventh_day' => $memorialDate->shouldDisplaySeventhDay()
            ]
        ]);
    }

    /**
     * 刪除重要日期記錄
     */
    public function destroy($saleId)
    {
        $memorialDate = MemorialDate::where('sale_id', $saleId)->first();

        if (!$memorialDate) {
            return response()->json([
                'success' => false,
                'message' => '找不到重要日期記錄'
            ], 404);
        }

        $memorialDate->delete();

        return response()->json([
            'success' => true,
            'message' => '重要日期記錄已刪除'
        ]);
    }

    /**
     * 重新計算重要日期（當方案變更時）
     */
    public function recalculate(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sale_data,id',
            'plan_id' => 'required|exists:plans,id'
        ]);

        $memorialDate = MemorialDate::where('sale_id', $request->sale_id)->first();

        if (!$memorialDate) {
            return response()->json([
                'success' => false,
                'message' => '找不到重要日期記錄'
            ], 404);
        }

        try {
            // 重新計算日期
            $calculatedDates = MemorialDate::calculateMemorialDates(
                $memorialDate->death_date,
                $request->plan_id
            );

            // 更新記錄
            $memorialDate->update(array_merge($calculatedDates, [
                'plan_id' => $request->plan_id
            ]));

            return response()->json([
                'success' => true,
                'message' => '重要日期已重新計算',
                'data' => $memorialDate->fresh()->formatted_dates
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '重新計算失敗：' . $e->getMessage()
            ], 500);
        }
    }
}

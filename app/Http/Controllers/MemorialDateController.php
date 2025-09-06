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
        $query = MemorialDate::with(['sale.cust_name']);

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

        // 日期範圍篩選
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

        $memorialDates = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('memorial_dates.index', compact('memorialDates', 'request'));
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
            'forty_ninth_day' => 'required|date',
            'hundredth_day' => 'required|date',
            'anniversary_day' => 'required|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        $memorialDate = MemorialDate::findOrFail($id);
        
        $memorialDate->update([
            'seventh_day' => $request->seventh_day,
            'forty_ninth_day' => $request->forty_ninth_day,
            'hundredth_day' => $request->hundredth_day,
            'anniversary_day' => $request->anniversary_day,
            'notes' => $request->notes
        ]);

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
            'plan_id' => 'nullable|exists:plans,id'
        ]);

        try {
            // 計算重要日期
            $calculatedDates = MemorialDate::calculateMemorialDates(
                $request->death_date,
                $request->plan_id
            );

            // 儲存或更新重要日期記錄
            $memorialDate = MemorialDate::updateOrCreate(
                ['sale_id' => $request->sale_id],
                array_merge($calculatedDates, [
                    'religion' => $request->religion,
                    'plan_id' => $request->plan_id,
                    'notes' => $request->notes
                ])
            );

            return response()->json([
                'success' => true,
                'message' => '重要日期已儲存',
                'data' => $memorialDate->formatted_dates
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '儲存失敗：' . $e->getMessage()
            ], 500);
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

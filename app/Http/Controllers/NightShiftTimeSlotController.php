<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NightShiftTimeSlot;
use Illuminate\Support\Facades\Validator;

class NightShiftTimeSlotController extends Controller
{
    /**
     * 顯示時段列表
     */
    public function index()
    {
        $timeSlots = NightShiftTimeSlot::orderBy('sort_order')
                                      ->orderBy('start_time')
                                      ->get();

        return view('increase.time_slots', compact('timeSlots'));
    }

    /**
     * 儲存新時段
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'min_weight' => 'nullable|numeric|min:0',
            'max_weight' => 'nullable|numeric|min:0|gte:min_weight',
            'price' => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        NightShiftTimeSlot::create($data);

        return redirect()->route('increase.time-slots.index')
                        ->with('success', '時段新增成功！');
    }

    /**
     * 更新時段
     */
    public function update(Request $request, $id)
    {
        $timeSlot = NightShiftTimeSlot::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'min_weight' => 'nullable|numeric|min:0',
            'max_weight' => 'nullable|numeric|min:0|gte:min_weight',
            'price' => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $timeSlot->update($data);

        return redirect()->route('increase.time-slots.index')
                        ->with('success', '時段更新成功！');
    }

    /**
     * 切換時段狀態
     */
    public function toggleStatus($id)
    {
        $timeSlot = NightShiftTimeSlot::findOrFail($id);
        $timeSlot->is_active = !$timeSlot->is_active;
        $timeSlot->save();

        $status = $timeSlot->is_active ? '啟用' : '停用';
        
        return response()->json([
            'success' => true,
            'message' => "時段已{$status}",
            'is_active' => $timeSlot->is_active
        ]);
    }

    /**
     * 刪除時段
     */
    public function destroy($id)
    {
        $timeSlot = NightShiftTimeSlot::findOrFail($id);
        
        // 檢查是否有相關的加成項目使用此時段
        if ($timeSlot->increaseItems()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => '無法刪除，此時段已被使用'
            ], 400);
        }

        $timeSlot->delete();

        return response()->json([
            'success' => true,
            'message' => '時段刪除成功'
        ]);
    }

    /**
     * 取得時段資料（AJAX）
     */
    public function getTimeSlot($id)
    {
        $timeSlot = NightShiftTimeSlot::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $timeSlot
        ]);
    }

    /**
     * 取得所有啟用的時段（AJAX）
     */
    public function getActiveTimeSlots()
    {
        $timeSlots = NightShiftTimeSlot::getActiveTimeSlots();
        
        return response()->json([
            'success' => true,
            'data' => $timeSlots
        ]);
    }
}

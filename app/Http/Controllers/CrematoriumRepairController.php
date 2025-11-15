<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CrematoriumRepair;
use App\Models\CrematoriumRepairDetail;
use App\Models\CrematoriumEquipment;
use App\Models\CrematoriumEquipmentInstance;
use App\Models\CrematoriumEquipmentType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CrematoriumRepairController extends Controller
{
    /**
     * 報修單列表
     */
    public function index(Request $request)
    {
        // 取得篩選參數
        $statusFilter = $request->get('status', '');
        $startDateFilter = $request->get('start_date', '');
        $endDateFilter = $request->get('end_date', '');
        
        // 建立查詢（使用 equipmentInstance）
        $query = CrematoriumRepair::with([
            'reporter', 
            'processor', 
            'repairDetails.equipmentInstance',
            'repairDetails.equipmentInstance.equipmentType'
        ]);
        
        // 狀態篩選
        if ($statusFilter !== '' && $statusFilter !== null) {
            $query->where('status', $statusFilter);
        }
        
        // 日期區間篩選
        if ($startDateFilter !== '' && $startDateFilter !== null) {
            $query->whereDate('report_date', '>=', $startDateFilter);
        }
        
        if ($endDateFilter !== '' && $endDateFilter !== null) {
            $query->whereDate('report_date', '<=', $endDateFilter);
        }
        
        $repairs = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('crematorium.repairs.index', compact('repairs', 'request'));
    }

    /**
     * 新增報修單頁面（使用設備實例）
     */
    public function create()
    {
        // 取得所有已配置的設備實例
        $equipments = CrematoriumEquipmentInstance::with('equipmentType')
            ->orderBy('category')
            ->orderBy('sub_category')
            ->get();
        
        return view('crematorium.repairs.create', compact('equipments'));
    }

    /**
     * 儲存報修單（使用設備實例）
     */
    public function store(Request $request)
    {
        $request->validate([
            'report_date' => 'required|date',
            'problem_description' => 'required|string',
            'equipment_instance_ids' => 'required|array|min:1',
            'equipment_instance_ids.*' => 'exists:crematorium_equipment_instances,id',
            'equipment_problems' => 'nullable|array',
        ]);

        // 生成報修單號：格式 R20251025001（R + 年月日 + 流水號）
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        
        $prefix = 'R' . $year . $month . $day;
        $lastRecord = CrematoriumRepair::where('repair_number', 'LIKE', $prefix . '%')
            ->orderBy('repair_number', 'desc')
            ->first();
        
        if ($lastRecord) {
            $lastNumber = intval(substr($lastRecord->repair_number, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        $repairNumber = $prefix . $newNumber;

        // 創建報修單
        $repair = CrematoriumRepair::create([
            'repair_number' => $repairNumber,
            'reporter_id' => Auth::id(),
            'report_date' => $request->report_date,
            'problem_description' => $request->problem_description,
            'status' => 'pending',
        ]);

        // 創建報修明細（使用 equipment_instance_id）
        foreach ($request->equipment_instance_ids as $index => $equipmentInstanceId) {
            $repair->repairDetails()->create([
                'equipment_instance_id' => $equipmentInstanceId,
                'problem_description' => $request->equipment_problems[$index] ?? null,
            ]);
        }

        return redirect()->route('crematorium.repairs.index')
            ->with('success', '報修單已成功建立！單號：' . $repairNumber);
    }

    /**
     * 查看報修單詳情（使用設備實例）
     */
    public function show($id)
    {
        $repair = CrematoriumRepair::with([
            'reporter', 
            'processor', 
            'repairDetails.equipmentInstance',
            'repairDetails.equipmentInstance.equipmentType'
        ])->findOrFail($id);
        
        return view('crematorium.repairs.show', compact('repair'));
    }

    /**
     * 編輯/處理報修單頁面（使用設備實例）
     */
    public function edit($id)
    {
        $repair = CrematoriumRepair::with([
            'reporter', 
            'processor', 
            'repairDetails.equipmentInstance',
            'repairDetails.equipmentInstance.equipmentType'
        ])->findOrFail($id);
        
        $equipments = CrematoriumEquipmentInstance::with('equipmentType')
            ->orderBy('category')
            ->orderBy('sub_category')
            ->get();
        
        return view('crematorium.repairs.edit', compact('repair', 'equipments'));
    }

    /**
     * 更新/處理報修單
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string',
            'detail_action' => 'nullable|array',
            'detail_quantity' => 'nullable|array',
            'detail_replacement_type' => 'nullable|array',
            'detail_notes' => 'nullable|array',
        ]);

        $repair = CrematoriumRepair::findOrFail($id);

        DB::beginTransaction();
        try {
            // 更新報修單狀態和處理資訊
            $repair->update([
                'processor_id' => Auth::id(),
                'processed_at' => now(),
                'status' => 'completed',
                'notes' => $request->notes,
            ]);

            // 更新報修明細並扣減庫存
            if ($request->has('detail_action')) {
                foreach ($request->detail_action as $detailId => $action) {
                    $detail = CrematoriumRepairDetail::with('equipmentInstance.equipmentType')->find($detailId);
                    if ($detail) {
                        $quantity = $request->detail_quantity[$detailId] ?? 0;
                        $replacementType = $request->detail_replacement_type[$detailId] ?? null;
                        
                        // 更新報修明細
                        $detail->update([
                            'action' => $action,
                            'quantity' => $quantity,
                            'replacement_type' => $replacementType,
                            'notes' => $request->detail_notes[$detailId] ?? null,
                        ]);
                        
                        // 如果處理方式為「更換」，扣減庫存
                        if ($action === 'replace' && $quantity > 0 && $detail->equipmentInstance && $detail->equipmentInstance->equipmentType) {
                            $equipmentType = $detail->equipmentInstance->equipmentType;
                            
                            // 檢查是否列入庫存計算
                            if (!$equipmentType->exclude_from_inventory) {
                                // 根據更換類型扣減庫存
                                if ($replacementType === 'new') {
                                    if ($equipmentType->stock_new < $quantity) {
                                        throw new \Exception("全新庫存不足！設備：{$equipmentType->name}，需要：{$quantity}，剩餘：{$equipmentType->stock_new}");
                                    }
                                    $equipmentType->decrement('stock_new', $quantity);
                                } elseif ($replacementType === 'usable') {
                                    if ($equipmentType->stock_usable < $quantity) {
                                        throw new \Exception("堪用庫存不足！設備：{$equipmentType->name}，需要：{$quantity}，剩餘：{$equipmentType->stock_usable}");
                                    }
                                    $equipmentType->decrement('stock_usable', $quantity);
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('crematorium.repairs.index')
                ->with('success', '報修單已處理完成！已自動扣減庫存。');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', '處理失敗：' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 取消報修單
     */
    public function cancel($id)
    {
        $repair = CrematoriumRepair::findOrFail($id);
        
        $repair->update([
            'status' => 'cancelled',
            'processor_id' => Auth::id(),
            'processed_at' => now(),
        ]);

        return redirect()->route('crematorium.repairs.index')
            ->with('success', '報修單已取消！');
    }
}

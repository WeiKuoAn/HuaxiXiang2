<?php

namespace App\Http\Controllers;

use App\Models\CrematoriumBooking;
use App\Models\CrematoriumEquipmentInstance;
use App\Models\CrematoriumEquipmentType;
use App\Models\CrematoriumMaintenance;
use App\Models\CrematoriumPurchase;
use App\Models\CrematoriumPurchaseItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrematoriumController extends Controller
{
    /**
     * 火化爐預約管理
     */
    public function bookings(Request $request)
    {
        $bookings = CrematoriumBooking::with(['equipment', 'customer'])
            ->orderBy('booking_date', 'desc')
            ->paginate(20);

        $equipments = CrematoriumEquipment::where('status', 'active')->get();

        return view('crematorium.bookings', compact('bookings', 'equipments', 'request'));
    }

    /**
     * 新增預約
     */
    public function createBooking()
    {
        $equipments = CrematoriumEquipment::where('status', 'active')->get();
        return view('crematorium.create_booking', compact('equipments'));
    }

    /**
     * 儲存預約
     */
    public function storeBooking(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|exists:crematorium_equipment,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'booking_date' => 'required|date|after:today',
            'time_slot' => 'required|string',
            'pet_name' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        CrematoriumBooking::create($request->all());

        return redirect()
            ->route('crematorium.bookings')
            ->with('success', '預約新增成功！');
    }

    /**
     * 維護記錄管理
     */
    public function maintenance(Request $request)
    {
        // 取得當前用戶
        $user = auth()->user();

        // 取得篩選參數
        $statusFilter = $request->get('status', '');
        $startDateFilter = $request->get('start_date', '');
        $endDateFilter = $request->get('end_date', '');
        $inspectorFilter = $request->get('inspector', '');
        $maintainerFilter = $request->get('maintainer', '');

        // 建立查詢
        $query = CrematoriumMaintenance::with(['inspectorUser', 'maintainerUser', 'maintenanceDetails']);

        // 權限控制：如果不是管理者（job_id 不是 1、2、3、7、9）
        $managerJobIds = [1, 2, 3, 7, 9];
        $isManager = in_array($user->job_id, $managerJobIds);

        if (!$isManager) {
            // 專員只能看到指派給自己的檢查記錄（檢查人員或保養人員是自己）
            $query->where(function ($q) use ($user) {
                $q
                    ->where('inspector', $user->id)
                    ->orWhere('maintainer', $user->id);
            });
        }

        // 篩選條件（累加篩選，所有非空條件都要符合）

        // 狀態篩選
        if ($statusFilter !== '' && $statusFilter !== null) {
            $query->where('status', (int) $statusFilter);
        }

        // 日期區間篩選
        if ($startDateFilter !== '' && $startDateFilter !== null) {
            $query->whereDate('maintenance_date', '>=', $startDateFilter);
        }

        if ($endDateFilter !== '' && $endDateFilter !== null) {
            $query->whereDate('maintenance_date', '<=', $endDateFilter);
        }

        // 檢查人員篩選
        if ($inspectorFilter !== '' && $inspectorFilter !== null) {
            $query->where('inspector', (int) $inspectorFilter);
        }

        // 保養人員篩選
        if ($maintainerFilter !== '' && $maintainerFilter !== null) {
            $query->where('maintainer', (int) $maintainerFilter);
        }

        $maintenance = $query->orderBy('created_at', 'desc')->whereIn('status', [0, 3])->get();

        // 取得所有員工用於篩選選項
        $staff = User::where('status', '=', '0')->orderBy('name')->get();

        return view('crematorium.maintenance', compact('maintenance', 'staff', 'request', 'isManager'));
    }

    /**
     * 新增維護記錄（指派檢查人員）
     */
    public function createMaintenance()
    {
        $user = auth()->user();

        // 權限檢查：只有管理者可以指派檢查人員
        $managerJobIds = [1, 2, 3, 7, 9];
        if (!in_array($user->job_id, $managerJobIds)) {
            abort(403, '您沒有權限指派檢查人員');
        }

        $staff = User::where('status', '=', '0')->orderBy('name')->get();

        // 生成檢查單號：格式 20251015001（年月日 + 流水號）
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        // 獲取今日最後一筆記錄的流水號
        $prefix = $year . $month . $day;
        $lastRecord = CrematoriumMaintenance::where('maintenance_number', 'LIKE', $prefix . '%')
            ->orderBy('maintenance_number', 'desc')
            ->first();

        if ($lastRecord) {
            // 取得最後的流水號並加1
            $lastNumber = intval(substr($lastRecord->maintenance_number, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            // 今日第一筆
            $newNumber = '001';
        }

        $maintenanceNumber = $prefix . $newNumber;

        return view('crematorium.create_maintenance', compact('staff', 'maintenanceNumber'));
    }

    /**
     * 儲存維護記錄
     */
    public function storeMaintenance(Request $request)
    {
        $data = $request->validate([
            'maintenance_number' => 'nullable|string|max:20|unique:crematorium_maintenance,maintenance_number',
            'maintenance_date' => 'required|date',
            'inspector' => 'required|integer|exists:users,id',
            'maintainer' => 'nullable|integer|exists:users,id',
            'notes' => 'nullable|string',
            'status' => 'nullable|integer|in:0,3,9',
            'power_system_status' => 'nullable|in:good,problem',
            'power_system_problem' => 'nullable|string',
            'high_voltage_wire_status' => 'nullable|in:good,problem',
            'high_voltage_wire_problem' => 'nullable|string',
        ]);

        // 若沒有傳單號，沿用 assignMaintenance 的格式自動產生
        if (empty($data['maintenance_number'])) {
            $prefix = now()->format('Ymd');
            $lastRecord = CrematoriumMaintenance::where('maintenance_number', 'LIKE', $prefix . '%')
                ->orderBy('maintenance_number', 'desc')
                ->first();
            $sequence = $lastRecord ? intval(substr($lastRecord->maintenance_number, -3)) + 1 : 1;
            $data['maintenance_number'] = $prefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);
        }

        $data['status'] = $data['status'] ?? 0;

        CrematoriumMaintenance::create($data);

        return redirect()
            ->route('crematorium.maintenance')
            ->with('success', '檢查記錄新增成功！');
    }

    /**
     * 查看維護記錄詳情
     */
    public function showMaintenance($id)
    {
        $maintenance = CrematoriumMaintenance::with([
            'maintenanceDetails.equipmentInstance.equipmentType',
            'inspectorUser',
            'maintainerUser'
        ])->findOrFail($id);

        return view('crematorium.maintenance_detail', compact('maintenance'));
    }

    /**
     * 指派檢查人員
     */
    public function assignMaintenance(Request $request)
    {
        $user = auth()->user();

        // 權限檢查：只有管理者可以指派檢查人員
        $managerJobIds = [1, 2, 3, 7, 9];
        if (!in_array($user->job_id, $managerJobIds)) {
            abort(403, '您沒有權限指派檢查人員');
        }

        // 驗證請求
        $request->validate([
            'maintenance_number' => 'required|string|unique:crematorium_maintenance,maintenance_number',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'assigned_inspector' => 'required|integer|exists:users,id',
            'assigned_maintainer' => 'nullable|integer|exists:users,id',
            'instructions' => 'nullable|string|max:1000',
        ]);

        // 創建檢查任務記錄
        $maintenance = CrematoriumMaintenance::create([
            'maintenance_number' => $request->maintenance_number,
            'maintenance_date' => $request->scheduled_date,
            'inspector' => $request->assigned_inspector,
            'maintainer' => $request->assigned_maintainer,
            'notes' => $request->instructions,
            'status' => 0,  // 未檢查
        ]);

        return redirect()
            ->route('crematorium.maintenance')
            ->with('success', '檢查任務已成功指派！單號：' . $request->maintenance_number);
    }

    /**
     * 編輯維護記錄
     */
    public function editMaintenance($id)
    {
        $user = auth()->user();
        $maintenance = CrematoriumMaintenance::with([
            'maintenanceDetails',
            'maintenanceDetails.equipmentInstance',
            'maintenanceDetails.equipmentInstance.equipmentType',
            'inspectorUser',
            'maintainerUser'
        ])->findOrFail($id);

        // 權限檢查：如果不是管理者，只能編輯指派給自己的記錄
        $managerJobIds = [1, 2, 3, 7, 9];
        $isManager = in_array($user->job_id, $managerJobIds);

        if (!$isManager) {
            // 檢查是否為指派給自己的檢查記錄
            if ($maintenance->inspector != $user->id && $maintenance->maintainer != $user->id) {
                abort(403, '您沒有權限編輯此檢查記錄');
            }
        }

        // 使用設備實例（已配置到火化爐的設備）
        $equipments = CrematoriumEquipmentInstance::with('equipmentType')
            ->orderBy('category')
            ->orderBy('sub_category')
            ->get();

        return view('crematorium.edit_maintenance', compact('maintenance', 'equipments'));
    }

    /**
     * 測試編輯頁面（不需要ID）
     */
    public function testEditMaintenance()
    {
        // 建立空的維護記錄物件供前端測試
        $maintenance = new CrematoriumMaintenance();

        // 使用設備實例（已配置到火化爐的設備）
        $equipments = CrematoriumEquipmentInstance::with('equipmentType')
            ->orderBy('category')
            ->orderBy('sub_category')
            ->get();

        return view('crematorium.edit_maintenance', compact('maintenance', 'equipments'));
    }

    /**
     * 更新維護記錄
     */
    public function updateMaintenance(Request $request, $id)
    {
        $user = auth()->user();
        $maintenance = CrematoriumMaintenance::findOrFail($id);

        // 權限檢查：如果不是管理者，只能更新指派給自己的記錄
        $managerJobIds = [1, 2, 3, 7, 9];
        $isManager = in_array($user->job_id, $managerJobIds);

        if (!$isManager) {
            if ($maintenance->inspector != $user->id && $maintenance->maintainer != $user->id) {
                abort(403, '您沒有權限更新此檢查記錄');
            }
        }

        // 驗證請求
        $request->validate([
            'maintenance_date' => 'required|date',
            'power_system_status' => 'nullable|in:good,problem',
            'power_system_problem' => 'nullable|string|max:500',
            'high_voltage_wire_status' => 'nullable|in:good,problem',
            'high_voltage_wire_problem' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'equipment_status' => 'nullable|array',
            'equipment_problem' => 'nullable|array',
            'equipment_action' => 'nullable|array',
            'equipment_quantity' => 'nullable|array',
            'equipment_replacement_type' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // 更新維護記錄
            $maintenance->update([
                'maintenance_date' => $request->maintenance_date,
                'power_system_status' => $request->power_system_status,
                'power_system_problem' => $request->power_system_problem,
                'high_voltage_wire_status' => $request->high_voltage_wire_status,
                'high_voltage_wire_problem' => $request->high_voltage_wire_problem,
                'notes' => $request->notes,
                'status' => 3,  // 送審
            ]);

            // 處理設備檢查記錄（使用 equipment_instance_id）並扣減庫存
            if ($request->has('equipment_status')) {
                foreach ($request->equipment_status as $equipmentInstanceId => $status) {
                    $problemDescription = $request->equipment_problem[$equipmentInstanceId] ?? null;
                    $action = $request->equipment_action[$equipmentInstanceId] ?? null;
                    $quantity = $request->equipment_quantity[$equipmentInstanceId] ?? 0;
                    $replacementType = $request->equipment_replacement_type[$equipmentInstanceId] ?? 'new';

                    // 更新或創建檢查記錄
                    $maintenance->maintenanceDetails()->updateOrCreate(
                        [
                            'maintenance_id' => $maintenance->id,
                            'equipment_instance_id' => $equipmentInstanceId,
                        ],
                        [
                            'status' => $status,
                            'problem_description' => $problemDescription,
                            'action' => $action,
                            'quantity' => $quantity,
                            'replacement_type' => $replacementType,
                        ]
                    );

                    // 如果處理方式為「更換」，扣減庫存
                    if ($action === 'replace' && $quantity > 0) {
                        $equipmentInstance = CrematoriumEquipmentInstance::with('equipmentType')->find($equipmentInstanceId);

                        if ($equipmentInstance && $equipmentInstance->equipmentType) {
                            $equipmentType = $equipmentInstance->equipmentType;

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
            return redirect()
                ->route('crematorium.maintenance')
                ->with('success', '檢查記錄已更新並送審！已自動扣減庫存。');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', '更新失敗：' . $e->getMessage())
                ->withInput();
        }
    }

    public function checkMaintenance($id)
    {
        $user = auth()->user();
        $maintenance = CrematoriumMaintenance::with([
            'maintenanceDetails',
            'maintenanceDetails.equipmentInstance',
            'maintenanceDetails.equipmentInstance.equipmentType',
            'inspectorUser',
            'maintainerUser'
        ])->findOrFail($id);

        // 權限檢查：如果不是管理者，只能編輯指派給自己的記錄
        $managerJobIds = [1, 2, 3, 7, 9];
        $isManager = in_array($user->job_id, $managerJobIds);

        if (!$isManager) {
            // 檢查是否為指派給自己的檢查記錄
            if ($maintenance->inspector != $user->id && $maintenance->maintainer != $user->id) {
                abort(403, '您沒有權限編輯此檢查記錄');
            }
        }

        // 使用設備實例（已配置到火化爐的設備）
        $equipments = CrematoriumEquipmentInstance::with('equipmentType')
            ->orderBy('category')
            ->orderBy('sub_category')
            ->get();

        return view('crematorium.check_maintenance', compact('maintenance', 'equipments'));
    }

    public function checkMaintenanceUpdate(Request $request, $id)
    {
        $user = auth()->user();
        $maintenance = CrematoriumMaintenance::findOrFail($id);
        if ($request->submit == 'check') {
            if($user->job_id == 1 || $user->job_id == 2 || $user->job_id == 3 || $user->job_id == 7 || $user->job_id == 10) {
                $maintenance->update(['status' => 9]);
                return redirect()->route('crematorium.maintenance')->with('success', '檢查記錄確認審核成功！');
            } else {
                $maintenance->update(['status' => 3]);
                return redirect()->route('crematorium.maintenance')->with('success', '檢查記錄送出審核成功！');
            }
        }
        return redirect()->route('crematorium.maintenance')->with('error', '您沒有權限審核此檢查記錄！');
    }

    /**
     * 進貨記錄列表
     */
    public function purchasesIndex(Request $request)
    {
        $query = CrematoriumPurchase::with(['items.equipmentType', 'purchaser']);

        // 篩選條件
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('equipment_type_id')) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('equipment_type_id', $request->equipment_type_id);
            });
        }

        if ($request->filled('start_date')) {
            $query->where('purchase_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('purchase_date', '<=', $request->end_date);
        }

        $purchases = $query
            ->orderBy('purchase_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        $equipmentTypes = CrematoriumEquipmentType::orderBy('name')->get();

        return view('crematorium.purchases_index', compact('purchases', 'equipmentTypes', 'request'));
    }

    /**
     * 新增進貨記錄頁面
     */
    public function purchasesCreate()
    {
        $equipmentTypes = CrematoriumEquipmentType::orderBy('name')->get();

        $purchaseNumber = CrematoriumPurchase::generatePurchaseNumber();

        return view('crematorium.purchases_create', compact('equipmentTypes', 'purchaseNumber'));
    }

    /**
     * 儲存進貨記錄
     */
    public function purchasesStore(Request $request)
    {
        $request->validate([
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string',
            'equipment_type_id' => 'required|array|min:1',
            'equipment_type_id.*' => 'required|exists:crematorium_equipment_types,id',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|integer|min:1',
            'unit_price' => 'nullable|array',
            'unit_price.*' => 'nullable|numeric|min:0',
            'item_notes' => 'nullable|array',
            'item_notes.*' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 計算總金額
            $totalPrice = 0;
            foreach ($request->equipment_type_id as $key => $equipmentTypeId) {
                if (!empty($equipmentTypeId)) {
                    $quantity = $request->quantity[$key] ?? 0;
                    $unitPrice = $request->unit_price[$key] ?? 0;
                    $totalPrice += $quantity * $unitPrice;
                }
            }

            // 創建進貨記錄
            $purchase = CrematoriumPurchase::create([
                'purchase_number' => CrematoriumPurchase::generatePurchaseNumber(),
                'purchase_date' => $request->purchase_date,
                'total_price' => $totalPrice,
                'supplier' => null,
                'invoice_number' => null,
                'purchaser_id' => auth()->id(),
                'notes' => $request->notes,
                'status' => 'confirmed',  // 直接確認
            ]);

            // 創建進貨明細並更新庫存
            foreach ($request->equipment_type_id as $key => $equipmentTypeId) {
                if (!empty($equipmentTypeId)) {
                    $quantity = $request->quantity[$key];
                    $unitPrice = $request->unit_price[$key] ?? null;
                    $subtotal = $unitPrice ? $quantity * $unitPrice : null;

                    // 創建明細
                    CrematoriumPurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'equipment_type_id' => $equipmentTypeId,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                        'notes' => $request->item_notes[$key] ?? null,
                    ]);

                    // 更新設備類型庫存
                    $equipmentType = CrematoriumEquipmentType::findOrFail($equipmentTypeId);
                    $equipmentType->increment('stock_new', $quantity);
                }
            }

            DB::commit();

            return redirect()
                ->route('crematorium.purchases.index')
                ->with('success', '進貨記錄新增成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', '進貨記錄新增失敗：' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 編輯進貨記錄頁面
     */
    public function purchasesEdit($id)
    {
        $purchase = CrematoriumPurchase::with(['items.equipmentType', 'purchaser'])->findOrFail($id);

        // 只有待確認狀態的記錄才能編輯
        if ($purchase->status !== 'pending') {
            return redirect()
                ->route('crematorium.purchases.index')
                ->with('error', '只有待確認狀態的進貨記錄才能編輯！');
        }

        $equipmentTypes = CrematoriumEquipmentType::orderBy('name')->get();

        return view('crematorium.purchases_edit', compact('purchase', 'equipmentTypes'));
    }

    /**
     * 更新進貨記錄
     */
    public function purchasesUpdate(Request $request, $id)
    {
        $purchase = CrematoriumPurchase::findOrFail($id);

        // 只有待確認狀態的記錄才能編輯
        if ($purchase->status !== 'pending') {
            return redirect()
                ->route('crematorium.purchases.index')
                ->with('error', '只有待確認狀態的進貨記錄才能編輯！');
        }

        $request->validate([
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string',
            'equipment_type_id' => 'required|array|min:1',
            'equipment_type_id.*' => 'required|exists:crematorium_equipment_types,id',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|integer|min:1',
            'unit_price' => 'nullable|array',
            'unit_price.*' => 'nullable|numeric|min:0',
            'item_notes' => 'nullable|array',
            'item_notes.*' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 計算總金額
            $totalPrice = 0;
            foreach ($request->equipment_type_id as $key => $equipmentTypeId) {
                if (!empty($equipmentTypeId)) {
                    $quantity = $request->quantity[$key] ?? 0;
                    $unitPrice = $request->unit_price[$key] ?? 0;
                    $totalPrice += $quantity * $unitPrice;
                }
            }

            // 更新進貨記錄
            $purchase->update([
                'purchase_date' => $request->purchase_date,
                'total_price' => $totalPrice,
                'notes' => $request->notes,
            ]);

            // 刪除舊的明細
            $purchase->items()->delete();

            // 創建新的明細
            foreach ($request->equipment_type_id as $key => $equipmentTypeId) {
                if (!empty($equipmentTypeId)) {
                    $quantity = $request->quantity[$key];
                    $unitPrice = $request->unit_price[$key] ?? null;
                    $subtotal = $unitPrice ? $quantity * $unitPrice : null;

                    CrematoriumPurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'equipment_type_id' => $equipmentTypeId,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                        'notes' => $request->item_notes[$key] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('crematorium.purchases.index')
                ->with('success', '進貨記錄更新成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', '進貨記錄更新失敗：' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 刪除進貨記錄
     */
    public function purchasesDestroy($id)
    {
        $purchase = CrematoriumPurchase::findOrFail($id);

        // 只有待確認狀態的記錄才能刪除
        if ($purchase->status !== 'pending') {
            return redirect()
                ->route('crematorium.purchases.index')
                ->with('error', '只有待確認狀態的進貨記錄才能刪除！');
        }

        DB::beginTransaction();
        try {
            $purchase->delete();

            DB::commit();

            return redirect()
                ->route('crematorium.purchases.index')
                ->with('success', '進貨記錄刪除成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('crematorium.purchases.index')
                ->with('error', '進貨記錄刪除失敗：' . $e->getMessage());
        }
    }
}

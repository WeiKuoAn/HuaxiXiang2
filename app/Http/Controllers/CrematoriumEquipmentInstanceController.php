<?php

namespace App\Http\Controllers;

use App\Models\CrematoriumEquipmentType;
use App\Models\CrematoriumEquipmentInstance;
use Illuminate\Http\Request;

class CrematoriumEquipmentInstanceController extends Controller
{
    /**
     * 火化爐設定（按位置顯示所有零件）
     */
    public function index(Request $request)
    {
        // 取得所有設備實例，按火化爐位置分組顯示
        $instances = CrematoriumEquipmentInstance::with('equipmentType')
            ->orderBy('category')
            ->orderBy('sub_category')
            ->get();
        
        return view('crematorium.instances_index', compact('instances'));
    }

    /**
     * 顯示新增設備實例表單
     */
    public function create()
    {
        $equipmentTypes = CrematoriumEquipmentType::orderBy('name')->get();
        return view('crematorium.instances_create', compact('equipmentTypes'));
    }

    /**
     * 儲存新設備實例
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_type_id' => 'required|exists:crematorium_equipment_types,id',
            'category' => 'required|in:furnace_1,furnace_2,ventilation',
            'sub_category' => 'nullable|in:fire_1,fire_2,fire_1a,fire_1b',
            'status' => 'required|in:active,maintenance,broken,inactive',
            'installed_date' => 'nullable|date',
            'last_maintenance_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // 建立完整位置描述
        $categoryText = ['furnace_1' => '一爐', 'furnace_2' => '二爐', 'ventilation' => '抽風'][$validated['category']];
        $subText = '';
        if ($validated['sub_category']) {
            $subMap = ['fire_1' => '一火', 'fire_2' => '二火', 'fire_1a' => '一火A', 'fire_1b' => '一火B'];
            $subText = '-' . $subMap[$validated['sub_category']];
        }
        $validated['location'] = $categoryText . $subText;

        CrematoriumEquipmentInstance::create($validated);

        return redirect()->route('crematorium.instances.index')
            ->with('success', '設備配置成功');
    }

    /**
     * 顯示編輯設備實例表單
     */
    public function edit($id)
    {
        $instance = CrematoriumEquipmentInstance::with('equipmentType')->findOrFail($id);
        $equipmentTypes = CrematoriumEquipmentType::orderBy('name')->get();
        
        return view('crematorium.instances_edit', compact('instance', 'equipmentTypes'));
    }

    /**
     * 更新設備實例
     */
    public function update(Request $request, $id)
    {
        $instance = CrematoriumEquipmentInstance::findOrFail($id);
        
        $validated = $request->validate([
            'equipment_type_id' => 'required|exists:crematorium_equipment_types,id',
            'category' => 'required|in:furnace_1,furnace_2,ventilation',
            'sub_category' => 'nullable|in:fire_1,fire_2,fire_1a,fire_1b',
            'status' => 'required|in:active,maintenance,broken,inactive',
            'installed_date' => 'nullable|date',
            'last_maintenance_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // 建立完整位置描述
        $categoryText = ['furnace_1' => '一爐', 'furnace_2' => '二爐', 'ventilation' => '抽風'][$validated['category']];
        $subText = '';
        if ($validated['sub_category']) {
            $subMap = ['fire_1' => '一火', 'fire_2' => '二火', 'fire_1a' => '一火A', 'fire_1b' => '一火B'];
            $subText = '-' . $subMap[$validated['sub_category']];
        }
        $validated['location'] = $categoryText . $subText;

        $instance->update($validated);

        return redirect()->route('crematorium.instances.index')
            ->with('success', '設備配置更新成功');
    }

    /**
     * 刪除設備實例
     */
    public function destroy($id)
    {
        $instance = CrematoriumEquipmentInstance::findOrFail($id);
        $instance->delete();

        return redirect()->route('crematorium.instances.index')
            ->with('success', '設備實例刪除成功');
    }

    /**
     * 快速更新設備狀態
     */
    public function updateStatus(Request $request, $id)
    {
        $instance = CrematoriumEquipmentInstance::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:active,maintenance,broken,inactive',
        ]);

        $instance->update($validated);

        return redirect()->back()->with('success', '設備狀態更新成功');
    }

    /**
     * 標記設備為故障
     */
    public function markAsBroken(Request $request, $id)
    {
        $instance = CrematoriumEquipmentInstance::findOrFail($id);
        
        $instance->markAsBroken($request->input('notes'));

        return redirect()->back()->with('success', '設備已標記為故障');
    }

    /**
     * 標記設備維護完成
     */
    public function markAsActive($id)
    {
        $instance = CrematoriumEquipmentInstance::findOrFail($id);
        
        $instance->markAsActive();

        return redirect()->back()->with('success', '設備維護完成，已恢復正常');
    }
}


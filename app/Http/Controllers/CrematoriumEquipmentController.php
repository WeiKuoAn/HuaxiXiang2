<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CrematoriumEquipment;
use App\Models\CrematoriumEquipmentType;
use App\Models\CrematoriumEquipmentInstance;

class CrematoriumEquipmentController extends Controller
{
    /**
     * 火化爐設備類型列表（庫存管理）
     */
    public function index(Request $request)
    {
        $categoryFilter = $request->get('category', '');
        
        // 使用新的設備類型表
        $query = CrematoriumEquipmentType::with(['instances', 'activeInstances', 'brokenInstances']);
        
        // 類別篩選改為透過設備實例的位置來篩選
        if ($categoryFilter) {
            $query->whereHas('instances', function($q) use ($categoryFilter) {
                $q->where('category', $categoryFilter);
            });
        }
        
        $equipmentTypes = $query->orderBy('name')->get();
        
        return view('crematorium.index', compact('equipmentTypes', 'categoryFilter'));
    }

    /**
     * 新增火化爐設備頁面
     */
    public function create()
    {
        return view('crematorium.create');
    }

    /**
     * 儲存新設備類型（零件）
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'exclude_from_inventory' => 'nullable|boolean',
            'stock_new' => 'nullable|integer|min:0',
            'stock_usable' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'locations' => 'nullable|array',
        ]);

        $data = [
            'name' => $request->name,
            'exclude_from_inventory' => $request->has('exclude_from_inventory'),
            'stock_new' => $request->stock_new ?? 0,
            'stock_usable' => $request->stock_usable ?? 0,
            'description' => $request->description,
        ];
        
        // 如果不列入庫存，強制設為0
        if ($data['exclude_from_inventory']) {
            $data['stock_new'] = 0;
            $data['stock_usable'] = 0;
        }

        // 創建設備類型
        $equipmentType = CrematoriumEquipmentType::create($data);

        // 根據勾選的位置，自動創建設備實例
        if ($request->has('locations') && is_array($request->locations)) {
            foreach ($request->locations as $location) {
                list($category, $subCategory) = explode('|', $location);
                
                $locationText = ['furnace_1' => '一爐', 'furnace_2' => '二爐', 'ventilation' => '抽風'][$category];
                if ($subCategory) {
                    $subMap = ['fire_1' => '一火', 'fire_2' => '二火', 'fire_1a' => '一火A', 'fire_1b' => '一火B'];
                    $locationText .= '-' . $subMap[$subCategory];
                }
                
                CrematoriumEquipmentInstance::create([
                    'equipment_type_id' => $equipmentType->id,
                    'category' => $category,
                    'sub_category' => $subCategory ?: null,
                    'location' => $locationText,
                    'status' => 'active',
                ]);
            }
        }

        return redirect()->route('crematorium.equipment.index')
            ->with('success', '設備類型新增成功，已配置到 ' . count($request->locations ?? []) . ' 個位置');
    }

    /**
     * 編輯設備類型頁面
     */
    public function edit($id)
    {
        $equipmentType = CrematoriumEquipmentType::with('instances')->findOrFail($id);
        return view('crematorium.edit', compact('equipmentType'));
    }

    /**
     * 更新設備類型和配置位置
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'exclude_from_inventory' => 'nullable|boolean',
            'stock_new' => 'nullable|integer|min:0',
            'stock_usable' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'locations' => 'nullable|array',
        ]);

        $equipmentType = CrematoriumEquipmentType::findOrFail($id);
        
        $data = [
            'name' => $request->name,
            'exclude_from_inventory' => $request->has('exclude_from_inventory'),
            'stock_new' => $request->stock_new ?? 0,
            'stock_usable' => $request->stock_usable ?? 0,
            'description' => $request->description,
        ];
        
        // 如果不列入庫存，強制設為0
        if ($data['exclude_from_inventory']) {
            $data['stock_new'] = 0;
            $data['stock_usable'] = 0;
        }
        
        $equipmentType->update($data);

        // 同步配置位置
        $newLocations = $request->locations ?? [];
        
        // 先刪除所有現有配置
        CrematoriumEquipmentInstance::where('equipment_type_id', $id)->delete();
        
        // 重新創建選中的配置
        foreach ($newLocations as $location) {
            list($category, $subCategory) = explode('|', $location);
            
            $locationText = ['furnace_1' => '一爐', 'furnace_2' => '二爐', 'ventilation' => '抽風'][$category];
            if ($subCategory) {
                $subMap = ['fire_1' => '一火', 'fire_2' => '二火', 'fire_1a' => '一火A', 'fire_1b' => '一火B'];
                $locationText .= '-' . $subMap[$subCategory];
            }
            
            CrematoriumEquipmentInstance::create([
                'equipment_type_id' => $equipmentType->id,
                'category' => $category,
                'sub_category' => $subCategory ?: null,
                'location' => $locationText,
                'status' => 'active',
            ]);
        }

        return redirect()->route('crematorium.equipment.index')
            ->with('success', '設備類型和配置更新成功！');
    }

    /**
     * 刪除設備類型
     */
    public function destroy($id)
    {
        try {
            $equipmentType = CrematoriumEquipmentType::findOrFail($id);
            
            // 檢查是否有關聯的設備實例
            if ($equipmentType->instances()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => '此設備類型下還有 ' . $equipmentType->instances()->count() . ' 個設備實例，無法刪除'
                ], 400);
            }
            
            $equipmentType->delete();

            return response()->json([
                'success' => true,
                'message' => '設備類型刪除成功'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '刪除失敗：' . $e->getMessage()
            ], 500);
        }
    }
}

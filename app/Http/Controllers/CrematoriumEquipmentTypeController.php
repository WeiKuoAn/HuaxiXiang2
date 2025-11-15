<?php

namespace App\Http\Controllers;

use App\Models\CrematoriumEquipmentType;
use App\Models\CrematoriumEquipmentInstance;
use Illuminate\Http\Request;

class CrematoriumEquipmentTypeController extends Controller
{
    /**
     * 顯示設備類型列表（庫存管理）
     */
    public function index(Request $request)
    {
        $categoryFilter = $request->get('category', '');
        
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
     * 顯示新增設備類型表單
     */
    public function create()
    {
        return view('crematorium.create');
    }

    /**
     * 儲存新設備類型
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:furnace_1,furnace_2,ventilation',
            'sub_category' => 'nullable|in:fire_1,fire_2,fire_1a,fire_1b',
            'stock_new' => 'required|integer|min:0',
            'stock_usable' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        CrematoriumEquipmentType::create($validated);

        return redirect()->route('crematorium.types.index')
            ->with('success', '設備類型新增成功');
    }

    /**
     * 顯示編輯設備類型表單
     */
    public function edit($id)
    {
        $equipmentType = CrematoriumEquipmentType::with('instances')->findOrFail($id);
        return view('crematorium.edit', compact('equipmentType'));
    }

    /**
     * 更新設備類型
     */
    public function update(Request $request, $id)
    {
        $equipmentType = CrematoriumEquipmentType::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:furnace_1,furnace_2,ventilation',
            'sub_category' => 'nullable|in:fire_1,fire_2,fire_1a,fire_1b',
            'stock_new' => 'required|integer|min:0',
            'stock_usable' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $equipmentType->update($validated);

        return redirect()->route('crematorium.types.index')
            ->with('success', '設備類型更新成功');
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

    /**
     * 顯示設備類型詳情（包含所有實例）
     */
    public function show($id)
    {
        $equipmentType = CrematoriumEquipmentType::with(['instances' => function($query) {
            $query->orderBy('location')->orderBy('status');
        }])->findOrFail($id);
        
        return view('crematorium.show', compact('equipmentType'));
    }
}


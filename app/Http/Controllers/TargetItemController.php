<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TargetItem;

class TargetItemController extends Controller
{
    public function update(Request $request, $id)
    {
        // 驗證請求數據
        $validated = $request->validate([
            'status' => 'required|in:進行中,已完成,未達標',
            'manual_achieved' => 'required|integer|min:0',
            'gift' => 'nullable|string|max:255',
        ]);

        // 找到對應的 TargetItem
        $targetItem = TargetItem::findOrFail($id);

        // 更新數據
        $targetItem->update([
            'status' => $validated['status'],
            'manual_achieved' => $validated['manual_achieved'],
            'gift' => $validated['gift'],
        ]);

        return redirect()->back()->with('success', '達標數據更新成功！');
    }
}

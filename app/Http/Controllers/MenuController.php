<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {
        // 主選單管理頁，只顯示 parent_id=null
        $datas = Menu::whereNull('parent_id')->orderBy('sort', 'asc')->get();
        return view('menu.index', compact('datas'));
    }

    public function subMenu($parentId)
    {
        $parent = Menu::findOrFail($parentId);
        $datas = Menu::where('parent_id', $parentId)->orderBy('sort', 'asc')->get();
        return view('menu.sub', compact('datas', 'parent'));
    }

    public function create()
    {
        $datas = Menu::where('url','like','#%')->get();
        return view('menu.create', compact('datas'));
    }

    public function store(Request $request)
    {
        $menu = new Menu();
        $menu->name = $request->name;
        $menu->type = $request->type;
        $menu->slug = $request->slug;
        $menu->parent_id = $request->parent_id;
        $menu->url = $request->url;
        $menu->icon = $request->icon;
        $menu->sort = $request->sort;
        $menu->comment = $request->comment;
        $menu->save();

        return redirect()->route('menu.create');
    }

    public function show($id)
    {
        $data = Menu::findOrFail($id);
        $parent_menus = Menu::whereNull('parent_id')->get();
        return view('menu.edit', compact('parent_menus', 'data'));
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->name = $request->name;
        $menu->type = $request->type;
        $menu->slug = $request->slug;
        $menu->parent_id = $request->parent_id;
        $menu->url = $request->url;
        $menu->icon = $request->icon;
        $menu->sort = $request->sort;
        $menu->comment = $request->comment;
        $menu->save();
        return redirect()->route('menu.index');
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*.id' => ['required', 'integer', 'exists:menu,id'],
            'order.*.sort' => ['required', 'integer'],
            'order.*.parent_id' => ['nullable', 'integer', 'exists:menu,id'],
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
                $orderData = $request->input('order', []);
                foreach ($orderData as $item) {
                    $menu = Menu::find($item['id']);
                    if ($menu) {
                        $menu->sort = $item['sort'];
                        // 若有 parent_id 欄位則一併更新
                        if (array_key_exists('parent_id', $item)) {
                            $menu->parent_id = $item['parent_id'];
                        }
                        $menu->save();
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => '排序已成功更新'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => '資料驗證失敗', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '排序更新失敗：' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $menu = Menu::findOrFail($id);
            
            // 檢查是否有子選單
            $hasChildren = Menu::where('parent_id', $id)->exists();
            if ($hasChildren) {
                return response()->json([
                    'success' => false,
                    'message' => '無法刪除有子選單的項目，請先刪除子選單'
                ]);
            }
            
            // 檢查是否有職稱選單配對使用此選單
            $hasJobMenus = \App\Models\JobMenu::where('menu_id', $id)->exists();
            if ($hasJobMenus) {
                return response()->json([
                    'success' => false,
                    'message' => '無法刪除已被職稱選單配對使用的選單'
                ]);
            }
            
            $menu->delete();
            
            return response()->json([
                'success' => true,
                'message' => '選單已成功刪除'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '刪除失敗：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 計算選單層級
     */
    private function calculateLevel($menu, $allMenus, $level = 0)
    {
        if ($menu->parent_id === null) {
            return $level;
        }
        
        $parent = $allMenus->where('id', $menu->parent_id)->first();
        if ($parent) {
            return $this->calculateLevel($parent, $allMenus, $level + 1);
        }
        
        return $level;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncreaseSetting;
use Illuminate\Support\Facades\Auth;

class IncreaseSettingController extends Controller
{
    /**
     * 顯示加成設定列表
     */
    public function index()
    {
        $settings = IncreaseSetting::orderBy('type')->get();
        
        return view('increase_setting.index', compact('settings'));
    }

    /**
     * 顯示編輯表單
     */
    public function edit($id)
    {
        $setting = IncreaseSetting::findOrFail($id);
        
        return view('increase_setting.edit', compact('setting'));
    }

    /**
     * 更新加成設定
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'phone_bonus' => 'required|numeric|min:0',
            'receive_bonus' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $setting = IncreaseSetting::findOrFail($id);
        
        $setting->update([
            'phone_bonus' => $request->phone_bonus,
            'receive_bonus' => $request->receive_bonus,
            'status' => $request->status
        ]);

        return redirect()->route('increase-setting.index')
            ->with('success', '加成設定已更新成功！');
    }

    /**
     * 批次更新所有設定
     */
    public function batchUpdate(Request $request)
    {
        try {
            $request->validate([
                'settings' => 'required|array',
                'settings.*.id' => 'required|exists:increase_settings,id',
                'settings.*.phone_bonus' => 'required|numeric|min:0',
                'settings.*.receive_bonus' => 'required|numeric|min:0',
                'settings.*.status' => 'required|in:active,inactive'
            ]);

            foreach ($request->settings as $settingData) {
                $setting = IncreaseSetting::find($settingData['id']);
                if ($setting) {
                    // 確保 status 正確設定
                    $status = $settingData['status'];
                    if ($status !== 'active' && $status !== 'inactive') {
                        $status = 'active';
                    }
                    
                    $setting->update([
                        'phone_bonus' => $settingData['phone_bonus'],
                        'receive_bonus' => $settingData['receive_bonus'],
                        'status' => $status
                    ]);
                }
            }

            return redirect()->route('increase-setting.index')
                ->with('success', '所有加成設定已更新成功！');
                
        } catch (\Exception $e) {
            return redirect()->route('increase-setting.index')
                ->with('error', '更新失敗：' . $e->getMessage());
        }
    }
}


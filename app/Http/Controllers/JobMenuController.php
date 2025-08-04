<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobMenu;
use App\Models\Job;
use App\Models\Menu;

class JobMenuController extends Controller
{
    public function index()
    {
        $datas = Job::with(['users', 'director_data'])->get();
        
        // 為每個職稱計算人數
        foreach($datas as $job) {
            $job->active_user_count = $job->users()->where('status', '0')->count();
            $job->total_user_count = $job->users()->count();
        }
        
        return view('job_menu.index', compact('datas'));
    }

    public function create()
    {
        $jobs = Job::get();
        $menus = Menu::orderby('sort','asc')->orderby('created_at')->get();
        return view('job_menu.create')->with([
            'jobs' => $jobs,
            'menus' => $menus
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'menu_ids' => 'array',
            'menu_ids.*' => 'exists:menus,id'
        ]);

        $jobId = $request->job_id;
        $menuIds = $request->input('menu_ids', []); // menu_ids[] 是 array
        
        // 檢查是否已經存在該職稱的選單配對
        $existingCount = JobMenu::where('job_id', $jobId)->count();
        if ($existingCount > 0) {
            return redirect()->back()->with('error', '該職稱已有選單配對，請使用編輯功能');
        }
        
        foreach ($menuIds as $menuId) {
            $jobMenu = new JobMenu();
            $jobMenu->job_id = $jobId;
            $jobMenu->menu_id = $menuId;
            $jobMenu->save();
        }

        return redirect()->route('job.menu.index')->with('success', '職稱選單配對新增成功');
    }

    public function show($id)
    {
        $job = Job::findOrFail($id);
        $jobs = Job::get();
        $menus = Menu::orderby('sort','asc')->orderby('created_at')->get();
        
        // 獲取該職稱已選擇的選單 ID
        $selectedMenuIds = JobMenu::where('job_id', $id)->pluck('menu_id')->toArray();
        
        return view('job_menu.edit')->with([
            'job' => $job,
            'jobs' => $jobs,
            'menus' => $menus,
            'selectedMenuIds' => $selectedMenuIds
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'job_id' => 'required|exists:job,id',
            'menu_ids' => 'array',
            'menu_ids.*' => 'exists:menu,id'
        ]);

        $jobId = $request->job_id;
        $menuIds = $request->input('menu_ids', []);
        
        // 先刪除該職稱的所有選單配對
        JobMenu::where('job_id', $id)->delete();
        
        // 重新建立新的選單配對
        foreach ($menuIds as $menuId) {
            $jobMenu = new JobMenu();
            $jobMenu->job_id = $id;
            $jobMenu->menu_id = $menuId;
            $jobMenu->save();
        }

        return redirect()->route('job.menu.index')->with('success', '職稱選單配對更新成功');
    }
}

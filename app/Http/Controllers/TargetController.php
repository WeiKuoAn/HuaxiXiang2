<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\TargetItem;
use App\Models\TargetData;
use App\Models\TargetCategories;
use App\Models\Job;

class TargetController extends Controller
{
    public function index()
    {
        $datas = TargetData::get();
        // 收集所有 job_ids 並展平成單一陣列
        $jobIds = $datas->pluck('job_id')->flatten()->unique()->toArray();
        // 查詢所有對應的職稱名稱
        $jobs = Job::whereIn('id', $jobIds)->pluck('name', 'id');

        return view('target.index')->with('datas', $datas)->with('jobs', $jobs);
    }

    public function create()
    {
        $categories = TargetCategories::where('status', 'up')->get();
        $jobs = Job::whereNotIn('id', [4, 6, 7, 8, 9])->where('status', 'up')->get();
        return view('target.create')->with('categories', $categories)->with('jobs', $jobs);
    }



    public function store(Request $request)
    {
        // 驗證輸入數據
        $validated = $request->validate([
            'category_id' => 'required|exists:target_categories,id',
            'job_ids' => 'required|array',
            'job_ids.*' => 'exists:job,id',
            'frequency' => 'required|in:月,季,半年',
            'target_condition' => 'required|in:金額,數量,金額+數量',
            'target_amount' => 'nullable|numeric|min:0',
            'target_quantity' => 'nullable|integer|min:0',
        ]);

        // 解析請求數據
        $categoryId = $validated['category_id'];
        $jobIds = $validated['job_ids']; // 選擇的職稱
        $frequency = $validated['frequency'];
        $targetCondition = $validated['target_condition'];
        $targetAmount = $validated['target_amount'] ?? null;
        $targetQuantity = $validated['target_quantity'] ?? null;

        // 建立 TargetData 記錄
        $targetJob = TargetData::create([
            'category_id' => $categoryId,
            'job_id' => $jobIds, // 存成 JSON 格式
            'target_amount' => ($targetCondition !== '數量') ? $targetAmount : null, // 若為「數量」模式，則金額為 null
            'target_quantity' => ($targetCondition !== '金額') ? $targetQuantity : null, // 若為「金額」模式，則數量為 null
            'frequency' => $frequency,
            'target_condition' => $targetCondition,
        ]);

        // 生成對應的時間範圍
        $dateRanges = $this->generateDateRanges(date('Y'), $frequency);

        // 建立 TargetItem 記錄
        foreach ($dateRanges as $range) {
            TargetItem::create([
                'target_data_id' => $targetJob->id,
                'start_date' => $range['start'],
                'end_date' => $range['end'],
                'status' => '進行中',
                'manual_achieved' => 0,
            ]);
        }

        return redirect()->route('target')->with('success', '目標設定成功！');
    }



    public function show($id)
    {
        $categories = TargetCategories::where('status', 'up')->get();
        $data = TargetData::where('id', $id)->first();
        $jobs = Job::whereNotIn('id', [4, 6, 7, 8, 9])->where('status', 'up')->get();
        return view('target.edit')->with('data', $data)->with('categories', $categories)->with('jobs', $jobs);
    }


    public function update(Request $request, $id)
    {
        // 驗證輸入數據
        $validated = $request->validate([
            'category_id' => 'required|exists:target_categories,id',
            'job_ids' => 'required|array',
            'job_ids.*' => 'exists:job,id',
            'frequency' => 'required|in:月,季,半年',
            'target_condition' => 'required|in:金額,數量,金額+數量',
            'target_amount' => 'nullable|numeric|min:0',
            'target_quantity' => 'nullable|integer|min:0',
        ]);

        // 找到目標記錄
        $targetJob = TargetData::findOrFail($id);

        // 解析請求數據
        $categoryId = $validated['category_id'];
        $jobIds = $validated['job_ids'];
        $frequency = $validated['frequency'];
        $targetCondition = $validated['target_condition'];
        $targetAmount = $validated['target_amount'] ?? null;
        $targetQuantity = $validated['target_quantity'] ?? null;

        // 更新 TargetData 記錄
        $targetJob->update([
            'category_id' => $categoryId,
            'job_id' => $jobIds, // 存成 JSON 格式
            'target_amount' => ($targetCondition !== '數量') ? $targetAmount : null,
            'target_quantity' => ($targetCondition !== '金額') ? $targetQuantity : null,
            'frequency' => $frequency,
            'target_condition' => $targetCondition,
        ]);

        // 如果頻率改變，則重新生成時間範圍
        if ($targetJob->wasChanged('frequency')) {
            // 刪除舊的 TargetItem 記錄
            TargetItem::where('target_data_id', $targetJob->id)->delete();

            // 重新生成時間範圍
            $dateRanges = $this->generateDateRanges(date('Y'), $frequency);

            // 重新建立 TargetItem 記錄
            foreach ($dateRanges as $range) {
                TargetItem::create([
                    'target_data_id' => $targetJob->id,
                    'start_date' => $range['start'],
                    'end_date' => $range['end'],
                    'status' => '進行中',
                    'manual_achieved' => 0,
                ]);
            }
        }

        return redirect()->route('target')->with('success', '目標更新成功！');
    }


    public function delete($id)
    {
        $categories = TargetCategories::where('status', 'up')->get();
        $data = TargetData::where('id', $id)->first();
        $jobs = Job::whereNotIn('id', [4, 6, 7, 8, 9])->where('status', 'up')->get();
        return view('target.del')->with('data', $data)->with('categories', $categories)->with('jobs', $jobs);
    }

    public function destroy($id)
    {
        // 找到目標記錄
        $target = TargetData::findOrFail($id);
        // 刪除關聯的 TargetItem 記錄
        TargetItem::where('target_data_id', $target->id)->delete();

        // 刪除 TargetData 記錄
        $target->delete();

        return redirect()->route('target')->with('success', '目標刪除成功！');
    }


    protected function generateDateRanges($year, $frequency)
    {
        $ranges = [];

        if ($frequency === '月') {
            for ($month = 1; $month <= 12; $month++) {
                $start = Carbon::create($year, $month, 1);
                $end = $start->clone()->endOfMonth();
                $ranges[] = ['start' => $start, 'end' => $end];
            }
        } elseif ($frequency === '季') {
            // 修正季度結束月份，應該是 3、6、9、12
            $quarters = [[1, 3], [4, 6], [7, 9], [10, 12]];
            foreach ($quarters as $quarter) {
                $start = Carbon::create($year, $quarter[0], 1);
                $end = Carbon::create($year, $quarter[1], 1)->endOfMonth(); // 修正結束月份
                $ranges[] = ['start' => $start, 'end' => $end];
            }
        } elseif ($frequency === '半年') {
            // 半年只需要 6、12 月
            $ranges[] = ['start' => Carbon::create($year, 1, 1), 'end' => Carbon::create($year, 6, 30)];
            $ranges[] = ['start' => Carbon::create($year, 7, 1), 'end' => Carbon::create($year, 12, 31)];
        }

        return $ranges;
    }
}

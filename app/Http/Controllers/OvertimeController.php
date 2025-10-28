<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\OvertimeRecord;
use App\Models\User;

class OvertimeController extends Controller
{
    public function index(Request $request)
    {
        $query = OvertimeRecord::with(['user', 'creator'])
            ->orderBy('overtime_date', 'desc');

        // 日期篩選
        if ($request->filled('start_date')) {
            $query->where('overtime_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('overtime_date', '<=', $request->end_date);
        }

        // 狀態篩選
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 人員篩選
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $datas = $query->paginate(50);
        $users = User::where('status', '0')->orderby('level')->orderby('seq')->whereNotIn('job_id', [1,4,8,9,6,11])->get();
            
        return view('overtime.index', compact('datas', 'users'));
    }

    public function create()
    {
        $users = User::where('status', '0')->orderby('level')->orderby('seq')->whereNotIn('job_id', [1,4,8,9,6,11])->get();
        return view('overtime.create', compact('users'));
    }

    public function store(Request $request)
    {
        // 驗證輸入
        $request->validate([
            'overtime_date' => 'required|date',
            'overtime' => 'required|array',
            'overtime.*.user_id' => 'required|exists:users,id',
            'overtime.*.minutes' => 'required|integer|min:1',
            'overtime.*.reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->overtime as $overtimeData) {
                // 建立加班記錄
                $overtimeRecord = new OvertimeRecord([
                    'overtime_date' => $request->overtime_date,
                    'user_id' => $overtimeData['user_id'],
                    'minutes' => $overtimeData['minutes'],
                    'reason' => $overtimeData['reason'],
                    'created_by' => Auth::id(),
                    'status' => 'approved', // 自動設為已核准
                ]);

                // 計算加班費
                $overtimeRecord->calculateOvertimePay();
                $overtimeRecord->save();
            }

            DB::commit();

            return redirect()->route('overtime.index')->with('success', '加班記錄建立成功！');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', '建立失敗：' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $overtime = OvertimeRecord::with(['user'])->findOrFail($id);
        
        // 檢查是否可以編輯
        if (!$overtime->canEdit()) {
            return redirect()->route('overtime.index')->with('error', '此記錄無法編輯！');
        }

        $users = User::where('status', '0')->orderby('level')->orderby('seq')->whereNotIn('job_id', [1,4,8,9,6,11])->get();
        
        return view('overtime.edit', compact('overtime', 'users'));
    }

    public function update(Request $request, $id)
    {
        // 驗證輸入
        $request->validate([
            'overtime_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'minutes' => 'required|integer|min:1',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $overtime = OvertimeRecord::findOrFail($id);
            
            // 檢查是否可以編輯
            if (!$overtime->canEdit()) {
                return redirect()->route('overtime.index')->with('error', '此記錄無法編輯！');
            }

            // 更新資料
            $overtime->update([
                'overtime_date' => $request->overtime_date,
                'user_id' => $request->user_id,
                'minutes' => $request->minutes,
                'reason' => $request->reason,
            ]);

            // 重新計算加班費
            $overtime->calculateOvertimePay();
            $overtime->save();

            return redirect()->route('overtime.index')->with('success', '加班記錄更新成功！');

        } catch (\Exception $e) {
            return back()->with('error', '更新失敗：' . $e->getMessage())->withInput();
        }
    }
    
    public function delete($id)
    {
        $overtime = OvertimeRecord::with(['user', 'creator'])->findOrFail($id);
        
        // 檢查是否可以刪除
        if (!$overtime->canDelete()) {
            return redirect()->route('overtime.index')->with('error', '此記錄無法刪除！');
        }
        
        return view('overtime.delete', compact('overtime'));
    }

    public function destroy($id)
    {
        try {
            $overtime = OvertimeRecord::findOrFail($id);
            
            // 檢查是否可以刪除
            if (!$overtime->canDelete()) {
                return redirect()->route('overtime.index')->with('error', '此記錄無法刪除！');
            }
            
            $overtime->delete();
            
            return redirect()->route('overtime.index')->with('success', '加班記錄刪除成功！');
        } catch (\Exception $e) {
            return back()->with('error', '刪除失敗：' . $e->getMessage());
        }
    }

    /**
     * API 方法：直接更新加班記錄（用於加成頁面）
     */
    public function updateRecord(Request $request, $id)
    {
        try {
            // 驗證輸入
            $request->validate([
                'minutes' => 'required|integer|min:1',
                'reason' => 'required|string|max:500',
            ]);

            $overtime = OvertimeRecord::findOrFail($id);
            
            // 檢查是否可以編輯
            if (!$overtime->canEdit()) {
                return response()->json([
                    'success' => false,
                    'message' => '此記錄無法編輯'
                ], 403);
            }

            // 更新資料
            $overtime->minutes = $request->minutes;
            $overtime->reason = $request->reason;
            
            // 重新計算加班費相關欄位
            $overtime->calculateOvertimePay();
            $overtime->save();

            return response()->json([
                'success' => true,
                'message' => '加班記錄更新成功',
                'data' => [
                    'id' => $overtime->id,
                    'minutes' => $overtime->minutes,
                    'reason' => $overtime->reason,
                    'formatted_hours' => $overtime->formatted_hours,
                    'first_two_hours' => $overtime->first_two_hours,
                    'remaining_hours' => $overtime->remaining_hours,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '驗證失敗',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '更新失敗：' . $e->getMessage()
            ], 500);
        }
    }





    /**
     * API 方法：手動新增加班記錄（用於加成頁面）
     */
    public function createRecord(Request $request)
    {
        try {
            // 驗證輸入
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'overtime_date' => 'required|date',
                'minutes' => 'required|integer|min:1',
                'reason' => 'required|string|max:500',
            ]);

            // 建立新的加班記錄
            $overtimeRecord = new OvertimeRecord([
                'overtime_date' => $request->overtime_date,
                'user_id' => $request->user_id,
                'minutes' => $request->minutes,
                'reason' => $request->reason,
                'created_by' => Auth::id(),
                'status' => 'approved', // 自動設為已核准
            ]);

            // 計算加班費相關欄位
            $overtimeRecord->calculateOvertimePay();
            $overtimeRecord->save();

            return response()->json([
                'success' => true,
                'message' => '加班記錄建立成功',
                'data' => [
                    'id' => $overtimeRecord->id,
                    'user_id' => $overtimeRecord->user_id,
                    'user_name' => $overtimeRecord->user->name ?? '未知人員',
                    'minutes' => $overtimeRecord->minutes,
                    'formatted_hours' => $overtimeRecord->formatted_hours,
                    'first_two_hours' => $overtimeRecord->first_two_hours,
                    'remaining_hours' => $overtimeRecord->remaining_hours,
                    'reason' => $overtimeRecord->reason,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '驗證失敗',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '建立失敗：' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        // 取得查詢參數
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status');
        $userId = $request->get('user_id');

        // 查詢資料
        $query = OvertimeRecord::with(['user', 'creator'])
            ->orderBy('overtime_date', 'asc');

        if ($startDate) {
            $query->where('overtime_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('overtime_date', '<=', $endDate);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $overtimes = $query->get();

        // 準備 Excel 資料
        $excelData = [];
        $monthlyStats = [];
        $dailyStats = [];

        foreach ($overtimes as $overtime) {
            $date = $overtime->overtime_date->format('Y-m-d');
            $month = $overtime->overtime_date->format('Y-m');
            $personName = $overtime->user->name ?? '未指定';
            
            // 月度統計
            if (!isset($monthlyStats[$month])) {
                $monthlyStats[$month] = [];
            }
            if (!isset($monthlyStats[$month][$personName])) {
                $monthlyStats[$month][$personName] = [
                    'count' => 0,
                    'total_minutes' => 0,
                    'total_pay' => 0
                ];
            }
            
            $monthlyStats[$month][$personName]['count']++;
            $monthlyStats[$month][$personName]['total_minutes'] += $overtime->minutes;
            $monthlyStats[$month][$personName]['total_pay'] += $overtime->overtime_pay;

            // 日統計
            if (!isset($dailyStats[$date])) {
                $dailyStats[$date] = [];
            }
            if (!isset($dailyStats[$date][$personName])) {
                $dailyStats[$date][$personName] = [
                    'count' => 0,
                    'total_minutes' => 0,
                    'total_pay' => 0
                ];
            }
            
            $dailyStats[$date][$personName]['count']++;
            $dailyStats[$date][$personName]['total_minutes'] += $overtime->minutes;
            $dailyStats[$date][$personName]['total_pay'] += $overtime->overtime_pay;

            // 準備詳細資料
            $excelData[] = [
                'date' => $date,
                'person' => $personName,
                'minutes' => $overtime->minutes,
                'formatted_hours' => $overtime->formatted_hours,
                'reason' => $overtime->reason,
                'overtime_pay' => $overtime->overtime_pay,
                'status' => $overtime->status_name,
                'creator' => $overtime->creator->name ?? '未知'
            ];
        }

        // 生成 CSV 檔案
        $filename = '加班統計_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($excelData, $monthlyStats, $dailyStats) {
            $file = fopen('php://output', 'w');
            
            // 寫入 BOM 以支援中文
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // 寫入標題
            fputcsv($file, ['加班日期', '人員', '加班分鐘', '加班時數', '事由', '加班費', '狀態', '建立者']);
            
            // 寫入詳細資料
            foreach ($excelData as $row) {
                fputcsv($file, [
                    $row['date'],
                    $row['person'],
                    $row['minutes'],
                    $row['formatted_hours'],
                    $row['reason'],
                    $row['overtime_pay'],
                    $row['status'],
                    $row['creator']
                ]);
            }
            
            // 寫入空行
            fputcsv($file, []);
            
            // 寫入日統計
            fputcsv($file, ['日統計']);
            fputcsv($file, ['日期', '人員', '記錄次數', '總分鐘', '總加班費']);
            
            foreach ($dailyStats as $date => $persons) {
                foreach ($persons as $person => $stats) {
                    fputcsv($file, [
                        $date,
                        $person,
                        $stats['count'],
                        $stats['total_minutes'],
                        $stats['total_pay']
                    ]);
                }
            }
            
            // 寫入空行
            fputcsv($file, []);
            
            // 寫入月度統計
            fputcsv($file, ['月度統計']);
            fputcsv($file, ['月份', '人員', '記錄次數', '總分鐘', '總加班費']);
            
            foreach ($monthlyStats as $month => $persons) {
                foreach ($persons as $person => $stats) {
                    fputcsv($file, [
                        $month,
                        $person,
                        $stats['count'],
                        $stats['total_minutes'],
                        $stats['total_pay']
                    ]);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

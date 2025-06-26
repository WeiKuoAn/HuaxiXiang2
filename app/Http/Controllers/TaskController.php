<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        // 取得所有任務
        $title = $request->input('title');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $status = $request->input('status');

        // 如果有 status 篩選
        if ($status !== null) {
            $datas = Task::where('status', $status);
        } else {
            $datas = Task::query(); // 如果沒有 status 篩選，就查詢所有任務
        }

        // 如果有 title 篩選
        if ($title) {
            $datas = $datas->where('title', 'like', '%' . $title . '%');
        }

        // 如果有日期篩選
        if ($start_date && $end_date) {
            $datas = $datas->whereBetween('start_date', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
        } elseif ($start_date) {
            $datas = $datas->where('start_date', '>=', $start_date . ' 00:00:00');
        } elseif ($end_date) {
            $datas = $datas->where('end_date', '<=', $end_date . ' 23:59:59');
        }

        $datas = $datas->paginate(50);

        return view('task.index')->with('datas', $datas);
    }

    public function create()
    {
        $now = Carbon::now()->format('Y-m-d');
        $users = User::all();
        return view('task.create')->with('users', $users)->with('now', $now);
    }

    public function store(Request $request)
    {
        // 驗證請求資料
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'required|date',          // Y-m-d
            'start_time'  => 'required|date_format:H:i', // H:i
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'end_time'    => 'nullable|date_format:H:i',
            'status'      => 'required|in:0,1',
            'note'        => 'nullable|string',
        ]);


        // 把日期+時間 合併並 parse 成 Carbon
        $start = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['start_date'] . ' ' . $validated['start_time']
        );

        $end = null;
        if (!empty($validated['end_date']) && !empty($validated['end_time'])) {
            $end = Carbon::createFromFormat(
                'Y-m-d H:i',
                $validated['end_date'] . ' ' . $validated['end_time']
            );
        }

        // 先準備共用欄位
        $data = [
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date'  => $start,
            'end_date'    => $end,
            'status'      => $validated['status'],
            'note'        => $validated['note'] ?? null,
            'created_by'  => Auth::id(),
        ];

        // 如果一進來就是已完成，就同時記錄關閉者
        if ($validated['status'] == 1) {
            $data['close_by'] = Auth::id();
        }

        // 儲存（假設你用 Todo 模型）
        Task::create($data);

        return redirect()->route('task')->with('success', '任務已成功建立！');
    }

    public function ajax_store(Request $request)
    {
        $v = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'required|date',
            'start_time'  => 'required|date_format:H:i',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'end_time'    => 'nullable|date_format:H:i',
            'status'      => 'required|in:0,1',
        ]);

        $start = Carbon::createFromFormat(
            'Y-m-d H:i',
            $v['start_date'] . ' ' . $v['start_time']
        );

        $end = null;
        if (!empty($v['end_date']) && !empty($v['end_time'])) {
            $end = Carbon::createFromFormat(
                'Y-m-d H:i',
                $v['end_date'] . ' ' . $v['end_time']
            );
        }

        // 1. 先建立最基本的欄位
        $task = Task::create([
            'title'       => $v['title'],
            'description' => $v['description'],
            'start_date'  => $start,
            'end_date'    => $end,
            'status'      => $v['status'],
            'created_by'  => Auth::id(),
        ]);

        // 2. 如果一進來就是已完成，就再單獨更新 close_by
        if ($v['status'] == 1) {
            $task->close_by = Auth::id();
            $task->save();
        }

        // 順便帶 user 名稱
        $task->load('created_users');

        // 回傳新建立的 Task
        return response()->json([
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'start_date' => optional($task->start_date)->timezone('Asia/Taipei')->format('Y-m-d H:i:s'),
                'end_date' => optional($task->end_date)->timezone('Asia/Taipei')->format('Y-m-d H:i:s'),
                'status' => $task->status,
                'note' => $task->note,
                'created_by_name' => $task->created_users ? $task->created_users->name : '',
            ]
        ]);
    }

    public function check(Request $request)
    {
        $request->validate(['id' => 'required|exists:tasks,id']);

        $task = Task::findOrFail($request->id);
        $task->status   = '1';
        $task->close_by = auth()->id();
        $ok = $task->save();              // <-- 確認回傳值

        return response()->json(['success' => (bool)$ok]);
    }

    public function show($id)
    {
        $data = Task::findOrFail($id);
        $now = Carbon::now()->format('Y-m-d');
        $users = User::where('status', '0')->get();
        return view('task.edit')->with('users', $users)->with('now', $now)->with('data', $data);
    }

    public function update($id, Request $request)
    {

        $task = Task::findOrFail($id);
        $task->title       = $request->title;
        $task->description = $request->description ?? null;
        $task->start_date = Carbon::parse($request->start_date . ' ' . $request->start_time);
        $task->end_date   = Carbon::parse($request->end_date   . ' ' . $request->end_time);

        $task->status      = $request->status;
        $task->note        = $request->note ?? null;
        if (isset($request->close_by)) {
            $task->close_by = $request->close_by;
        } else {
            $task->close_by = null; // 如果沒有關閉者，則設為 null
        }
        $task->save();

        return redirect()->route('task')->with('success', '任務已成功更新！');
    }

    public function delete($id)
    {
        $data = Task::findOrFail($id);
        $now = Carbon::now()->format('Y-m-d');
        $users = User::where('status', '0')->get();
        return view('task.del')->with('users', $users)->with('now', $now)->with('data', $data);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()->route('task')->with('success', '任務已成功刪除！');
    }
}

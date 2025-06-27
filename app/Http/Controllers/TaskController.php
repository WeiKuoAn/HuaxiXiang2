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
        $assigned_to = $request->input('assigned_to');

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

        // 如果有 assigned_to 篩選
        if ($assigned_to) {
            $datas = $datas->where('assigned_to', $assigned_to);
        }

        // 如果有日期篩選
        if ($start_date && $end_date) {
            $datas = $datas->whereBetween('start_date', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
        } elseif ($start_date) {
            $datas = $datas->where('start_date', '>=', $start_date . ' 00:00:00');
        } elseif ($end_date) {
            $datas = $datas->where('end_date', '<=', $end_date . ' 23:59:59');
        }

        $datas = $datas->with('created_users', 'close_users', 'assigned_users')->paginate(50);
        $users = User::where('status','0')->get();

        return view('task.index')->with('datas', $datas)->with('users', $users);
    }

    public function create()
    {
        $now = Carbon::now()->format('Y-m-d');
        $users = User::where('status','0')->get();
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
            'end_time'    => 'nullable|date_format:H:i', // 移除 status 驗證
            'assigned_to' => 'nullable|exists:users,id',
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
            'status'      => 0, // 新增任務預設為待辦 (0)
            'created_by'  => Auth::id(),
            'assigned_to' => $validated['assigned_to'] ?? null,
        ];

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
            // 'status'      => 'required|in:0,1', // 移除 status 驗證
            'assigned_to' => 'nullable|exists:users,id',
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
            'description' => $v['description'] ?? null,
            'start_date'  => $start,
            'end_date'    => $end,
            'status'      => 0, // 新增任務預設為待辦 (0)
            'created_by'  => Auth::id(),
            'assigned_to' => $v['assigned_to'] ?? null,
        ]);

        // 順便帶 user 名稱
        $task->load('created_users', 'assigned_users');

        // 回傳新建立的 Task
        return response()->json([
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'start_date' => optional($task->start_date)->timezone('Asia/Taipei')->format('Y-m-d H:i:s'),
                'end_date' => optional($task->end_date)->timezone('Asia/Taipei')->format('Y-m-d H:i:s'),
                'status' => $task->status,
                'created_by_name' => $task->created_users ? $task->created_users->name : '',
                'assigned_to_name' => $task->assigned_users ? $task->assigned_users->name : '',
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

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            // start_time is not submitted from the edit form, so its validation is removed.
            'end_date'    => 'nullable|date|after_or_equal:'.$task->start_date->format('Y-m-d'),
            'end_time'    => 'nullable|date_format:H:i',
            'status'      => 'required|boolean',
            'assigned_to' => 'nullable|exists:users,id',
            'close_by'    => 'nullable|exists:users,id',
        ]);

        $task->title       = $request->title;
        $task->description = $request->description ?? null;
        $task->end_date   = ($request->end_date && $request->end_time) ? Carbon::parse($request->end_date . ' ' . $request->end_time) : null;
        $task->assigned_to = $request->assigned_to;
        $task->status      = $request->status;

        // If status is 'completed' (1), set the closer.
        // If status is 'pending' (0), clear the closer.
        if ($request->status == 1) {
            // If a closer is not specified, set the current user as the closer.
            $task->close_by = $request->close_by ?? Auth::id();
        } else {
            $task->close_by = null;
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

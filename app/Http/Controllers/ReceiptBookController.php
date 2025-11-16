<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReceiptBook;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class ReceiptBookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ReceiptBook::with(['holder', 'creator']);

        // 筛选：保管人
        if ($request->filled('holder_id')) {
            $query->where('holder_id', $request->holder_id);
        }

        // 筛选：状态（避免命名冲突，使用 book_status）
        if ($request->filled('book_status')) {
            $query->where('status', $request->book_status);
        }

        // 單號搜尋：輸入任一數字，自動換算到 50 張為一冊的區間
        $computedRange = null;
        if ($request->filled('number')) {
            $num = (int) $request->number;
            if ($num > 0) {
                $start = (int) (floor(($num - 1) / 50) * 50 + 1);
                $end = $start + 49;
                $computedRange = [$start, $end];
                $query->where('start_number', $start)->where('end_number', $end);
            }
        }

        // 筛选：日期范围
        if ($request->filled('date_from')) {
            $query->where('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('issue_date', '<=', $request->date_to);
        }

        $receiptBooks = $query->orderBy('start_number', 'desc')
            ->paginate(10)
            ->withQueryString();

        // 为每个收據添加统计信息
        foreach ($receiptBooks as $book) {
            $book->statistics = $book->getStatistics();
        }

        $users = User::orderBy('name')->get();

        return view('receipt_books.index', compact('receiptBooks', 'users', 'computedRange', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('receipt_books.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'start_number' => 'required|integer|min:1',
            'end_number' => 'required|integer|gt:start_number',
            'holder_id' => 'nullable|exists:users,id',
            'issue_date' => 'nullable|date',
            'note' => 'nullable|string',
        ], [
            'start_number.required' => '请输入起始号码',
            'start_number.integer' => '起始号码必须是整数',
            'start_number.min' => '起始号码必须大于0',
            'end_number.required' => '请输入结束号码',
            'end_number.integer' => '结束号码必须是整数',
            'end_number.gt' => '结束号码必须大于起始号码',
            'holder_id.exists' => '选择的保管人不存在',
            'issue_date.required' => '请选择发放日期',
            'issue_date.date' => '发放日期格式不正确',
        ]);

        // 检查号码范围是否重叠
        $overlap = ReceiptBook::where(function ($query) use ($request) {
            $query->whereBetween('start_number', [$request->start_number, $request->end_number])
                ->orWhereBetween('end_number', [$request->start_number, $request->end_number])
                ->orWhere(function ($q) use ($request) {
                    $q->where('start_number', '<=', $request->start_number)
                      ->where('end_number', '>=', $request->end_number);
                });
        })->exists();

        if ($overlap) {
            return back()->withInput()->withErrors(['start_number' => '该号码范围与现有收據重叠，请重新输入']);
        }

        ReceiptBook::create([
            'start_number' => $request->start_number,
            'end_number' => $request->end_number,
            'holder_id' => $request->holder_id,
            'issue_date' => $request->issue_date,
            'note' => $request->note,
            'created_by' => Auth::id(),
            // 若未指定保管人，狀態設為未使用；有保管人則為使用中
            'status' => $request->holder_id ? 'active' : 'unused',
        ]);

        return redirect()->route('receipt-books.index')
            ->with('success', '收據创建成功！');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $receiptBook = ReceiptBook::with(['holder', 'creator'])->findOrFail($id);
        
        // 获取统计信息
        $statistics = $receiptBook->getStatistics();
        
        // 获取单号详情
        $numberDetails = $receiptBook->getNumberDetails();
        
        // 获取缺少的单号
        $missingNumbers = $receiptBook->getMissingNumbers();

        return view('receipt_books.show', compact('receiptBook', 'statistics', 'numberDetails', 'missingNumbers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $receiptBook = ReceiptBook::findOrFail($id);
        $users = User::orderBy('name')->get();
        
        return view('receipt_books.edit', compact('receiptBook', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $receiptBook = ReceiptBook::findOrFail($id);

        $request->validate([
            'holder_id' => 'required|exists:users,id',
            'issue_date' => 'nullable|date',
            'returned_at' => 'nullable|date|after_or_equal:issue_date',
            'status' => 'required|in:unused,active,returned,cancelled',
            'note' => 'nullable|string',
        ], [
            'holder_id.required' => '请选择保管人',
            'holder_id.exists' => '选择的保管人不存在',
            'issue_date.date' => '发放日期格式不正确',
            'returned_at.date' => '缴回日期格式不正确',
            'returned_at.after_or_equal' => '缴回日期不能早于发放日期',
            'status.required' => '请选择状态',
            'status.in' => '状态值不正确',
        ]);

        // 若有繳回日期，狀態自動改為「已繳回」
        $status = $request->status;
        if ($request->filled('returned_at')) {
            $status = 'returned';
        } elseif ($request->filled('issue_date')) {
            // 若更新了發放日期（有值），狀態改為「使用中」
            $status = 'active';
        }

        $receiptBook->update([
            'holder_id' => $request->holder_id,
            'issue_date' => $request->issue_date,
            'returned_at' => $request->returned_at,
            'status' => $status,
            'note' => $request->note,
        ]);

        return redirect()->route('receipt-books.show', $receiptBook->id)
            ->with('success', '收據更新成功！');
    }

    /**
     * 可認領清單
     */
    public function claimable(Request $request)
    {
        // 僅顯示「未使用」的可認領清單
        $receiptBooks = ReceiptBook::whereNull('holder_id')
            ->where('status', 'unused')
            ->orderBy('start_number', 'desc')
            ->orderBy('issue_date', 'desc')
            ->paginate(20);
        return view('receipt_books.claim', compact('receiptBooks'));
    }

    /**
     * 認領單本
     */
    public function claim(Request $request, $id)
    {
        $receiptBook = ReceiptBook::findOrFail($id);
        if (!is_null($receiptBook->holder_id)) {
            return redirect()->back()->with('error', '此單本已被認領。');
        }
        $receiptBook->holder_id = Auth::id();
        // 認領後狀態改為「使用中」
        $receiptBook->status = 'active';
        $receiptBook->save();
        return redirect()->back()->with('success', '認領成功！');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $receiptBook = ReceiptBook::findOrFail($id);
        $receiptBook->delete();

        return redirect()->route('receipt-books.index')
            ->with('success', '收據删除成功！');
    }

    /**
     * 标记为已缴回
     */
    public function markAsReturned($id)
    {
        $receiptBook = ReceiptBook::findOrFail($id);
        
        $receiptBook->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        return redirect()->back()->with('success', '已标记为缴回！');
    }
}

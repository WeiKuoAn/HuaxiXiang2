<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class FlexibleRpgAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $accessLevel = 'restricted'): Response
    {
        // 檢查用戶是否已登入
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 檢查用戶是否啟用
        if (Auth::user()->status != 0) {
            return redirect()->route('login')->with('error', '您的帳號已被停用');
        }

        // 根據訪問級別設定不同的權限
        switch ($accessLevel) {
            case 'public':
                // 公開權限：所有登入用戶都可以訪問
                $allowedJobIds = [1, 2, 3, 4, 5, 6, 7, 9, 10, 11];
                break;

            case 'staff':
                // 員工權限：專員以上可以訪問
                $allowedJobIds = [1, 2, 3, 4, 5, 6, 7, 9, 10];
                break;

            case 'management':
                // 管理權限：主管以上可以訪問
                $allowedJobIds = [1, 2, 3, 7, 9, 10];
                break;

            case 'restricted':
            default:
                // 限制權限：只有特定職位可以訪問
                $allowedJobIds = [1, 2, 6, 7];
                break;
        }

        // 檢查用戶的 level（超級管理員和管理員都有權限）
        $hasLevelAccess = Auth::user()->level == 0 || Auth::user()->level == 1;

        // 檢查用戶的 job_id
        $hasJobAccess = in_array(Auth::user()->job_id, $allowedJobIds);

        // 如果沒有權限，直接返回 404
        if (!$hasLevelAccess && !$hasJobAccess) {
            abort(404);
        }

        return $next($request);
    }
}

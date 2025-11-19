<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Sale_prom;
use App\Models\Prom;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SaleSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Rpg37Controller extends Controller
{
    public function rpg37(Request $request)
    {
        // 日期範圍處理
        $firstDay = $request->after_date 
            ? Carbon::parse($request->after_date)->startOfDay()
            : Carbon::now()->startOfMonth();
        
        $lastDay = $request->before_date 
            ? Carbon::parse($request->before_date)->endOfDay()
            : Carbon::now()->endOfMonth();

        // 上個月同期（用於計算成長率）
        $lastMonthFirst = $firstDay->copy()->subMonth()->startOfMonth();
        $lastMonthLast = $firstDay->copy()->subMonth()->endOfMonth();

        // ========== 1. 趨勢數據：依月份分組 ==========
        $trendData = $this->getTrendData($firstDay, $lastDay);

        // ========== 2. 來源分析 ==========
        $sourceAnalysis = $this->getSourceAnalysis($firstDay, $lastDay);

        // ========== 3. 產品列表數據 ==========
        $productList = $this->getProductList($firstDay, $lastDay);

        // ========== 4. KPI 卡片數據 ==========
        $kpiData = $this->getKpiData($firstDay, $lastDay, $lastMonthFirst, $lastMonthLast);

        // ========== 5. 衝量 vs 衝漂亮數據 ==========
        $volumeVsProfit = $this->getVolumeVsProfit($firstDay, $lastDay);

        return view('rpg37.index', [
            'request' => $request,
            'firstDay' => $firstDay,
            'lastDay' => $lastDay,
            'trendData' => $trendData,
            'sourceAnalysis' => $sourceAnalysis,
            'productList' => $productList,
            'kpiData' => $kpiData,
            'volumeVsProfit' => $volumeVsProfit,
        ]);
    }

    /**
     * 趨勢數據：依月份分組
     */
    private function getTrendData($firstDay, $lastDay)
    {
        $data = Sale_prom::join('sale_data', 'sale_data.id', '=', 'sale_prom.sale_id')
            ->join('prom', 'prom.id', '=', 'sale_prom.prom_id')
            ->leftJoin('product', function($join) {
                $join->on('product.prom_id', '=', 'sale_prom.prom_id')
                     ->where('product.status', 'up');
            })
            ->where('sale_data.sale_date', '>=', $firstDay)
            ->where('sale_data.sale_date', '<=', $lastDay)
            ->where('sale_data.status', '9') // 已對帳
            ->whereNotNull('sale_data.type')
            ->where('sale_data.type', '!=', '')
            ->select(
                DB::raw('DATE_FORMAT(sale_data.sale_date, "%Y-%m") as month'),
                DB::raw('COUNT(DISTINCT sale_prom.id) as volume'),
                DB::raw('SUM(sale_prom.prom_total) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return $data;
    }

    /**
     * 來源分析：比較不同來源（從 sale_data.type 和 sale_source 表）
     */
    private function getSourceAnalysis($firstDay, $lastDay)
    {
        $data = Sale_prom::join('sale_data', 'sale_data.id', '=', 'sale_prom.sale_id')
            ->join('prom', 'prom.id', '=', 'sale_prom.prom_id')
            ->leftJoin('sale_source', function($join) {
                $join->on(DB::raw('sale_source.code COLLATE utf8mb4_unicode_ci'), '=', DB::raw('sale_data.type COLLATE utf8mb4_unicode_ci'));
            })
            ->leftJoin('product', function($join) {
                $join->on('product.prom_id', '=', 'sale_prom.prom_id')
                     ->where('product.status', 'up');
            })
            ->where('sale_data.sale_date', '>=', $firstDay)
            ->where('sale_data.sale_date', '<=', $lastDay)
            ->where('sale_data.status', '9')
            ->whereNotNull('sale_data.type')
            ->where('sale_data.type', '!=', '')
            ->select(
                DB::raw('IFNULL(sale_source.name, "未知來源") COLLATE utf8mb4_unicode_ci as source'),
                'sale_source.code as source_code',
                DB::raw('COUNT(DISTINCT sale_prom.id) as volume'),
                DB::raw('SUM(sale_prom.prom_total) as revenue'),
                DB::raw('SUM(sale_prom.prom_total - COALESCE(product.cost, 0)) as profit')
            )
            ->groupBy(DB::raw('IFNULL(sale_source.name, "未知來源") COLLATE utf8mb4_unicode_ci'), 'sale_source.code')
            ->get();

        return $data;
    }

    /**
     * 產品列表數據：包含總銷量、總營收、總利潤、平均毛利率（統一產品，不依來源細分）
     */
    private function getProductList($firstDay, $lastDay)
    {
        // 取得所有後續處理項目的銷售數據
        $saleProms = Sale_prom::join('sale_data', 'sale_data.id', '=', 'sale_prom.sale_id')
            ->join('prom', 'prom.id', '=', 'sale_prom.prom_id')
            ->leftJoin('sale_source', function($join) {
                $join->on(DB::raw('sale_source.code COLLATE utf8mb4_unicode_ci'), '=', DB::raw('sale_data.type COLLATE utf8mb4_unicode_ci'));
            })
            ->where('sale_data.sale_date', '>=', $firstDay)
            ->where('sale_data.sale_date', '<=', $lastDay)
            ->where('sale_data.status', '9')
            ->whereNotNull('sale_data.type')
            ->where('sale_data.type', '!=', '')
            ->select(
                'sale_prom.id',
                'sale_prom.prom_id',
                'sale_prom.prom_total',
                'prom.name as product_name'
            )
            ->get();

        // 取得每個 prom 對應的產品成本（從 product 表）
        $promIds = $saleProms->pluck('prom_id')->unique();
        $products = Product::whereIn('prom_id', $promIds)
            ->where('status', 'up')
            ->select('prom_id', 'cost')
            ->get()
            ->groupBy('prom_id');

        // 只按產品分組統計（不依來源細分）
        $grouped = $saleProms->groupBy('prom_id')->map(function($items, $promId) use ($products) {
            $firstItem = $items->first();
            $totalVolume = $items->count();
            $totalRevenue = $items->sum('prom_total');
            $avgPrice = $items->avg('prom_total');
            
            // 取得該產品的平均成本
            $promProducts = $products->get($promId);
            $avgCost = $promProducts ? $promProducts->avg('cost') : 0;
            
            // 計算成本和利潤
            $totalCost = $avgCost * $totalVolume;
            $totalProfit = $totalRevenue - $totalCost;
            $marginRate = $totalRevenue > 0 ? ($totalProfit / $totalRevenue * 100) : 0;
            
            // 來源顯示為「混合」或主要來源（取最常見的來源）
            $source = '混合';
            
            return [
                'prom_id' => $promId,
                'product_name' => $firstItem->product_name,
                'source' => $source,
                'total_volume' => $totalVolume,
                'total_revenue' => $totalRevenue,
                'avg_price' => $avgPrice,
                'total_profit' => $totalProfit,
                'margin_rate' => round($marginRate, 2),
                'cost' => $avgCost,
            ];
        })->values();

        return $grouped;
    }

    /**
     * KPI 卡片數據
     */
    private function getKpiData($firstDay, $lastDay, $lastMonthFirst, $lastMonthLast)
    {
        // 本月數據
        $currentProms = Sale_prom::join('sale_data', 'sale_data.id', '=', 'sale_prom.sale_id')
            ->where('sale_data.sale_date', '>=', $firstDay)
            ->where('sale_data.sale_date', '<=', $lastDay)
            ->where('sale_data.status', '9')
            ->whereNotNull('sale_data.type')
            ->where('sale_data.type', '!=', '')
            ->get();

        $currentRevenue = $currentProms->sum('prom_total');
        
        // 計算成本（從 product 表取得）
        $promIds = $currentProms->pluck('prom_id')->unique();
        $products = Product::whereIn('prom_id', $promIds)
            ->where('status', 'up')
            ->select('prom_id', 'cost')
            ->get()
            ->groupBy('prom_id');
        
        $currentCost = 0;
        foreach ($currentProms->groupBy('prom_id') as $promId => $items) {
            $promProducts = $products->get($promId);
            $avgCost = $promProducts ? $promProducts->avg('cost') : 0;
            $currentCost += $avgCost * $items->count();
        }
        
        $currentProfit = $currentRevenue - $currentCost;

        // 上個月數據
        $lastMonthProms = Sale_prom::join('sale_data', 'sale_data.id', '=', 'sale_prom.sale_id')
            ->where('sale_data.sale_date', '>=', $lastMonthFirst)
            ->where('sale_data.sale_date', '<=', $lastMonthLast)
            ->where('sale_data.status', '9')
            ->whereNotNull('sale_data.type')
            ->where('sale_data.type', '!=', '')
            ->get();

        $lastMonthRevenue = $lastMonthProms->sum('prom_total');

        // 計算成長率
        $growthRate = $lastMonthRevenue > 0 
            ? (($currentRevenue - $lastMonthRevenue) / $lastMonthRevenue * 100)
            : 0;

        return [
            'total_revenue' => $currentRevenue,
            'total_profit' => $currentProfit,
            'growth_rate' => round($growthRate, 2),
        ];
    }

    /**
     * 衝量 vs 衝漂亮數據
     */
    private function getVolumeVsProfit($firstDay, $lastDay)
    {
        $saleProms = Sale_prom::join('sale_data', 'sale_data.id', '=', 'sale_prom.sale_id')
            ->join('prom', 'prom.id', '=', 'sale_prom.prom_id')
            ->where('sale_data.sale_date', '>=', $firstDay)
            ->where('sale_data.sale_date', '<=', $lastDay)
            ->where('sale_data.status', '9')
            ->whereNotNull('sale_data.type')
            ->where('sale_data.type', '!=', '')
            ->select(
                'sale_prom.prom_id',
                'sale_prom.prom_total',
                'prom.name as product_name'
            )
            ->get();

        // 取得產品成本
        $promIds = $saleProms->pluck('prom_id')->unique();
        $products = Product::whereIn('prom_id', $promIds)
            ->where('status', 'up')
            ->select('prom_id', 'cost')
            ->get()
            ->groupBy('prom_id');

        // 按產品分組統計
        $data = $saleProms->groupBy('prom_id')->map(function($items, $promId) use ($products) {
            $firstItem = $items->first();
            $volume = $items->count();
            $totalRevenue = $items->sum('prom_total');
            
            $promProducts = $products->get($promId);
            $avgCost = $promProducts ? $promProducts->avg('cost') : 0;
            $totalCost = $avgCost * $volume;
            $profit = $totalRevenue - $totalCost;
            $marginRate = $totalRevenue > 0 ? ($profit / $totalRevenue * 100) : 0;
            
            return [
                'prom_id' => $promId,
                'product_name' => $firstItem->product_name,
                'volume' => $volume,
                'profit' => $profit,
                'margin_rate' => round($marginRate, 2),
            ];
        })->values();

        return $data;
    }

    /**
     * 顯示單個後續處理的詳細頁面（今年每月比較）
     */
    public function detail(Request $request, $promId)
    {
        $prom = Prom::findOrFail($promId);
        
        // 取得今年的日期範圍
        $currentYear = $request->year ?? date('Y');
        $firstDay = Carbon::createFromDate($currentYear, 1, 1)->startOfDay();
        $lastDay = Carbon::createFromDate($currentYear, 12, 31)->endOfDay();

        // 取得該產品今年每個月的數據
        $monthlyData = $this->getMonthlyData($promId, $firstDay, $lastDay);

        // 取得該產品今年每個月的來源分析
        $monthlySourceData = $this->getMonthlySourceData($promId, $firstDay, $lastDay);

        // 計算總計
        $totals = [
            'total_volume' => $monthlyData->sum('volume'),
            'total_revenue' => $monthlyData->sum('revenue'),
            'total_profit' => $monthlyData->sum('profit'),
        ];

        // 取得可選年份列表
        $years = Sale_prom::join('sale_data', 'sale_data.id', '=', 'sale_prom.sale_id')
            ->where('sale_prom.prom_id', $promId)
            ->whereNotNull('sale_data.type')
            ->where('sale_data.type', '!=', '')
            ->select(DB::raw('YEAR(sale_data.sale_date) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('rpg37.detail', [
            'prom' => $prom,
            'currentYear' => $currentYear,
            'years' => $years,
            'monthlyData' => $monthlyData,
            'monthlySourceData' => $monthlySourceData,
            'totals' => $totals,
        ]);
    }

    /**
     * 取得單個產品今年每個月的數據
     */
    private function getMonthlyData($promId, $firstDay, $lastDay)
    {
        $data = Sale_prom::join('sale_data', 'sale_data.id', '=', 'sale_prom.sale_id')
            ->leftJoin('product', function($join) {
                $join->on('product.prom_id', '=', 'sale_prom.prom_id')
                     ->where('product.status', 'up');
            })
            ->where('sale_prom.prom_id', $promId)
            ->where('sale_data.sale_date', '>=', $firstDay)
            ->where('sale_data.sale_date', '<=', $lastDay)
            ->where('sale_data.status', '9')
            ->whereNotNull('sale_data.type')
            ->where('sale_data.type', '!=', '')
            ->select(
                DB::raw('DATE_FORMAT(sale_data.sale_date, "%Y-%m") as month'),
                DB::raw('DATE_FORMAT(sale_data.sale_date, "%m") as month_num'),
                DB::raw('COUNT(DISTINCT sale_prom.id) as volume'),
                DB::raw('SUM(sale_prom.prom_total) as revenue'),
                DB::raw('SUM(sale_prom.prom_total - COALESCE(product.cost, 0)) as profit'),
                DB::raw('AVG(sale_prom.prom_total) as avg_price')
            )
            ->groupBy('month', 'month_num')
            ->orderBy('month')
            ->get()
            ->map(function($item) {
                return [
                    'month' => $item->month,
                    'month_num' => (int)$item->month_num,
                    'volume' => $item->volume,
                    'revenue' => $item->revenue,
                    'profit' => $item->profit,
                    'avg_price' => $item->avg_price ?? 0,
                ];
            });

        // 確保所有月份都有數據（補齊缺失的月份）
        $year = (int)$firstDay->format('Y');
        $allMonths = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthKey = sprintf('%04d-%02d', $year, $i);
            $existing = $data->firstWhere('month', $monthKey);
            $allMonths[] = $existing ?? [
                'month' => $monthKey,
                'month_num' => $i,
                'volume' => 0,
                'revenue' => 0,
                'profit' => 0,
                'avg_price' => 0,
            ];
        }

        return collect($allMonths);
    }

    /**
     * 取得單個產品今年每個月的來源分析
     */
    private function getMonthlySourceData($promId, $firstDay, $lastDay)
    {
        $data = Sale_prom::join('sale_data', 'sale_data.id', '=', 'sale_prom.sale_id')
            ->leftJoin('sale_source', function($join) {
                $join->on(DB::raw('sale_source.code COLLATE utf8mb4_unicode_ci'), '=', DB::raw('sale_data.type COLLATE utf8mb4_unicode_ci'));
            })
            ->where('sale_prom.prom_id', $promId)
            ->where('sale_data.sale_date', '>=', $firstDay)
            ->where('sale_data.sale_date', '<=', $lastDay)
            ->where('sale_data.status', '9')
            ->whereNotNull('sale_data.type')
            ->where('sale_data.type', '!=', '')
            ->select(
                DB::raw('DATE_FORMAT(sale_data.sale_date, "%Y-%m") as month'),
                DB::raw('IFNULL(sale_source.name, "未知來源") COLLATE utf8mb4_unicode_ci as source'),
                DB::raw('COUNT(DISTINCT sale_prom.id) as volume'),
                DB::raw('SUM(sale_prom.prom_total) as revenue')
            )
            ->groupBy('month', DB::raw('IFNULL(sale_source.name, "未知來源") COLLATE utf8mb4_unicode_ci'))
            ->orderBy('month')
            ->get();

        // 按月份分組
        return $data->groupBy('month');
    }
}

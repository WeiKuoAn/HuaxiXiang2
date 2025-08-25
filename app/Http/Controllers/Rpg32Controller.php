<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Prom;
use App\Models\Sale;
use App\Models\Sale_prom;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Rpg32Controller extends Controller
{
    public function rpg32(Request $request)
    {
        $years = range(Carbon::now()->year, 2022);
        $currentMonth = Carbon::now()->month;
        $pastMonth = Carbon::now()->subMonth()->month;
        $nowYear = Carbon::now()->year;

        // 取得當月第一天和最後一天（目前）
        $current_year = $request->current_year;
        $current_month = $request->current_month;

        if (!isset($current_month)) {
            $currentMonthStart = Carbon::now()->firstOfMonth();
            $currentMonthEnd = Carbon::now()->endOfMonth();
        } else {
            $currentMonthStart = Carbon::createFromDate($current_year, $current_month, 1)->firstOfMonth();
            $currentMonthEnd = Carbon::createFromDate($current_year, $current_month, 1)->endOfMonth();
        }

        // 取得上個月第一天和最後一天（過去）
        $past_year = $request->past_year;
        $past_month = $request->past_month;
        if (!isset($past_month)) {
            $pastMonthStart = Carbon::now()->subMonth()->firstOfMonth();
            $pastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        } else {
            $pastMonthStart = Carbon::createFromDate($past_year, $past_month)->firstOfMonth();
            $pastMonthEnd = Carbon::createFromDate($past_year, $past_month)->endOfMonth();
        }

        // 安葬處理、後續處理項目
        $proms = Prom::whereIn('type', ['A', 'B'])->where('status', 'up')->get();

        foreach ($proms as $prom) {
            $datas[$prom->id]['name'] = $prom->name;
            $datas[$prom->id]['current_count'] = DB::table('sale_data')
                                                ->leftjoin('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
                                                ->whereNotNull('sale_prom.prom_id')
                                                ->where('sale_data.sale_date', '>=', $currentMonthStart)
                                                ->where('sale_data.sale_date', '<=', $currentMonthEnd)
                                                ->where('sale_prom.prom_id', $prom->id)
                                                ->where('sale_data.status', '9')
                                                ->count();

            $datas[$prom->id]['past_count'] = DB::table('sale_data')
                                                ->leftjoin('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
                                                ->whereNotNull('sale_prom.prom_id')
                                                ->where('sale_data.sale_date', '>=', $pastMonthStart)
                                                ->where('sale_data.sale_date', '<=', $pastMonthEnd)
                                                ->where('sale_prom.prom_id', $prom->id)
                                                ->where('sale_data.status', '9')
                                                ->count();;
            $datas[$prom->id]['current_amount'] = DB::table('sale_data')
                                                ->leftjoin('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
                                                ->whereNotNull('sale_prom.prom_id')
                                                ->where('sale_data.sale_date', '>=', $currentMonthStart)
                                                ->where('sale_data.sale_date', '<=', $currentMonthEnd)
                                                ->where('sale_prom.prom_id', $prom->id)
                                                ->where('sale_data.status', '9')
                                                ->sum('sale_prom.prom_total');
            $datas[$prom->id]['past_amount'] = DB::table('sale_data')
                                                ->leftjoin('sale_prom', 'sale_prom.sale_id', '=', 'sale_data.id')
                                                ->whereNotNull('sale_prom.prom_id')
                                                ->where('sale_data.sale_date', '>=', $pastMonthStart)
                                                ->where('sale_data.sale_date', '<=', $pastMonthEnd)
                                                ->where('sale_prom.prom_id', $prom->id)
                                                ->where('sale_data.status', '9')
                                                ->sum('sale_prom.prom_total');
            
            // 計算成長率
            $datas[$prom->id]['count_growth_rate'] = $this->calculateGrowthRate($datas[$prom->id]['current_count'], $datas[$prom->id]['past_count']);
            $datas[$prom->id]['amount_growth_rate'] = $this->calculateGrowthRate($datas[$prom->id]['current_amount'], $datas[$prom->id]['past_amount']);
            
            // 計算成長金額和數量
            $datas[$prom->id]['count_growth_amount'] = $datas[$prom->id]['current_count'] - $datas[$prom->id]['past_count'];
            $datas[$prom->id]['amount_growth_amount'] = $datas[$prom->id]['current_amount'] - $datas[$prom->id]['past_amount'];
            
            // 評估表現
            $datas[$prom->id]['count_performance'] = $this->evaluatePerformance($datas[$prom->id]['count_growth_rate']);
            $datas[$prom->id]['amount_performance'] = $this->evaluatePerformance($datas[$prom->id]['amount_growth_rate']);
        }

        // 計算總計
        $total_current_count = 0;
        $total_past_count = 0;
        $total_current_amount = 0;
        $total_past_amount = 0;
        
        foreach ($datas as $data) {
            $total_current_count += $data['current_count'];
            $total_past_count += $data['past_count'];
            $total_current_amount += $data['current_amount'];
            $total_past_amount += $data['past_amount'];
        }
        
        $summary = [
            'total_count_growth_rate' => $this->calculateGrowthRate($total_current_count, $total_past_count),
            'total_amount_growth_rate' => $this->calculateGrowthRate($total_current_amount, $total_past_amount),
            'total_count_growth_amount' => $total_current_count - $total_past_count,
            'total_amount_growth_amount' => $total_current_amount - $total_past_amount,
            'total_count_performance' => $this->evaluatePerformance($this->calculateGrowthRate($total_current_count, $total_past_count)),
            'total_amount_performance' => $this->evaluatePerformance($this->calculateGrowthRate($total_current_amount, $total_past_amount))
        ];

        // dd($datas);
        

        // 計算成長率分析
        $growth_analysis = $this->calculateGrowthAnalysis($currentMonthStart, $currentMonthEnd, $pastMonthStart, $pastMonthEnd);

        $months = [
            '01' => ['name' => '一月'],
            '02' => ['name' => '二月'],
            '03' => ['name' => '三月'],
            '04' => ['name' => '四月'],
            '05' => ['name' => '五月'],
            '06' => ['name' => '六月'],
            '07' => ['name' => '七月'],
            '08' => ['name' => '八月'],
            '09' => ['name' => '九月'],
            '10' => ['name' => '十月'],
            '11' => ['name' => '十一月'],
            '12' => ['name' => '十二月'],
        ];
        return view('rpg32.index', compact('years', 'currentMonth', 'pastMonth', 'nowYear', 'current_year', 'current_month', 'request', 'pastMonthStart', 'pastMonthEnd', 'currentMonthStart', 'currentMonthEnd', 'months', 'growth_analysis', 'datas', 'summary'));
    }

    /**
     * 計算成長率分析
     */
    private function calculateGrowthAnalysis($currentStart, $currentEnd, $pastStart, $pastEnd)
    {
        $analysis = [];

        // 1. 基本業務量成長率
        $currentBusinessVolume = $this->getBusinessVolume($currentStart, $currentEnd);
        $pastBusinessVolume = $this->getBusinessVolume($pastStart, $pastEnd);

        $analysis['business_volume'] = [
            'current' => $currentBusinessVolume,
            'past' => $pastBusinessVolume,
            'growth_rate' => $this->calculateGrowthRate($currentBusinessVolume, $pastBusinessVolume),
            'growth_amount' => $currentBusinessVolume - $pastBusinessVolume
        ];

        // 2. 客戶數量成長率
        $currentCustomers = $this->getCustomerCount($currentStart, $currentEnd);
        $pastCustomers = $this->getCustomerCount($pastStart, $pastEnd);

        $analysis['customers'] = [
            'current' => $currentCustomers,
            'past' => $pastCustomers,
            'growth_rate' => $this->calculateGrowthRate($currentCustomers, $pastCustomers),
            'growth_amount' => $currentCustomers - $pastCustomers
        ];

        // 3. 平均客單價成長率
        $currentAvgOrder = $currentBusinessVolume > 0 ? $currentBusinessVolume / $currentCustomers : 0;
        $pastAvgOrder = $pastBusinessVolume > 0 ? $pastBusinessVolume / $pastCustomers : 0;

        $analysis['average_order'] = [
            'current' => round($currentAvgOrder, 2),
            'past' => round($pastAvgOrder, 2),
            'growth_rate' => $this->calculateGrowthRate($currentAvgOrder, $pastAvgOrder),
            'growth_amount' => round($currentAvgOrder - $pastAvgOrder, 2)
        ];

        // 4. 師父誦經次數成長率（示例）
        $currentChanting = $this->getChantingCount($currentStart, $currentEnd);
        $pastChanting = $this->getChantingCount($pastStart, $pastEnd);

        $analysis['chanting'] = [
            'current' => $currentChanting,
            'past' => $pastChanting,
            'growth_rate' => $this->calculateGrowthRate($currentChanting, $pastChanting),
            'growth_amount' => $currentChanting - $pastChanting
        ];

        // 5. 綜合成長指數
        $analysis['composite_index'] = $this->calculateCompositeIndex($analysis);

        return $analysis;
    }

    /**
     * 計算基本成長率
     */
    private function calculateGrowthRate($current, $past)
    {
        if ($past == 0) {
            return $current > 0 ? 100 : 0;  // 如果上期為0，本期有數據則為100%成長
        }

        $growthRate = (($current - $past) / $past) * 100;
        return round($growthRate, 2);
    }

    /**
     * 取得業務量（可根據實際需求調整）
     */
    private function getBusinessVolume($startDate, $endDate)
    {
        // 這裡應該根據您的實際業務邏輯來計算
        // 例如：銷售總額、服務次數等
        return Sale::where('sale_date', '>=', $startDate)
            ->where('sale_date', '<=', $endDate)
            ->where('status', '9')
            ->sum('pay_price');
    }

    /**
     * 取得客戶數量
     */
    private function getCustomerCount($startDate, $endDate)
    {
        return Sale::where('sale_date', '>=', $startDate)
            ->where('sale_date', '<=', $endDate)
            ->where('status', '9')
            ->distinct('customer_id')
            ->count('customer_id');
    }

    /**
     * 取得師父誦經次數（示例方法）
     */
    private function getChantingCount($startDate, $endDate)
    {
        // 這裡應該根據您的實際數據表來查詢
        // 假設有一個 chantings 表記錄誦經次數
        // return Chanting::where('date', '>=', $startDate)
        //                ->where('date', '<=', $endDate)
        //                ->sum('count');

        // 暫時返回模擬數據
        return rand(15, 30);  // 模擬數據，實際應該查詢資料庫
    }

    /**
     * 計算綜合成長指數
     */
    private function calculateCompositeIndex($analysis)
    {
        $weights = [
            'business_volume' => 0.4,  // 業務量權重40%
            'customers' => 0.3,  // 客戶數權重30%
            'average_order' => 0.2,  // 客單價權重20%
            'chanting' => 0.1  // 誦經次數權重10%
        ];

        $compositeScore = 0;
        foreach ($weights as $key => $weight) {
            if (isset($analysis[$key]['growth_rate'])) {
                $compositeScore += $analysis[$key]['growth_rate'] * $weight;
            }
        }

        return round($compositeScore, 2);
    }

    /**
     * 計算師父誦經成長率（您的案例）
     */
    public function calculateChantingGrowth(Request $request)
    {
        $request->validate([
            'current_chanting' => 'required|numeric|min:0',
            'previous_chanting' => 'required|numeric|min:0'
        ]);

        $currentChanting = $request->current_chanting;
        $previousChanting = $request->previous_chanting;

        $growthRate = $this->calculateGrowthRate($currentChanting, $previousChanting);
        $growthAmount = $currentChanting - $previousChanting;

        // 評估成長表現
        $performance = $this->evaluatePerformance($growthRate);

        $result = [
            'current_chanting' => $currentChanting,
            'previous_chanting' => $previousChanting,
            'growth_rate' => $growthRate,
            'growth_amount' => $growthAmount,
            'performance_level' => $performance['level'],
            'performance_description' => $performance['description']
        ];

        return response()->json([
            'success' => true,
            'data' => $result,
            'calculation' => [
                'formula' => '成長率 = (本期 - 上期) / 上期 × 100%',
                'calculation' => "({$currentChanting} - {$previousChanting}) / {$previousChanting} × 100% = {$growthRate}%"
            ]
        ]);
    }

    /**
     * 計算客戶提升度分析
     */
    public function calculateCustomerLift(Request $request)
    {
        $request->validate([
            'current_customers' => 'required|numeric|min:0',
            'previous_customers' => 'required|numeric|min:0',
            'current_revenue' => 'required|numeric|min:0',
            'previous_revenue' => 'required|numeric|min:0'
        ]);

        $currentCustomers = $request->current_customers;
        $previousCustomers = $request->previous_customers;
        $currentRevenue = $request->current_revenue;
        $previousRevenue = $request->previous_revenue;

        // 客戶數量成長率
        $customerGrowth = $this->calculateGrowthRate($currentCustomers, $previousCustomers);

        // 營收成長率
        $revenueGrowth = $this->calculateGrowthRate($currentRevenue, $previousRevenue);

        // 平均客單價
        $currentAvgOrder = $currentCustomers > 0 ? $currentRevenue / $currentCustomers : 0;
        $previousAvgOrder = $previousCustomers > 0 ? $previousRevenue / $previousCustomers : 0;
        $avgOrderGrowth = $this->calculateGrowthRate($currentAvgOrder, $previousAvgOrder);

        // 客戶提升度指數 (客戶成長率 + 客單價成長率的加權平均)
        $customerLiftIndex = ($customerGrowth * 0.6) + ($avgOrderGrowth * 0.4);

        $result = [
            'customer_growth_rate' => $customerGrowth,
            'revenue_growth_rate' => $revenueGrowth,
            'average_order_growth_rate' => $avgOrderGrowth,
            'customer_lift_index' => round($customerLiftIndex, 2),
            'current_avg_order' => round($currentAvgOrder, 2),
            'previous_avg_order' => round($previousAvgOrder, 2)
        ];

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * 您的師父誦經案例演示
     */
    public function chantingExample()
    {
        // 您的案例：2025-07 師父誦經20次，2025-08 師父誦經25次
        $currentChanting = 25;  // 2025-08
        $previousChanting = 20;  // 2025-07

        $growthRate = $this->calculateGrowthRate($currentChanting, $previousChanting);
        $growthAmount = $currentChanting - $previousChanting;
        $performance = $this->evaluatePerformance($growthRate);

        $result = [
            'current_chanting' => $currentChanting,
            'previous_chanting' => $previousChanting,
            'growth_rate' => $growthRate,
            'growth_amount' => $growthAmount,
            'performance_level' => $performance['level'],
            'performance_description' => $performance['description']
        ];

        return response()->json([
            'success' => true,
            'example' => '師父誦經成長率計算示例',
            'data' => [
                'period' => '2025-07 到 2025-08',
                'previous_month' => '2025-07: 20次',
                'current_month' => '2025-08: 25次',
                'calculation' => $result,
                'formula_explanation' => [
                    'basic_formula' => '成長率 = (本期 - 上期) / 上期 × 100%',
                    'calculation' => '成長率 = (25 - 20) / 20 × 100% = 5 / 20 × 100% = 25%',
                    'result' => '師父誦經次數成長 25%',
                    'performance' => $result['performance_description']
                ]
            ]
        ]);
    }

    /**
     * 評估成長表現
     */
    private function evaluatePerformance($growthRate)
    {
        if ($growthRate >= 20) {
            return [
                'level' => 'excellent',
                'description' => '表現優異'
            ];
        } elseif ($growthRate >= 10) {
            return [
                'level' => 'good',
                'description' => '表現良好'
            ];
        } elseif ($growthRate >= 0) {
            return [
                'level' => 'stable',
                'description' => '表現穩定'
            ];
        } elseif ($growthRate >= -10) {
            return [
                'level' => 'declining',
                'description' => '略有下降'
            ];
        } else {
            return [
                'level' => 'poor',
                'description' => '需要關注'
            ];
        }
    }

    /**
     * 測試成長率計算
     */
    public function testGrowthCalculation(Request $request)
    {
        // 模擬您的數據
        $testData = [
            'current_count' => 25,
            'past_count' => 20,
            'current_amount' => 50000,
            'past_amount' => 40000
        ];

        $countGrowthRate = $this->calculateGrowthRate($testData['current_count'], $testData['past_count']);
        $amountGrowthRate = $this->calculateGrowthRate($testData['current_amount'], $testData['past_amount']);

        $result = [
            'test_data' => $testData,
            'count_growth_rate' => $countGrowthRate . '%',
            'amount_growth_rate' => $amountGrowthRate . '%',
            'count_growth_amount' => $testData['current_count'] - $testData['past_count'],
            'amount_growth_amount' => $testData['current_amount'] - $testData['past_amount'],
            'count_performance' => $this->evaluatePerformance($countGrowthRate),
            'amount_performance' => $this->evaluatePerformance($amountGrowthRate),
            'calculation_explanation' => [
                'count_formula' => "數量成長率 = ({$testData['current_count']} - {$testData['past_count']}) / {$testData['past_count']} × 100% = {$countGrowthRate}%",
                'amount_formula' => "金額成長率 = ({$testData['current_amount']} - {$testData['past_amount']}) / {$testData['past_amount']} × 100% = {$amountGrowthRate}%"
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * 生成成長報告摘要
     */
    public function generateGrowthSummary(Request $request)
    {
        $analysisData = $request->input('analysis_data', []);

        $summary = [
            'total_metrics' => count($analysisData),
            'positive_growth' => 0,
            'negative_growth' => 0,
            'stable_growth' => 0,
            'average_growth_rate' => 0,
            'key_highlights' => [],
            'areas_of_concern' => []
        ];

        $totalGrowthRate = 0;
        $validMetrics = 0;

        foreach ($analysisData as $metric => $data) {
            if (isset($data['growth_rate'])) {
                $totalGrowthRate += $data['growth_rate'];
                $validMetrics++;

                if ($data['growth_rate'] > 0) {
                    $summary['positive_growth']++;
                    $summary['key_highlights'][] = "{$metric}: 成長 {$data['growth_rate']}%";
                } elseif ($data['growth_rate'] < 0) {
                    $summary['negative_growth']++;
                    $summary['areas_of_concern'][] = "{$metric}: 下降 " . abs($data['growth_rate']) . '%';
                } else {
                    $summary['stable_growth']++;
                }
            }
        }

        if ($validMetrics > 0) {
            $summary['average_growth_rate'] = round($totalGrowthRate / $validMetrics, 2);
        }

        return response()->json([
            'success' => true,
            'summary' => $summary
        ]);
    }
}

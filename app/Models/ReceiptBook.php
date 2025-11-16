<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Sale;

class ReceiptBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_number',
        'end_number',
        'holder_id',
        'issue_date',
        'returned_at',
        'status',
        'note',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'returned_at' => 'date',
    ];

    /**
     * 保管人
     */
    public function holder()
    {
        return $this->belongsTo(User::class, 'holder_id');
    }

    /**
     * 创建人
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * 获取该收據的所有号码范围
     */
    public function getAllNumbers()
    {
        return range($this->start_number, $this->end_number);
    }

    /**
     * 获取已使用的单号（从 sale_data 查询）
     * 注意：sale_on 格式为 "NO.221"，需要移除 "NO." 前缀
     */
    public function getUsedNumbers()
    {
        $sales = Sale::whereNotNull('sale_on')
            ->where('sale_on', '!=', '')
            ->where('sale_on', 'LIKE', 'NO.%')
            ->get();
        
        $usedNumbers = [];
        foreach ($sales as $sale) {
            // 移除 "NO." 或 "no." 前缀，取得纯数字
            $number = (int) preg_replace('/^NO\./i', '', $sale->sale_on);
            
            // 检查是否在当前收據的范围内
            if ($number >= $this->start_number && $number <= $this->end_number) {
                $usedNumbers[] = $number;
            }
        }
        
        return array_unique($usedNumbers);
    }

    /**
     * 获取已作废的单号（从 sale_data 查询）
     * 注意：目前 sale_data 表中没有作废标记，返回空数组
     * 如需追踪作废记录，请在 sale_data 表中添加相应字段
     */
    public function getVoidNumbers()
    {
        // 暂时返回空数组，因为 sale_data 表中没有作废标记
        // 如果以后添加了作废字段（如 is_cancelled），可以这样查询：
        // return Sale::whereBetween('sale_on', [$this->start_number, $this->end_number])
        //     ->whereNotNull('sale_on')
        //     ->where('sale_on', '!=', '')
        //     ->where('is_cancelled', 1)
        //     ->pluck('sale_on')
        //     ->toArray();
        
        return [];
    }

    /**
     * 获取缺少的单号（跳号）
     */
    public function getMissingNumbers()
    {
        $allNumbers = $this->getAllNumbers();
        $usedNumbers = $this->getUsedNumbers();
        $voidNumbers = $this->getVoidNumbers();
        
        // 已使用的和已作废的都不算缺少
        $accountedNumbers = array_merge($usedNumbers, $voidNumbers);
        
        return array_values(array_diff($allNumbers, $accountedNumbers));
    }

    /**
     * 获取使用统计
     */
    public function getStatistics()
    {
        $allNumbers = $this->getAllNumbers();
        $usedNumbers = $this->getUsedNumbers();
        $voidNumbers = $this->getVoidNumbers();
        $missingNumbers = $this->getMissingNumbers();

        return [
            'total' => count($allNumbers),
            'used' => count($usedNumbers),
            'void' => count($voidNumbers),
            'missing' => count($missingNumbers),
            'usage_rate' => count($allNumbers) > 0 ? round((count($usedNumbers) / count($allNumbers)) * 100, 2) : 0,
        ];
    }

    /**
     * 获取单号详情（包含关联的销售单）
     * 注意：sale_on 格式为 "NO.221"，需要添加 "NO." 前缀来查询
     */
    public function getNumberDetails()
    {
        $allNumbers = $this->getAllNumbers();
        $details = [];

        foreach ($allNumbers as $number) {
            // 查询时添加 "NO." 前缀，因为数据库中存储的格式是 "NO.221"
            $saleData = Sale::where('sale_on', 'NO.' . $number)->first();
            
            $details[$number] = [
                'number' => $number,
                // 目前只区分已使用和未使用，如果以后需要区分作废，可以检查 is_cancelled 字段
                'status' => $saleData ? 'used' : 'unused',
                'sale_data' => $saleData,
            ];
        }

        return $details;
    }
}

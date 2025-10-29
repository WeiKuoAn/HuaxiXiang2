<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OvertimeRecordLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'overtime_record_id',
        'action',
        'action_by',
        'action_at',
        'source',
        'old_values',
        'new_values',
        'note'
    ];

    protected $casts = [
        'action_at' => 'datetime',
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * 取得加班記錄
     */
    public function overtimeRecord()
    {
        return $this->belongsTo(OvertimeRecord::class);
    }

    /**
     * 取得操作人員
     */
    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by');
    }

    /**
     * 取得操作類型文字
     */
    public function getActionTextAttribute()
    {
        return $this->action === 'created' ? '新增' : '編輯';
    }

    /**
     * 取得來源文字
     */
    public function getSourceTextAttribute()
    {
        $sources = [
            'overtime_create' => '加班管理新增',
            'overtime_edit' => '加班管理編輯',
            'increase_manual' => '加成管理手動新增',
            'increase_edit' => '加成管理編輯'
        ];
        
        return $sources[$this->source] ?? $this->source;
    }

    /**
     * 取得變更摘要
     */
    public function getChangesSummaryAttribute()
    {
        if ($this->action === 'created') {
            return '建立新的加班記錄';
        }
        
        $summary = [];
        
        if ($this->old_values && $this->new_values) {
            if (isset($this->old_values['minutes']) && isset($this->new_values['minutes'])) {
                if ($this->old_values['minutes'] != $this->new_values['minutes']) {
                    $summary[] = "加班分鐘：{$this->old_values['minutes']} → {$this->new_values['minutes']}";
                }
            }
            
            if (isset($this->old_values['reason']) && isset($this->new_values['reason'])) {
                if ($this->old_values['reason'] != $this->new_values['reason']) {
                    $summary[] = "事由：{$this->old_values['reason']} → {$this->new_values['reason']}";
                }
            }
        }
        
        return implode('、', $summary) ?: '無變更';
    }
}

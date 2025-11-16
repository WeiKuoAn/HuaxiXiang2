<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReceiptBook;

class ReceiptBookBulkSeeder extends Seeder
{
    public function run(): void
    {
        $start = 3051;
        $maxEnd = 15000;
        $count = 0;

        while ($start <= $maxEnd) {
            $end = min($start + 49, $maxEnd);

            $exists = ReceiptBook::where('start_number', $start)
                ->where('end_number', $end)
                ->exists();

            if (!$exists) {
                ReceiptBook::create([
                    'start_number' => $start,
                    'end_number' => $end,
                    'holder_id' => null,
                    'issue_date' => null,
                    'returned_at' => null,
                    'status' => 'unused', // 預設未使用
                    'note' => null,
                ]);
                $count++;
            }

            $start += 50;
        }

        $this->command?->info("Receipt books inserted: {$count}");
    }
}



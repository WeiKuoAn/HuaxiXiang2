<?php

namespace App\Console\Commands;

use App\Models\Lamp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloseExpiredLamps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lamp:auto-close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically close lamps whose end date has already passed';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $closed = Lamp::closeExpired();

        $message = "Auto-closed {$closed} expired lamp records.";
        $this->info($message);
        Log::info('[lamp:auto-close]', ['closed' => $closed]);

        return Command::SUCCESS;
    }
}


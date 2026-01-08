<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EngineerLog;
use Illuminate\Support\Carbon;

class PopulateLogMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:log_month';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate log_month for existing engineer logs based on first date in entries';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $logs = EngineerLog::whereNull('log_month')->get();

        foreach ($logs as $log) {
            $entries = json_decode($log->entries, true);
            if (is_array($entries) && !empty($entries)) {
                $firstDate = $entries[0]['date'];
                $logMonth = Carbon::parse($firstDate)->format('Y-m');
                $log->update(['log_month' => $logMonth]);
            }
        }

        $this->info('Log month populated for ' . $logs->count() . ' records.');

        return Command::SUCCESS;
    }
}

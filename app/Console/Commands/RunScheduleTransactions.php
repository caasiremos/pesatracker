<?php

namespace App\Console\Commands;

use App\Http\Repositories\ScheduledTransactionRepository;
use Illuminate\Console\Command;

class RunScheduleTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-schedule-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run scheduled transactions that are due today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        (new ScheduledTransactionRepository())->runScheduledTransactions();
    }
}

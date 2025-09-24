<?php

namespace App\Console\Commands;

use App\Payment\Relworx\MobileMoney;
use App\Utils\Logger;
use Illuminate\Console\Command;

class CheckRelworxTransactionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-relworx-transaction-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of all the transactions in the database and update the with the status from relworx and update the customer balance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Logger::info('Checking relworx transaction status');
        //(new MobileMoney())->getTransactionStatus();
    }
}

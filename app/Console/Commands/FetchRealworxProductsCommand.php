<?php

namespace App\Console\Commands;

use App\Payment\Relworx\Products;
use Illuminate\Console\Command;

class FetchRealworxProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-realworx-products-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all the products available on realworx';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        (new Products())->getProducts();
    }
}

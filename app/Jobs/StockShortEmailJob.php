<?php

namespace App\Jobs;

use App\Models\Product;
use App\Mail\StockShort;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class StockShortEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $products;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($productIds)
    {
        $this->products = Product::whereIn('id', $productIds)->get();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to('contact@subdine.com')->send(new StockShort($this->products));
    }
}

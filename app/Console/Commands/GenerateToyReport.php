<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateToyReport extends Command
{
    protected $signature = 'toy:report';

    protected $description = 'Generate a report of best-selling toys';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Retrieve the best selling toys
        $bestSellingToys = DB::table('orders_toys')
            ->select('toy_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('toy_id')
            ->orderByDesc('total_quantity')
            ->limit(10) // Adjust the limit as needed
            ->get()->toArray();

        \Log::info('Best Selling Toys:', $bestSellingToys);
        // Prepare report content
        $report = "Best Selling Toys Report:\n\n";
        foreach ($bestSellingToys as $index => $toy) {
            $toyDetails = DB::table('toys')->where('id', $toy->toy_id)->first();
            $report .= ($index + 1) . ". " . $toyDetails->name . " - Total Quantity Sold: " . $toy->total_quantity . "\n\n";
        }

        // Example email sending
        $adminEmail = 'walidbabi@localhost.com';
        \Mail::raw($report, function ($message) use ($adminEmail) {
            $message->from('admin@localhost.com', 'Little Dreamers') // Change the sender name here
            ->to($adminEmail)
                ->subject('Best Selling Toys Report');
        });

        $this->info('Best-selling toys report sent successfully!');
    }

}
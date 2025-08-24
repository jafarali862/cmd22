<?php

namespace App\Console\Commands;

use App\Models\PharmacyMedicine;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteOldRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'records:delete-old';
   

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenMinutesAgo = Carbon::now()->subMinutes(10);
        PharmacyMedicine::where('created_at', '<=', $tenMinutesAgo)->delete();
        $this->info('Old records deleted successfully!');
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Contract;
use Carbon\Carbon;


class UpdateContractStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-contract-status';

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
        $now = Carbon::now();
        $contracts = Course::where('end_date', '>=', $now)->where('status', 0)->get();
        
        
    
        /*foreach ($contracts as $contract) {
            $contract->status = 1;
            $contract->save();
        }*/
        
        Contract::where('end_date', '>=', $now)->where('status', 0)
        ->update(['status' => 1]);


    }
}
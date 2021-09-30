<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Auth;
use DB;

class AutoprocedureCron extends Command
{
/**
* The name and signature of the console command.
*
* @var string
*/
protected $signature = 'autoprocedure:cron';

/**
* The console command description.
*
* @var string
*/
protected $description = 'Command description';

/**
* Create a new command instance.
*
* @return void
*/
public function __construct()
{
    parent::__construct();
    }

    /**
    * Execute the console command.
    *
    * @return mixed
    */
    public function handle()
    {
        $this->info('Demo:Cron Cummand Run successfully!');
        \Log::info("Cron is working fine!");
        echo 'sdfsdfsdf';
        DB::beginTransaction();
        try {

            DB::table('R')->insert([
                'R' => '1'
            ]);
            DB::commit();
        } catch(\Exception $e){

        DB::rollback();
        }
        $this->info('Demo:Cron Cummand Run successfully!');
    }
}

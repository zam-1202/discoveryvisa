<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ApplicationBatchController;
use Carbon\Carbon;

class GenerateSubmissionList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:submission_list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a PDF for each branch containing the submission list for the current day.';

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
		$this->info('Creating submission list for each branch...');
		
        // ApplicationBatchController::generateSubmissionList();
		
		$this->info('Submission lists for ' . Carbon::now()->toDateString() . ' has been generated.');
    }
}

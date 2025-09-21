<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AutoPunchOutService;

class AutoPunchOutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'punch:auto-out 
                            {--force : Force auto punch-out for all open punches}
                            {--today : Process only today\'s punches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically punch out employees who forgot to punch out';

    protected $autoPunchOutService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AutoPunchOutService $autoPunchOutService)
    {
        parent::__construct();
        $this->autoPunchOutService = $autoPunchOutService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting auto punch-out process...');

        try {
            if ($this->option('today')) {
                // Process only today's punches
                $results = $this->autoPunchOutService->processEndOfDayPunchOuts();
                $this->info('Processed today\'s open punches.');
            } else {
                // Process previous days' open punches
                $results = $this->autoPunchOutService->processAutoPunchOuts();
            }

            $this->info("Total processed: {$results['processed']}");
            if ($results['errors'] > 0) {
                $this->warn("Errors encountered: {$results['errors']}");
            }

            $this->table(
                ['Punch ID', 'Employee ID', 'Status', 'Message'],
                collect($results['details'])->map(function ($detail) {
                    return [
                        $detail['punch_id'] ?? 'N/A',
                        $detail['employee_id'] ?? 'N/A',
                        $detail['status'],
                        $detail['message'] ?? 'Success',
                    ];
                })
            );

            $this->info('Auto punch-out process completed successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Auto punch-out process failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
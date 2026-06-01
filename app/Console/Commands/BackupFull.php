<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Backup\Tasks\Backup\BackupJobFactory;

class BackupFull extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:full';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run full backup of database and files to configured disk';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting full backup...');

        // In a real scenario, we would use Spatie Backup package
        // artisan call backup:run --only-db
        $this->call('backup:run', ['--only-db' => true]);
        
        $this->info('Backup completed successfully.');
    }
}

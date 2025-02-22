<?php

namespace App\Console\Commands;

use App\Services\SyncAzureData;
use Exception;
use Illuminate\Console\Command;

class ReadQueueMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:azure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read messages from Azure queue and sync';

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
     * @return int
     * @throws Exception
     */
    public function handle(): int
    {
        $syncObj = new SyncAzureData();

        $process = 10000;
        $index = 1;
        while ($index <= $process) {
            $syncObj->getOneMessageAndInsert('DELETE');
            $index++;
        }

        return 0;
    }
}

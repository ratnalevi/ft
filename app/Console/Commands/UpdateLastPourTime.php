<?php

namespace App\Console\Commands;

use App\Models\DeviceLine;
use App\Models\LineData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateLastPourTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:last-pour-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to update last pour time';

    /**
     * Execute the console command.
     */
    public function handle(): bool
    {
        $subquery = LineData::select('*')
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY DevicesID, DeviceLinesID ORDER BY ReportDateTime DESC) AS row_num')
            ->where('Pulse', '>', 0);

        $lastPourTimes = DB::table(DB::raw("({$subquery->toSql()}) as ranked"))
            ->mergeBindings($subquery->getQuery())
            ->select('DevicesID', 'DeviceLinesID', 'Pulse', 'ReportDateTime')
            ->where('row_num', 1)
            ->get();

        if (empty($lastPourTimes)) {
            echo "No Device Lines found\n";
            return false;
        }

        echo "Device Lines found: " . count($lastPourTimes) . "\n";
        foreach ($lastPourTimes as $lastPourTime) {
            echo "Updating Device Line " . $lastPourTime->DevicesID . " Line " . $lastPourTime->DeviceLinesID;
            $deviceLine = DeviceLine::where('DevicesID', $lastPourTime->DevicesID)->where('Line', $lastPourTime->DeviceLinesID)->first();
            if (empty($deviceLine)) {
                echo " Device Line not found \n";
                continue;
            }
            echo " LastPour " . $lastPourTime->Pulse . " LastPourDateTime " . $lastPourTime->ReportDateTime . "\n";
            $deviceLine->LastPourDateTime = $lastPourTime->ReportDateTime;
            $deviceLine->LastPour = $lastPourTime->Pulse;
            $deviceLine->update();
        }

        return true;
    }
}

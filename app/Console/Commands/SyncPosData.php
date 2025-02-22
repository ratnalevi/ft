<?php

namespace App\Console\Commands;

use App\Models\BeerBrand;
use App\Models\PosData;
use App\Models\PosItem;
use App\Models\POSStoreIDToLocation;
use App\Models\PosSyncConfig;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncPosData extends Command
{
    public const CFG_DIR_UPLOAD = [
        'local' => "/Users/levi/Workspace/UPLOAD/TOAST",
        'dev' => "/var/www/html/TOAST"
    ];
    public const CFG_SFTP_FILE = "ItemSelectionDetails.csv";
    public const ITEM_SFTP_FILE = "AllItemsReport.csv";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:pos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To sync data';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $stores = POSStoreIDToLocation::where('RecordStatus', 1)->get();
        foreach ($stores as $store) {
            $days = 1;
            Log::channel('toast')->info('========== Processing Toast data for store: ' . $store->StoreID . ' ==========');
            while ($days > 0) {
                $t = '-' . $days . ' days';
                $date = date_create(date("Ymd"))->modify($t)->format('Ymd');
                Log::channel('toast')->info('Processing data for date: ' . $date);
                $days--;

                $dirName = self::CFG_DIR_UPLOAD[config('app.env')] . "/" . $store->StoreID . "/" . $date;
                if (is_dir($dirName)) {
                    Log::channel('toast')->error("Toast directory does exist and would have been processed");
                    continue;
                }

                Log::channel('toast')->info('========== Downloading Toast data ==========');
                if (!$this->downloadToastData($store, $date)) {
                    Log::channel('toast')->error("Unable to download Toast data");
                    continue;
                }

                if (!is_dir($dirName)) {
                    Log::channel('toast')->error("Toast data directory doesn't exists after download! for $date and $dirName");
                    continue;
                }

                Log::channel('toast')->info('========== Processing Items data ==========');
                $this->processItemsData($store, $date);

                Log::channel('toast')->info('========== Processing Transactions data ==========');
                $this->processTransactionsData($store, $date);
            }
        }
    }

    public function downloadToastData($store, $date): bool
    {
        $dirName = self::CFG_DIR_UPLOAD[config('app.env')] . "/" . $store->StoreID . "/" . $date;
        Log::channel('toast')->info("Creating new directory: " . $dirName);
        if (!is_dir($dirName)) {
            mkdir($dirName);
        }

        $command = "cd " . $dirName . "; sftp -r -i $dirName/../../../keys/id_rsa " . $store->ToastSFTPUserName . "@" . $store->toastsftpurl . ":" . $store->StoreID . "/" . $date . "/* " . $dirName;
        Log::channel('toast')->info('Downloading Toast data');
        Log::channel('toast')->info($command);
        shell_exec($command);

        if (!is_dir($dirName)) {
            Log::channel('toast')->error("Toast data directory doesn't exists!");
            return false;
        }

        return true;
    }

    public function processItemsData($store, $date): void
    {
        $fileName = self::CFG_DIR_UPLOAD[config('app.env')] . "/" . $store->StoreID . "/" . $date . "/" . self::ITEM_SFTP_FILE;
        if (!file_exists($fileName)) {
            Log::channel('toast')->error("File doesn't exists!");
            return;
        }

        $itemRow = 0;
        $itemsHandle = fopen($fileName, "r");
        $headers = [];
        while (($data = fgetcsv($itemsHandle, 1000, ",")) != false) {
            $itemRow++;
            if ($itemRow == 1) {
                foreach ($data as $key => $value) {
                    $headers[$value] = $key;
                }
            }

            if ($itemRow < 3) {
                continue;
            }

            $itemNum = $data[$headers['Master ID']];
            $menuName = $data[$headers['Menu Name']];
            $menuGroup = $data[$headers['Menu Group']];
            $menuSubGroup = $data[$headers['Subgroup']];
            $itemName = $data[$headers['Menu Item']];

            if ((empty($menuGroup) || empty($menuSubGroup)) && empty($itemName)) {
                continue;
            }

            if (!$this->isMenuAllowedToProcess($store->StoreID, $menuName, $menuGroup, $menuSubGroup)) {
                continue;
            }

            Log::channel('toast')->info("Menu Name: " . $menuName . " Menu Group: $menuGroup Sub Group: $menuSubGroup Menu Item: $itemName");

            $beerOunceConfig = [
                'Craft Draft Beer' => 16,
                'Draft Beer' => 16,
                'Schooner Beer' => 30,
                'Pitcher Beer' => 64,
            ];

            $ounces = $beerOunceConfig[$menuGroup] ?? $beerOunceConfig[$menuSubGroup];
            $itemDesc = 'Draft Beer ' . $ounces . 'oz';

            $accountId = $store->AccountID;
            $locationId = $store->LocationID;

            $brandName = str_replace('PIT ', '', $itemName);
            $brandName = str_replace('SCH ', '', $brandName);
            $brandName = str_replace('DFT ', '', $brandName);
            $brandName = str_replace('  ', '', $brandName);
            $brandName = str_replace('Blvd', 'Boulevard', $brandName);

            $beerBrand = BeerBrand::where('Brand', 'like', DB::raw('"%' . $brandName . '%"'))->first();
            if (!empty($beerBrand)) {
                $beerBrandId = $beerBrand->BeerBrandsID ?? 0;
            } else {
                $beerBrandId = 0;
            }

            $posItem = PosItem::where('AccountID', $accountId)->where('LocationID', $locationId)
                ->where('itemNum', $itemNum)->first();
            Log::channel('toast')->info("Brand name to search for: $brandName and Brand ID: " . $beerBrandId);

            if (empty($posItem)) {
                Log::channel('toast')->info('Creating new POS Item');
                $posItem = new PosItem();
                $posItem->AccountID = $accountId;
                $posItem->LocationID = $locationId;
                $posItem->BeerBrandID = $beerBrandId;
                $posItem->Ounces = $ounces;
                $posItem->ItemNUM = $itemNum;
                $posItem->ItemName = $itemName;
                $posItem->ItemDESC = $itemDesc;
                $posItem->ItemFLAG = 1;
                $posItem->RecordStatus = 1;
                $posItem->InsertDateTime = date('Y-m-d H:i:s');
                $posItem->UpdateDateTime = date('Y-m-d H:i:s');
                $posItem->save();
            } else {
                Log::channel('toast')->info('POS Item exists: ID: ' . $posItem->POSItemsID);
            }
        }
    }

    private function isMenuAllowedToProcess($storeId, $menuName = '', $menuGroup = '', $subGroup = '', $salesCategory = ''): bool
    {
        if (!empty($menuName)) {
            if (!$this->isTypeAllowedToProcess($storeId, 'menuName', $menuName)) {
                return false;
            }
        }

        if (!empty($menuGroup)) {
            if (!$this->isTypeAllowedToProcess($storeId, 'menuGroup', $menuGroup)) {
                return false;
            }
        }

        if (!empty($subGroup)) {
            if (!$this->isTypeAllowedToProcess($storeId, 'menuSubgroup', $subGroup)) {
                return false;
            }
        }

        if (!empty($salesCategory)) {
            if (!$this->isTypeAllowedToProcess($storeId, 'salesCategory', $salesCategory)) {
                return false;
            }
        }

        return true;
    }

    private function isTypeAllowedToProcess($storeId, $type, $value): bool
    {
        $posMenuConfig = PosSyncConfig::where('storeid', '=', $storeId)->where('name', '=', $type)->first();

        if (!empty($posMenuConfig) && !empty($posMenuConfig->value)) {
            $allowedTypes = explode(',', $posMenuConfig->value);
            if (in_array(trim($value), $allowedTypes)) {
                return true;
            }
        }

        return false;
    }

    public function processTransactionsData($store, $date): void
    {
        $fileName = self::CFG_DIR_UPLOAD[config('app.env')] . "/" . $store->StoreID . "/" . $date . "/" . self::CFG_SFTP_FILE;
        Log::channel('toast')->info('File to process for Items data: ' . $fileName);
        if (!file_exists($fileName)) {
            Log::channel('toast')->error("File doesn't exists!");
            return;
        }

        $row = 0;
        $headers = [];
        $handle = fopen($fileName, "r");
        while (($data = fgetcsv($handle, 1000, ",")) != false) {
            $row++;
            if ($row == 1) {
                foreach ($data as $key => $value) {
                    $headers[$value] = $key;
                }
            }

            if ($row < 3) {
                continue;
            }

            $itemNum = $data[$headers['Master Id']];
            $menuName = $data[$headers['Sales Category']];
            $menuGroup = $data[$headers['Menu Group']];
            $itemName = $data[$headers['Menu Item']];

            if (!$this->isMenuAllowedToProcess($store->StoreID, '', $menuGroup, '', $menuName)) {
                continue;
            }

            if (strtoupper($data[$headers['Void?']]) == 'TRUE') {
                continue;
            }

            Log::channel('toast')->info("Menu Name: " . $menuName . ", Menu Group: $menuGroup, Menu Item: $itemName");

            $posItemData = POSStoreIDToLocation::select(
                'POSStoreIDToLocation.AccountID',
                'POSStoreIDToLocation.LocationID',
                'Devices.DevicesID',
                'POSItems.POSItemsID',
                'POSItems.BeerBrandID',
                'POSItems.ItemNUM',
                'POSItems.Ounces'
            )
                ->leftJoin('Devices', function ($join) {
                    $join->on('Devices.AccountID', '=', 'POSStoreIDToLocation.AccountID');
                    $join->on('Devices.LocationID', '=', 'POSStoreIDToLocation.LocationID');
                })
                ->leftJoin('POSItems', function ($join) use ($itemNum) {
                    $join->on('POSItems.ItemNUM', '=', DB::raw("'{$itemNum}'"));
                })->where('POSStoreIDToLocation.StoreID', $store->StoreID)->first();

            if (empty($posItemData)) {
                Log::channel('toast')->error('Unable to get mapped Pos Item');
                continue;
            }

            Log::channel('toast')->info('Mapped POS Item ID: ' . $posItemData->POSItemsID . ' Beer Brand ID: ' . $posItemData->BeerBrandID);

            $recordDateTime = DateTime::createFromFormat('m/d/y H:i A', $data[4]);
            $dayDateTime = $recordDateTime->format('Y-m-d H:i:s');

            $posData = new PosData();
            $posData->AccountID = $posItemData->AccountID;
            $posData->LocationID = $posItemData->LocationID;
            $posData->DeviceID = $posItemData->DevicesID;
            $posData->POSItemsID = $posItemData->POSItemsID;
            $posData->StoreName = $data[0];
            $posData->DayDateTime = $dayDateTime;
            $posData->OrderNumber = $data[1];
            $posData->ItemNUM = $itemNum;
            $posData->ItemName = $itemName;
            $posData->EmployeeName = $data[6];
            $posData->Quantity = $data[24];
            $posData->totalOunces = $data[24] * ($posItemData->Ounces ?? 0);
            $posData->GrossAMT = $data[21];
            $posData->TranAMT = $data[23];
            $posData->CompAMT = $data[22];
            $posData->VoidAMT = strtoupper($data[26]) == 'TRUE' ? $data[21] : 0;
            $posData->TranFLAG = 1;
            $posData->RecordStatus = 1;
            $posData->InsertDateTime = date('Y-m-d H:i:s');
            $posData->UpdateDateTime = date('Y-m-d H:i:s');
            $posData->save();
        }
    }
}

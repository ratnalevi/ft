<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POSStoreIDToLocation extends Model
{
    use HasFactory;

    public const CREATED_AT = 'InsertDateTime';
    public const UPDATED_AT = 'UpdateDateTime';

    public $timestamps = false;
    protected $table = "POSStoreIDToLocation";

    protected $primaryKey = 'POSStoreIDToLocationID';

    protected $fillable = [
        'StoreID',
        'AccountID',
        'LocationID',
        'ToastExportID',
        'ToastSFTPUsername',
        'toastsftpurl',
        'RecordStatus',
        'InsertDateTime',
        'UpdateDateTime'
    ];
}

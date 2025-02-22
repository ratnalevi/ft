<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PosItem
 *
 * @property $POSItemsID
 * @property $AccountID
 * @property $LocationID
 * @property $BeerBrandID
 * @property $BeerTypeID
 * @property $Ounces
 * @property $ItemNUM
 * @property $ItemName
 * @property $ItemDESC
 * @property $ItemFLAG
 * @property $RecordStatus
 * @property $InsertDateTime
 * @property $UpdateDateTime
 *
 * @package App
 * @mixin Builder
 */
class PosData extends Model
{
    public const CREATED_AT = 'InsertDateTime';
    public const UPDATED_AT = 'UpdateDateTime';
    /**
     * @var int|mixed
     */
    protected $perPage = 10;
    protected $table = 'POSData';
    protected $primaryKey = 'POSDataID';

    protected $fillable = ['POSBatchID', 'POSParentDataID', 'AccountID', 'LocationID', 'DeviceID', 'POSItemsID', 'BeerBrandsID', 'UserID', 'StoreName', 'DayDateTime', 'OrderNumber', 'ItemNUM', 'ItemName', 'EmployeeName', 'Quantity', 'TotalOunces', 'GrossAMT', 'TranAMT', 'CompAMT', 'VoidAMT', 'TranFLAG', 'AuditDateTime', 'AuditStatus', 'RecordStatus', 'InsertDateTime', 'UpdateDateTime'];
}

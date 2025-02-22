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
class PosItem extends Model
{
    public const CREATED_AT = 'InsertDateTime';
    public const UPDATED_AT = 'UpdateDateTime';
    protected $perPage = 10;
    protected $table = 'POSItems';
    protected $primaryKey = 'POSItemsID';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['POSItemsID', 'AccountID', 'LocationID', 'BeerBrandID', 'Ounces', 'ItemNUM', 'ItemName', 'ItemDESC', 'ItemFLAG', 'RecordStatus', 'InsertDateTime', 'UpdateDateTime'];


}

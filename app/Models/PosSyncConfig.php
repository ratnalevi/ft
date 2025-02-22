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
class PosSyncConfig extends Model
{
    /**
     * @var int|mixed
     */
    protected $table = 'possyncconfig';
    protected $primaryKey = 'possyncid';

    protected $fillable = [];
}

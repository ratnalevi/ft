<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorLine extends Model
{
    use HasFactory;

    public const CREATED_AT = 'InsertDateTime';
    public const UPDATED_AT = 'UpdateDateTime';
    protected $table = "DeviceLines";
    protected $primaryKey = "DistributorLineID";
    protected $fillable = [
        "DeviceLinesID",
        "DevicesID",
        "DeviceStatus",
        "Line",
        "BeerTubingID",
        "BeerBrandsID",
        "KegTypeID",
        "OptTemp",
        "OptPressure",
        "TempPressAlert",
        "TempPressAlertTimeOut",
        "TempAlertValue",
        "PressAlertValue",
        "KegCost",
        "LineLength",
        "LineType",
        "RecordStatus",
        "InsertDateTime",
        "UpdateDateTime",
    ];

    public function LineData()
    {
        return $this->hasMany(BeerBrand::class, "BeerBrandsID", "BeerBrandsID");
    }
}

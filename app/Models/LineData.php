<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineData extends Model
{
    use HasFactory;

    public const CREATED_AT = 'InsertDateTime';
    public const UPDATED_AT = 'UpdateDateTime';
    protected $table = "LineData";
    protected $primaryKey = 'LineDataID';
    protected $fillable = [
        "LineDataID",
        "DevicesID",
        "ReportDateTime",
        "DeviceLinesID",
        "DistAccountID",
        "Unit",
        "Temp",
        "TempMin",
        "TempMax",
        "PressureUnit",
        "Pres",
        "PresMin",
        "PresMax",
        "Depress",
        "Repress",
        "TDS",
        "TDSMin",
        "TDSMax",
        "Pulse",
        "RecordStatus",
        "InsertDateTime",
        "UpdateDateTime",
    ];

    public function linedataSet()
    {
        return $this->hasMany(BeerBrand::class, "BeerBrandsID", "BeerBrandsID");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    public const CREATED_AT = 'InsertDateTime';
    public const UPDATED_AT = 'UpdateDateTime';
    protected $table = "Location";
    protected $primaryKey = 'LocationID';
    protected $fillable = [
        "LocationID",
        "LocationType",
        "UserType",
        "UserID",
        "LocationName",
        "LocationDESC",
        "LocationNum",
        "PhonePrimary",
        "PhoneSeconday",
        "Fax",
        "Email",
        "EmailTechnical",
        "Address1",
        "Address2",
        "City",
        "PostalCode",
        "State",
        "CountryCode",
        'TimeZone',
        "Lat",
        "Lon",
        "RecordStatus",
        "InsertDateTime",
        "UpdateDateTime",
        "FromDateTime",
        "EndDateTime",
        "TotalHours",
    ];

    public function userdemographics()
    {
        return $this->belongsTo(UserDemoGraphic::class, 'UserID', 'UserID');
    }

}

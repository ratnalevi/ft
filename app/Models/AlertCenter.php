<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertCenter extends Model
{
    use HasFactory;

    public const CREATED_AT = 'InsertDateTime';
    public const UPDATED_AT = 'UpdateDateTime';
    protected $table = "Alerts";
    protected $primaryKey = "AlertID";
    protected $fillable = [
        'AlertID',
        'AlertName',
        'AlertDescription',
        'RecordStatus',
        'InsertDateTime',
        'UpdateDateTime'
    ];
}

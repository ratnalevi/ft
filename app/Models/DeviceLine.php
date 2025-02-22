<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceLine extends Model
{
    use HasFactory;

    public const CREATED_AT = 'InsertDateTime';
    public const UPDATED_AT = 'UpdateDateTime';
    protected $table = "DeviceLines";
    protected $primaryKey = 'DeviceLinesID';
    protected $fillable = ['LastPourDateTime', 'LastPour'];
}

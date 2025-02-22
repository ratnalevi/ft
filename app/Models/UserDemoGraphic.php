<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDemoGraphic extends Model
{
    use HasFactory;

    public const CREATED_AT = 'InsertDateTime';
    public const UPDATED_AT = 'UpdateDateTime';
    protected $table = 'UserDemographic';
    protected $primaryKey = 'UserID';
    protected $fillable = ['UserID', 'FirstName', 'Middle', 'LastName', 'LocationID', 'RecordStatus'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    use HasFactory;

    public const CREATED_AT = 'InsertDateTime';
    public const UPDATED_AT = 'UpdateDateTime';
    public $timestamps = false;
    protected $table = "UserAccount";
    protected $primaryKey = 'UserAccountID';
    protected $fillable = [
        'UserAccountID',
        'UserID',
        'AccountID',
        'LocationID',
        'ConfigurationID',
        'AdminAccess',
        'IsActive',
        'RecordStatus',
        'InsertDateTime',
        'UpdateDateTime'
    ];
}

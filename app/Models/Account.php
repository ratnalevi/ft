<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    public const CREATED_AT = 'InsertDateTime';
    public const UPDATED_AT = 'UpdateDateTime';
    public $timestamps = false;
    protected $table = "Accounts";
    protected $primaryKey = 'AccountID';
    protected $fillable = [
        'AccountID',
        'LocationID',
        'ConfigurationID',
        'AccountName',
        'AccountLocationID',
        'IsActive',
        'Comments',
        'RecordStatus',
        'InsertDateTime',
        'UpdateDateTime'
    ];
}

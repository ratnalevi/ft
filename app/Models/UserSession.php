<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;


    public const CREATED_AT = 'InsertDateTime';
    public const UPDATED_AT = 'UpdateDateTime';
    public $timestamps = false;
    protected $table = "UserSession";
    protected $primaryKey = 'UserID';
    protected $fillable =
        [
            'Users_ID',
            'IPAddress',
            'SessionDateTime',
            'ExpireSeconds',
            'SessionID',
            'RecordStatus',
            'InsertDateTime',
            'UpdateDateTime'
        ];
}

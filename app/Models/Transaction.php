<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public const CREATED_AT = 'InsertDateTime';
    public const UPDATED_AT = 'UpdateDateTime';
    protected $table = "Transactions";
    protected $primaryKey = "TransID";

    public function linedata()
    {
        return $this->hasOne(LineData::class, '');
    }

}

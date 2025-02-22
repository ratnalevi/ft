<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    public const CREATED_AT = 'InsertDateTime';
    public const UPDATED_AT = 'UpdateDateTime';
    protected $table = "Distributors";
    protected $primaryKey = "DistributorID";
}

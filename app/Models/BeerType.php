<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeerType extends Model
{
    use HasFactory;

    public const CREATED_AT = 'InsertDateTime';
    public const UPDATED_AT = 'UpdateDateTime';
    protected $table = "BeerType";
    protected $primaryKey = 'BeerTypeID';

    public function beerbrands()
    {
        return $this->hasMany(BeerBrand::class, 'BeerTypeID', 'BeerTypeID');
    }

}

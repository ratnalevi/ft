<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeerBrand extends Model
{
    use HasFactory;

    public const CREATED_AT = 'InsertDateTime';
    public const UPDATED_AT = 'UpdateDateTime';
    protected $table = "BeerBrands";
    protected $primaryKey = 'BeerBrandsID';
    protected $fillable = [
        'BeerBrandsID',
        'BrewerID',
        'BeerTypeID',
        'Brand',
        'ABV',
        'IsActive',
        'Comments',
        'RecordStatus',
    ];

    public function distributors()
    {
        $this->belongsToMany(DistributorLine::class, 'BeerBrandsID', 'BeerBrandsID');
    }

    public function beertypes()
    {
        return $this->belongsTo(BeerType::class, 'BeerTypeID', 'BeerTypeID');
    }
}

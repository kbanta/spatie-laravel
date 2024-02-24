<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'type_id',
        'family_id',
        'brand_id',
        'category_id',
        'name',
        'description',
        'sku',
        'inventory',
        'weight',
        'dimension',
        'size_id',
        'color_id',
    ];

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }
    public function price() {
        return $this->hasMany(Price::class);
    }
}

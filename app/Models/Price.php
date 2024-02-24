<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Price extends Model
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
        'product_id',
        'price',
        'cost',
        'discount',
        'special_price_start',
        'special_price_end',
    ];
    public function product() {
        return $this->belongsTo(Product::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'product_images';
    protected $fillable=['image','product_id'];
    public $timestamps = true;

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }
}

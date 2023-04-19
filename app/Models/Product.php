<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable=['name','price','quantity','description'];
    public $timestamps = true;

    public function images()
    {
        return $this->hasMany('App\Models\ProductImage', 'product_id');
    }

    public function category()
    {
        return $this->belongsToMany(Package::class, 'package_activities');
        //return $this->hasMany('App\Models\Category', 'product_id');
    }
}

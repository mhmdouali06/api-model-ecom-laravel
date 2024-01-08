<?php

namespace App\Models;

use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class product extends Model
{
  
    use HasFactory,softDeletes;
    protected $fillable=[
        "name",
        "description",
        "image",
        "category_id",
        "slug"
    ];
    public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }
    public function images(){
        return $this->hasMany(ProductImage::class,'product_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopCategories extends Model
{
    use HasFactory;

    protected $table = "0product_categories";
    protected $primaryKey = "id_cp_shop";
    public $incrementing = true; 
    public $timestamps = true;

    protected $fillable = [
        'category_name',
    ];
}

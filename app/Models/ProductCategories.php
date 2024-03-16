<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategories extends Model
{
    use HasFactory;

    protected $table = "0product_categories";
    protected $primaryKey = "id_cp_prod";
    public $incrementing = true; 
    public $timestamps = true;

    protected $fillable = [
        'category_name',
    ];
}

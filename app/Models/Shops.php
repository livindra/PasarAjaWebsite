<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shops extends Model
{
    use HasFactory;

    protected $table = "0shops";
    protected $primaryKey = "id_shop";
    public $incrementing = true; 
    public $timestamps = true;

    protected $fillable = [
        'id_user',
        'phone_number',
        'shop_name',
        'description',
        'benchmark',
        'operational',
        'photo',
    ];
}

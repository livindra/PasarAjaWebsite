<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = "0events";
    protected $primaryKey = "id_report";
    public $incrementing = true; 
    public $timestamps = true;

    protected $fillable = [
        'id_user',
        'id_shop',
        'id_product',
        'reason',
    ];
}

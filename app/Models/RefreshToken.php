<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
    use HasFactory;

    protected $table = "0session";
    protected $primaryKey = "id_session";
    public $incrementing = true; 
    public $timestamps = true;

    protected $fillable = [
        'id_user',
        'device_token',
        'device',
        'number',
    ];
}

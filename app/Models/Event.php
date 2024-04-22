<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = "0events";
    protected $primaryKey = "id_event";
    public $incrementing = true; 
    public $timestamps = true;

    protected $fillable = [
        'id_user',
        'event_name',
        'description',
        'start_date',
        'end_date',
        'start_hour',
        'end_hour',
        'photo',
    ];
}

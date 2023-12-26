<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'timestamp',
        'ip',
        'method',
        'uri',
        'module',
        'channel_id',
        'partner_id',
        'external_id',
        'client_id',
        'request_header',
        'request_body',
        'response',
        'code',
    ];
}

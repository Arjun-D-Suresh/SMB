<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mailer extends Model
{
    use HasFactory;

    protected $filename = [
        'name',
        'email',
        'password',

    ];
}

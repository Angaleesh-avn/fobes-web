<?php

namespace Modules\Plan\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Plan\Database\factories\PriceplanFactory;

class PlanResdex extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'frontend_show' => 'boolean',
    ];
}

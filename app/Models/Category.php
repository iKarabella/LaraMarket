<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'title',
        'code',
        'description',
        'visibility',
        'parent',
        'sort'
    ];

    protected $casts = [
        'visibility' => 'boolean',
    ];
}

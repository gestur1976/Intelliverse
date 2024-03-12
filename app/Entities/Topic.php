<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Topic extends Entity
{
    protected $properties = [
        'id',
        'title',
        'slug',
        'created_at',
    ];

    protected $dates = [
        'created_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'slug' =>'string',
        'created_at' => 'datetime',
    ];

    protected $allowedFields = [
        'title',
        'slug',
        'created_at',
    ];
}

<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Fact extends Entity
{
    protected $properties = [
        'id',
        'fact',
        'source_slug',
        'target_slug',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'fact' => 'string',
        'source_slug' =>'string',
        'target_slug' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $allowedFields = [
        'fact',
        'user_id',
        'source_slug' =>'string',
        'target_slug' => 'string',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

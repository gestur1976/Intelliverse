<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Term extends Entity
{
    protected $properties = [
        'id',
        'term',
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
        'term' => 'string',
        'source_slug' =>'string',
        'target_slug' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $allowedFields = [
        'term',
        'source_slug' =>'string',
        'target_slug' => 'string',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

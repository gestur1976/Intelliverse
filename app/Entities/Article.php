<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Article extends Entity
{
    protected $properties = [
        'id',
        'title',
        'content_paragraphs',
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
        'title' => 'string',
        'content_paragraphs' => 'json-array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $allowedFields = [
        'title',
        'content_paragraphs',
        'source_slug',
        'target_slug',

    ];
}

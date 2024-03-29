<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Article extends Entity
{
    protected $properties = [
        'id',
        'user_id',
        'title',
        'source_slug',
        'target_slug',
        'views',
        'content_paragraphs',
        'topic_id',
        'generated',
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
        'user_id' => 'integer',
        'title' => 'string',
        'source_slug' =>'string',
        'target_slug' =>'string',
        'views' => 'integer',
        'content_paragraphs' => 'json-array',
        'topic_id' => 'integer',
        'generated' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $allowedFields = [
        'title',
        'user_id',
        'title',
        'source_slug',
        'target_slug',
        'content_paragraphs',
        'views',
        'content_paragraphs',
        'topic_id',
        'generated',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

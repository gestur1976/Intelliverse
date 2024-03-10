<?php

namespace App\Models;

use CodeIgniter\Model;
class ArticleModel extends Model
{
    protected $table         = 'articles';

    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title', 'source_slug', 'target_slug', 'content_paragraphs',
    ];
    protected $returnType    = \App\Entities\Article::class;
    protected $useTimestamps = true;
}
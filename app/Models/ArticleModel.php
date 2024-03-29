<?php

namespace App\Models;

use CodeIgniter\Model;
class ArticleModel extends Model
{
    protected $table         = 'articles';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;

    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'title', 'user_id', 'source_slug', 'target_slug', 'views', 'content_paragraphs', 'topic_id', 'generated', 'created_at', 'updated_at', 'deleted_at'
    ];

    protected $useTimestamps = true;

    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $returnType    = \App\Entities\Article::class;

}

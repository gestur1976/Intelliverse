<?php

namespace App\Models;

use CodeIgniter\Model;
class TopicModel extends Model
{
    protected $table         = 'topics';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;

    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'title', 'slug'
    ];

    protected $useTimestamps = true;

    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $returnType    = \App\Entities\Topic::class;

    public function getTopicsArray(): array
    {
        $topics = $this->findAll();
        $topicsArray = array_map(function($topic) {
            return [
                "title" => $topic->title,
                "slug" =>  $topic->slug,
            ];
        }, $topics);

        return $topicsArray;
    }

}

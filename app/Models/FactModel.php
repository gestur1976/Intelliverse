<?php

namespace App\Models;

use CodeIgniter\Model;
class FactModel extends Model
{
    protected $table         = 'facts';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;

    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'fact', 'user_id', 'article_slug', 'created_at', 'updated_at', 'deleted_at',
    ];

    protected $useTimestamps = true;

    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $returnType    = \App\Entities\Fact::class;

}

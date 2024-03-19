<?php

namespace App\Models;

use CodeIgniter\Model;
class TermModel extends Model
{
    protected $table         = 'terms';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;

    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'term', 'source_slug', 'target_slug', 'created_at', 'updated_at', 'deleted_at',
    ];

    protected $useTimestamps = true;

    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $returnType    = \App\Entities\Term::class;

}

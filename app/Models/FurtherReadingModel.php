<?php

namespace App\Models;

use CodeIgniter\Model;
class FurtherReadingModel extends Model
{
    protected $table         = 'further_readings';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;

    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'further_reading', 'source_slug', 'target_slug', 'created_at', 'updated_at', 'deleted_at',
    ];

    protected $useTimestamps = true;

    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $returnType    = \App\Entities\FurtherReading::class;

}

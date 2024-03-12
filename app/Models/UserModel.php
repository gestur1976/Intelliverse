<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'username', 'name', 'email', 'password', 'role', 'picture', 'bio'
    ];
    protected $useTimestamps = true;

    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $returnType = \App\Entities\User::class;

}

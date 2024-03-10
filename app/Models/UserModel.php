<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['username', 'name', 'email', 'password', 'role', 'picture', 'bio'];

    protected $returnType = \App\Entities\User::class;

    protected $useTimestamps = true;
}

<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'josep@gestur.org',
            'password' => '$2y$10$aVHNUFrTGM84k24faJC8ueW1uRvUDjt3yjP6uu71SW89gYsuPjRc6',
        ];

        $this->db->table('users')->insert($data);
    }
}

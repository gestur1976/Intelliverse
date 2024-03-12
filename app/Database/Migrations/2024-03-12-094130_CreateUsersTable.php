<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        {
            $this->forge->addField([
                'id' => [
                    'type' => 'int',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true
                ],
                'username' => [
                    'type' => 'VARCHAR',
                    'constraint' => '50',
                ],
                'name' => [
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                ],
                'password' => [
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                ],
                'email' => [
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'unique' => TRUE,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
                'created_at timestamp default current_timestamp',
                'updated_at timestamp default current_timestamp on update current_timestamp',
            ]);
            $attributes = [
                'ENGINE' => 'InnoDB',
                'CHARACTER SET' => 'utf8mb4',
                'COLLATE' => 'utf8mb4_unicode_ci',
            ];

            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('username');
            $this->forge->createTable('users', true, $attributes);
        }
    }

    public function down() {
        $this->forge->dropTable('users');
    }}

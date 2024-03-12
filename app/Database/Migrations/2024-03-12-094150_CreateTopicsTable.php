<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTopicsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at datetime default current_timestamp',
        ]);

        $attributes = [
            'ENGINE' => 'InnoDB',
            'CHARACTER SET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ];

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('topics', true, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('topics');
    }
}

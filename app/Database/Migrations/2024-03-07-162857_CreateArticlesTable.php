<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateArticlesTable extends Migration
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
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'slug' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'content' => [
                'type' => 'text',
            ],
            'created_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'created_at timestamp default CURRENT_TIMESTAMP',
            'updated_at timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'
        ]);

        $attributes = [
            'ENGINE' => 'InnoDB',
            'CHARACTER SET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ];

        $this->forge->addKey('id', true);
        $this->forge->createTable('articles', true, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('articles');
    }
}

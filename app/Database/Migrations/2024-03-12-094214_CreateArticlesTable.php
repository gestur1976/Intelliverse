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
            'user_id' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 1,
            ],
            'title' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'source_slug' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'target_slug' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'views' => [
                'type' => 'int',
                'constraint' => 11,
                'default' => 0,
                'unsigned' => true,
            ],
            'content_paragraphs' => [
                'type' => 'json',
            ],
            'topic_id' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'default' => null,
            ],
            'created_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'deleted_at' => [
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
        $this->forge->addUniqueKey('source_slug');
        $this->forge->addUniqueKey('target_slug');
        $this->forge->createTable('articles', true, $attributes);
        $this->forge->addForeignKey('user_id', 'users', 'id');
        $this->forge->addForeignKey('topic_id', 'topics', 'id');
        $this->forge->processIndexes('articles');
    }

    public function down()
    {
        $this->forge->dropTable('articles');
    }
}

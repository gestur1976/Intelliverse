<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTermsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'term'        => ['type' => 'varchar', 'constraint' => 255],
            'description' => ['type' => 'varchar', 'constraint' => 255],
            'article_id'  => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'created_at'  => ['type' => 'datetime'],
            'updated_at'  => ['type' => 'datetime'],
            'created_at timestamp default CURRENT_TIMESTAMP',
            'updated_at timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'
        ]);

        $attributes = [
            'ENGINE' => 'InnoDB',
            'CHARACTER SET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ];

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('term');
        $this->forge->createTable('terms', true, $attributes);
        $this->forge->addForeignKey('article_id', 'articles', 'id');
        $this->forge->processIndexes('terms');
    }

    public function down()
    {
        $this->forge->dropTable('terms');
    }
}

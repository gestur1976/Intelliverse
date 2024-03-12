<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFurtherReadingsTable extends Migration
{
    public function up()
    {
        {
            $this->forge->addField([
                'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'further_reading' => ['type' => 'varchar', 'constraint' => 255],
                'article_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
                'created_at' => ['type' => 'datetime'],
                'updated_at' => ['type' => 'datetime'],
                'created_at timestamp default CURRENT_TIMESTAMP',
                'updated_at timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'
            ]);

            $attributes = [
                'ENGINE' => 'InnoDB',
                'CHARACTER SET' => 'utf8mb4',
                'COLLATE' => 'utf8mb4_unicode_ci',
            ];

            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('article_id', 'articles', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('further_readings', true, $attributes);
            $this->forge->processIndexes('further_readings');
        }
    }

    public function down()
    {
        $this->forge->dropTable('further_readings');
    }
}
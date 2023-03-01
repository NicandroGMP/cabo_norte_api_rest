<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Works extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_work' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'work' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false
            ],
            'batch' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
                'unique' => true
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false
            ],
            'update_at' => [
                'type' => 'datetime',
                'null' => true
            ],
            'created_at datetime default current_timestamp',
        ]);
        $this->forge->addPrimaryKey('id_work');
        $this->forge->createTable('works');
    }

    public function down()
    {
        $this->forge->dropTable('works');
    }
}

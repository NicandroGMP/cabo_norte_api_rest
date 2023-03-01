<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Managers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_manager' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false
            ],
            'lastname' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false
            ],
            'company' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false
            ],
            'position' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
                'unique' => true
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false
            ],
            'worker' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ],
            'update_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'created_at datetime default current_timestamp',
        ]);
        $this->forge->addPrimaryKey('id_manager');
        $this->forge->createTable('managers');
        $this->forge->addForeignKey('worker', 'works', 'id_work');
    }

    public function down()
    {
        $this->forge->dropTable('managers');
    }
}

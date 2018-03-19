<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_standups extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'project_id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
            ),
            'standup' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field("`end` TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE');
        $this->dbforge->create_table('standups');
    }

    public function down()
    {
        $this->dbforge->drop_table('standups');
    }
}
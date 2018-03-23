<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_projects extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'project' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('projects');
    }

    public function down()
    {
        $this->dbforge->drop_table('projects');
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_members extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'firstname' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'lastname' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('members');
    }

    public function down()
    {
        $this->dbforge->drop_table('members');
    }
}
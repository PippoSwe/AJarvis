<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_entities extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'standup_id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
            ),
            'salience' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,8',
                'unsigned' => TRUE,
                'default' => '0.0',
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (standup_id) REFERENCES standups(id) ON DELETE CASCADE');
        $this->dbforge->create_table('entities');
    }

    public function down()
    {
        $this->dbforge->drop_table('entities');
    }
}
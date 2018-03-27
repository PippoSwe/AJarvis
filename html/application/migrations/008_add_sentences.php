<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_sentences extends CI_Migration {

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
            'sentence' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'score' => array(
                'type' => 'DECIMAL',
                'constraint' => '3,2',
                'unsigned' => FALSE,
                'null' => TRUE,
            ),
            'magnitude' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (standup_id) REFERENCES standups(id) ON DELETE CASCADE');
        $this->dbforge->create_table('sentences');
    }

    public function down()
    {
        $this->dbforge->drop_table('sentences');
    }
}
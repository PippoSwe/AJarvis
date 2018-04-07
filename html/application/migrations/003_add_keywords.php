<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_keywords extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'keyword' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('keywords');
        $this->db->query('ALTER TABLE `keywords` ADD UNIQUE INDEX (`keyword`)');
    }

    public function down()
    {
        $this->dbforge->drop_table('keywords');
    }
}
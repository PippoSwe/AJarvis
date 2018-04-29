<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_configs extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field(array(
            'key' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'value' => array(
                'type' => 'TEXT',
            ),
        ));
        $this->dbforge->add_key('key', TRUE);
        $this->dbforge->create_table('configs');
        $this->db->query("INSERT INTO `configs` (`key`, `value`) VALUES('service_type', 'remote')");
    }

    public function down()
    {
        $this->dbforge->drop_table('configs');
    }
}
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

        $data = array(
            array('key' => 'audio_bucket_name', 'value' => 'ajarvis-rest'),
            array('key' => 'key_file', 'value' => ''),
            array('key' => 'ip', 'value' => ''),
            array('key' => 'port ', 'value' => ''),
        );

        foreach($data as $insertData)
            $this->db->insert('configs', $insertData);
    }

    public function down()
    {
        $this->dbforge->drop_table('configs');
    }
}
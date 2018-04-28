<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_nlp extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
            ),
            'status' => array(
                'type' => 'ENUM("Pending","Success","Failed")',
                'default' => 'Pending',
                'null' => FALSE,
            ),
            'logs' => array(
                'type' => 'TEXT',
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (id) REFERENCES standups(id) ON DELETE CASCADE');
        $this->dbforge->create_table('standups_nlp');
        $this->db->query("CREATE TRIGGER `insert_nlp` AFTER INSERT ON `standups_speech_to_text`\r\n
FOR EACH ROW BEGIN\r\n 
INSERT INTO `standups_nlp` (`id`) VALUES(NEW.id);\r\n
END\r\n");
    }

    public function down()
    {
        $this->dbforge->drop_table('standups_nlp');
        $this->db->query("DROP TRIGGER `insert_nlp`");
    }
}
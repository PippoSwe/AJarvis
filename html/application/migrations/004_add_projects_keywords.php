<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_projects_keywords extends CI_Migration {

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
            'keyword_id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (keyword_id) REFERENCES keywords(id) ON DELETE CASCADE');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE');
        $this->dbforge->add_key(array('keyword_id', 'project_id'));
        $this->dbforge->create_table('projects_keywords');
        $this->db->query('ALTER TABLE `projects_keywords` ADD UNIQUE INDEX (`keyword_id`,`project_id`)');
    }

    public function down()
    {
        $this->dbforge->drop_table('projects_keywords');
    }
}
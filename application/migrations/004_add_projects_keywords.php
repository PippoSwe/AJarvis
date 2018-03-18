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
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('projects_keywords');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (keyword_id) REFERENCES keywords(id) ON DELETE CASCADE');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (project_id) REFERENCES project(id) ON DELETE CASCADE');
        $this->dbforge->add_key(array('keyword_id', 'project_id'));
    }

    public function down()
    {
        $this->dbforge->drop_table('projects_keywords');
    }
}
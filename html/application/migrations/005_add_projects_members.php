<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_projects_members extends CI_Migration {

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
            'member_id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE');
        $this->dbforge->add_key(array('project_id', 'member_id'));
        $this->dbforge->create_table('projects_members');
    }

    public function down()
    {
        $this->dbforge->drop_table('projects_members');
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectMember_model extends CI_Model {

    public $id;
    public $project_id;
    public $member_id;

    function __construct()
    {
        // https://www.codeigniter.com/user_guide/database/forge.html#adding-keys
        parent::__construct();
    }

    public function find($project_id = null, $member_id = null, $limit = null, $offset = 0)
    {
        //https://www.codeigniter.com/userguide3/database/query_builder.html#looking-for-similar-data
        $collection = $this->db->from('projects_members');
        $collection = $collection->join('projects', 'projects.id = project_id', 'left');
        $collection = $collection->join('members', 'members.id = member_id', 'left');
        if (!is_null($project_id))
            $collection = $collection->where('project_id', $project_id);
        if (!is_null($member_id))
            $collection = $collection->where('member_id', $member_id);
        if (!is_null($limit))
            $collection = $collection->limit($limit, $offset);
        $result = $collection
            ->get()
            ->result();
        return $result;
    }

    public function get($id)
    {
        $records = $this->db->from('projects_members')
            ->join('projects', 'projects.id = project_id', 'left')
            ->join('members', 'members.id = member_id', 'left')
            ->where('projects_members.id', $id)->get()
            ->result();
        if(sizeof($records) > 0)
            return $records[0];
        return null;
    }

    public function insert($data)
    {
        $this->db->set($data);
        $this->db->insert('projects_members');
        return $this->get($this->db->insert_id());
    }

    public function delete($project_id, $member_id)
    {
        $this->db->where(array(
            'project_id' => $project_id,
            'member_id' => $member_id));
        $this->db->delete('projects_members');
        return TRUE;
    }

}
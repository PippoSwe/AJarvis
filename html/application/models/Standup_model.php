<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Standup_model extends CI_Model {

    public $id;
    public $project_id;
    public $standup;
    public $score;
    public $magnitude;
    public $end;

    function __construct()
    {
        // https://www.codeigniter.com/user_guide/database/forge.html#adding-keys
        parent::__construct();
    }

    public function find($project_id = null, $limit = null, $offset = 0)
    {
        //https://www.codeigniter.com/userguide3/database/query_builder.html#looking-for-similar-data
        $collection = $this->db->select('standups.id, project_id, project, standup, score, magnitude, end')
            ->from('standups');
        $collection = $collection->join('projects', 'projects.id = project_id', 'left');
        if (!is_null($project_id))
            $collection = $collection->where('project_id', $project_id);
        if (!is_null($limit))
            $collection = $collection->limit($limit, $offset);
        $result = $collection
            ->get()
            ->result();
        return $result;
    }

    public function get($id)
    {
        $records = $this->db->select('standups.id, project_id, project, standup, score, magnitude, end')
            ->from('standups')
            ->join('projects', 'projects.id = project_id', 'left')
            ->where('standups.id', $id)->get()
            ->result();
        if(sizeof($records) > 0)
            return $records[0];
        return null;
    }

    public function insert($data)
    {
        $this->db->set($data);
        $this->db->insert('standups');
        return $this->get($this->db->insert_id());
    }

    public function update($id, $data)
    {
        $this->db->set($data);
        $this->db->where('id', $id);
        $this->db->update('standups');
        return $this->get($id);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('standups');
        return TRUE;
    }

}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_model extends CI_Model {

    public $id;
    public $name;

    function __construct()
    {
        // https://www.codeigniter.com/user_guide/database/forge.html#adding-keys
        parent::__construct();
    }

    public function find($limit = null, $offset = 0)
    {
        //https://www.codeigniter.com/userguide3/database/query_builder.html#looking-for-similar-data
        $collection = $this->db->from('projects');
        if (!is_null($limit))
            $collection = $collection->limit($limit, $offset);
        $result = $collection
            ->get()
            ->result();
        return $result;
    }

    public function flow($project_id = null, $limit = null, $offset = 0)
    {
        //https://www.codeigniter.com/userguide3/database/query_builder.html#looking-for-similar-data
        $collection = $this->db->select('standups.score')
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

    public function sentences($project_id = null, $type ='all' , $limit = null, $offset = 0)
    {
        $collection = $this->db->select('sentence, sentences.score, sentences.magnitude')
            ->from('sentences');
        $collection = $collection->join('standups', 'standups.id = standup_id', 'left');
        $collection = $collection->join('projects', 'projects.id = project_id', 'left');

        if (!is_null($project_id))
            $collection = $collection->where('project_id', $project_id);

        switch ($type) {
            case 'positive':
                $collection = $collection->where('sentences.score > 0.25');
                $collection = $collection->order_by('sentences.score', 'DESC');
                break;
            case 'negative':
                $collection = $collection->where('sentences.score < -0.25');
                $collection = $collection->order_by('sentences.score', 'ASC');
                break;
        }

        if (!is_null($limit))
            $collection = $collection->limit($limit, $offset);
        $result = $collection
            ->get()
            ->result();
        return $result;
    }

    public function get($id)
    {
        $records = $this->db->from('projects')
            ->where('id', $id)->get()
            ->result();
        if(sizeof($records) > 0)
            return $records[0];
        return null;
    }

    public function insert($data)
    {
        $this->db->set($data);
        $this->db->insert('projects');
        return $this->get($this->db->insert_id());
    }

    public function update($id, $data)
    {
        $this->db->set($data);
        $this->db->where('id', $id);
        $this->db->update('projects');
        return $this->get($id);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('projects');
        return TRUE;
    }

}
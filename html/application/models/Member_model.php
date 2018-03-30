<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member_model extends CI_Model {

    public $id;
    public $firstname;
    public $lastname;

    function __construct()
    {
        // https://www.codeigniter.com/user_guide/database/forge.html#adding-keys
        parent::__construct();
    }

    public function find($limit = null, $offset = 0, $searchParam = null)
    {
        //https://www.codeigniter.com/userguide3/database/query_builder.html#looking-for-similar-data
        $collection = $this->db->from('members');
        if (!is_null($limit))
            $collection = $collection->limit($limit, $offset);
        if (!is_null($searchParam)){
            $collection = $collection->like('CONCAT(firstname,\' \',lastname)', $searchParam, 'after');
            $collection = $collection->or_like('CONCAT(lastname,\' \',firstname)', $searchParam, 'after');
        }
        $result = $collection
            ->get()
            ->result();
        return $result;
    }

    public function get($id)
    {
        $records = $this->db->from('members')
            ->where('id', $id)->get()
            ->result();
        if(sizeof($records) > 0)
            return $records[0];
        return null;
    }

    public function insert($data)
    {
        $this->db->set($data);
        $this->db->insert('members');
        return $this->get($this->db->insert_id());
    }

    public function update($id, $data)
    {
        $this->db->set($data);
        $this->db->where('id', $id);
        $this->db->update('members');
        return $this->get($id);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('members');
        return TRUE;
    }

}
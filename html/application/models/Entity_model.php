<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Entity_model extends CI_Model {

    public $id;
    public $standup_id;
    public $name;
    public $type;
    public $salience;

    function __construct()
    {
        // https://www.codeigniter.com/user_guide/database/forge.html#adding-keys
        parent::__construct();
    }

    public function find($standup_id = null, $limit = null, $offset = 0)
    {
        //https://www.codeigniter.com/userguide3/database/query_builder.html#looking-for-similar-data
        $collection = $this->db->select('entities.id, standup_id, name, type, salience')
            ->from('entities');
        $collection = $collection->join('standups', 'standups.id = standup_id', 'left');
        if (!is_null($standup_id))
            $collection = $collection->where('standup_id', $standup_id);
        if (!is_null($limit))
            $collection = $collection->limit($limit, $offset);
        $result = $collection
            ->get()
            ->result();
        return $result;
    }

    public function get($id)
    {
        $records = $this->db->select('entities.id, standup_id, standups.standup, name, type, salience')
            ->from('entities')
            ->join('standups', 'standups.id = standup_id', 'left')
            ->where('entities.id', $id)->get()
            ->result();
        if(sizeof($records) > 0)
            return $records[0];
        return null;
    }

    public function insert($data)
    {
        $this->db->set($data);
        $this->db->insert('entities');
        return $this->get($this->db->insert_id());
    }

    /*
    public function update($id, $data)
    {
        $this->db->set($data);
        $this->db->where('id', $id);
        $this->db->update('entities');
        return $this->get($id);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('entities');
        return TRUE;
    }
    */

}
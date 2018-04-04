<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config_model extends CI_Model
{

    public $key;
    public $value;

    function __construct()
    {
        parent::__construct();
    }

    public function get($key)
    {
        $records = $this->db
            ->from('configs')
            ->where('key', $key)->get()
            ->result();
        if (sizeof($records) > 0)
            return $records[0];
        return null;
    }

    public function readAll(){
        $collection = $this->db->select('*')
            ->from('configs');
        $result = $collection
            ->get()
            ->result();
        return $result;
    }

    public function update($data)
    {
        $this->load->database();
       // $this->db->where('key', $data->key);
       // $this->db->update('configs', $data);
        $this->db->replace('configs', $data);
    }
}
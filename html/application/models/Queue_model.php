<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Queue_model extends CI_Model
{

    public $id;
    public $project;
    public $standup;
    public $stt_status;
    public $nlp_status;

    function __construct()
    {
        // https://www.codeigniter.com/user_guide/database/forge.html#adding-keys
        parent::__construct();
    }

    public function find($limit = null, $offset = 0)
    {
        //https://www.codeigniter.com/userguide3/database/query_builder.html#looking-for-similar-data
        $collection = $this->db->select('standups.id, standup, project_id, project, 
            standups_nlp.status AS nlp_status, 
            CASE
                WHEN standups_nlp.status = \'Pending\' THEN \'warning\'
                WHEN standups_nlp.status = \'Success\' THEN \'success\'
                ELSE \'danger\'
            END as nlp_badge_color,        
            standups_speech_to_text.status AS stt_status,             
            CASE
                WHEN standups_speech_to_text.status = \'Pending\' THEN \'warning\'
                WHEN standups_speech_to_text.status = \'Success\' THEN \'success\'
                ELSE \'danger\'
            END as stt_badge_color,            
            end')
            ->from('standups');
        $collection = $collection->join('projects', 'projects.id = project_id', 'left');
        $collection = $collection->join('standups_nlp', 'standups_nlp.id = standups.id', 'left');
        $collection = $collection->join('standups_speech_to_text', 'standups_speech_to_text.id = standups.id', 'left');
        if (!is_null($limit))
            $collection = $collection->limit($limit, $offset);
        $result = $collection
            ->get()
            ->result();
        return $result;
    }

    public function countPending()
    {
        $collection = $this->db->select('COUNT(*) as result')->from('standups');
        $collection = $collection->join('projects', 'projects.id = project_id', 'left');
        $collection = $collection->join('standups_nlp', 'standups_nlp.id = standups.id', 'left');
        $collection = $collection->join('standups_speech_to_text', 'standups_speech_to_text.id = standups.id', 'left');
        $collection = $collection->where('standups_speech_to_text.status','Pending');
        $collection = $collection->or_where('standups_nlp.status','Pending');
        $result = $collection
            ->get()
            ->result();
        return $result;
    }

    public function get($id)
    {
        $records = $this->db->select('standups.id, standup, project_id, project, 
            standups_nlp.status AS nlp_status, 
            standups_speech_to_text.status AS stt_status, end')
            ->from('standups')
            ->join('projects', 'projects.id = project_id', 'left')
            ->join('standups_nlp', 'standups_nlp.id = standups.id', 'left')
            ->join('standups_speech_to_text', 'standups_speech_to_text.id = standups.id', 'left')
            ->where('standups.id', $id)->get()
            ->result();
        if (sizeof($records) > 0)
            return $records[0];
        return null;
    }

    public function update_nlp($id, $data)
    {
        $this->db->set($data);
        $this->db->where('id', $id);
        $this->db->update('standups_nlp');
        return $this->get($id);
    }

    public function update_stt($id, $data)
    {
        $this->db->set($data);
        $this->db->where('id', $id);
        $this->db->update('standups_speech_to_text');
        return $this->get($id);
    }

}
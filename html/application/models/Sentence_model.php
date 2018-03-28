<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sentence_model extends CI_Model {

    public $id;
    public $standup_id;
    public $sentence;
    public $score;
    public $magnitude;

    function __construct()
    {
        // https://www.codeigniter.com/user_guide/database/forge.html#adding-keys
        parent::__construct();
    }

    public function find($standup_id = null, $limit = null, $offset = 0)
    {
        //https://www.codeigniter.com/userguide3/database/query_builder.html#looking-for-similar-data
        $collection = $this->db->select('sentences.id, standup_id, sentence, sentences.score, sentences.magnitude')
            ->from('sentences');
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

    public function sentences($standup_id = null, $type ='all' , $limit = null, $offset = 0)
    {
        $collection = $this->db->select('sentence, sentences.score, sentences.magnitude')
            ->from('sentences');
        $collection = $collection->join('standups', 'standups.id = standup_id', 'left');
        if (!is_null($standup_id))
            $collection = $collection->where('standup_id', $standup_id);

        switch ($type) {
            case 'positive':
                $collection = $collection->where('sentences.score > 0.25');
                $collection = $collection->order_by('sentences.score', 'DESC');
                break;
            case 'negative':
                $collection = $collection->where('sentences.score < -0.25');
                $collection = $collection->order_by('sentences.score', 'ASC');
                break;
            default:
                break;
        }

        if (!is_null($limit))
            $collection = $collection->limit($limit, $offset);
        $result = $collection
            ->get()
            ->result();
        return $result;
    }

    public function countType($standup_id = null, $type ='positive' , $limit = null, $offset = 0)
    {
        $collection = $this->db->select('COUNT(*) as number')
            ->from('sentences');
        $collection = $collection->join('standups', 'standups.id = standup_id', 'left');
        if (!is_null($standup_id))
            $collection = $collection->where('standup_id', $standup_id);

        switch ($type) {
            case 'positive':
                $collection = $collection->where('sentences.score > 0.25');
                break;
            case 'negative':
                $collection = $collection->where('sentences.score < -0.25');
                break;
            case 'neutral':
                $collection = $collection->where('sentences.score BETWEEN -0.25 AND 0.25');
                $collection = $collection->where('sentences.magnitude','0');
                break;
            case 'mixed':
                $collection = $collection->where('sentences.score BETWEEN -0.25 AND 0.25');
                $collection = $collection->where('sentences.magnitude > 0');
                break;
        }

        if (!is_null($limit))
            $collection = $collection->limit($limit, $offset);
        $result = $collection
            ->get()
            ->result();
        return $result;
    }

    public function flow($standup_id = null, $limit = null, $offset = 0)
    {
        $collection = $this->db->select('sentences.score')
            ->from('sentences');
        $collection = $collection->join('standups', 'standups.id = standup_id', 'left');
        if (!is_null($standup_id))
            $collection = $collection->where('standup_id', $standup_id);
        if (!is_null($limit))
            $collection = $collection->limit($limit, $offset);
        $this->db->order_by('end', 'ASC');
        $result = $collection
            ->get()
            ->result();
        return $result;
    }

    public function get($id)
    {
        $records = $this->db->select('sentences.id, standup_id, standups.standup, sentence, sentences.score, sentences.magnitude')
            ->from('sentences')
            ->join('standups', 'standups.id = standup_id', 'left')
            ->where('sentences.id', $id)->get()
            ->result();
        if(sizeof($records) > 0)
            return $records[0];
        return null;
    }

    public function insert($data)
    {
        $this->db->set($data);
        $this->db->insert('sentences');
        return $this->get($this->db->insert_id());
    }

    /*
    public function update($id, $data)
    {
        $this->db->set($data);
        $this->db->where('id', $id);
        $this->db->update('sentences');
        return $this->get($id);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('sentences');
        return TRUE;
    }
    */

}

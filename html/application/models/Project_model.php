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
        $collection = $this->db->select('sentence, sentences.score, sentences.magnitude, standups.id AS standup_id')
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

    public function entities($project_id = null, $limit = null, $offset = 0)
    {
        $collection = $this->db->select('entities.name, entities.type, SUM(entities.salience) AS sum_salience,COUNT(entities.salience) AS count_salience ,  standups.id AS standup_id')
            ->from('entities');
        $collection = $collection->join('standups', 'standups.id = standup_id', 'left');
        $collection = $collection->join('projects', 'projects.id = project_id', 'left');

        if (!is_null($project_id))
            $collection = $collection->where('project_id', $project_id);

        $collection = $collection->group_by('entities.name');
        $collection = $collection->order_by('sum_salience', 'DESC');
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

    public function statistics($id) {
        $records = $this->db->query('SELECT 1 AS id, 
    "Numero frasi rilevate" AS name, 
    CAST(count(*) AS DECIMAL(10,2)) AS value 
FROM sentences 
WHERE standup_id IN (
    SELECT id 
    FROM standups 
    WHERE project_id = '.$id.'
)
UNION 
SELECT 2 AS id, 
    "Numero frasi positive" AS name, 
    CAST(count(*) AS DECIMAL(10,2)) AS value 
FROM sentences 
WHERE standup_id IN (
    SELECT id 
    FROM standups 
    WHERE project_id = '.$id.'
)
AND score > 0.25
UNION 
SELECT 3 AS id, 
    "Numero frasi negative" AS name, 
    CAST(count(*) AS DECIMAL(10,2)) AS value 
FROM sentences 
WHERE standup_id IN (
    SELECT id 
    FROM standups 
    WHERE project_id = '.$id.'
)
AND score < -0.25
UNION 
SELECT 4 AS id, 
    "Numero frasi neutre" AS name, 
    CAST(count(*) AS DECIMAL(10,2)) AS value 
FROM sentences 
WHERE standup_id IN (
    SELECT id 
    FROM standups 
    WHERE project_id = '.$id.'
)
AND score >= -0.25
AND score <= 0.25
UNION 
SELECT 5 AS id, 
    "Numero argomenti" AS name, 
    CAST(count(*) AS DECIMAL(10,2)) AS value 
FROM entities 
WHERE standup_id IN (
    SELECT id 
    FROM standups 
    WHERE project_id = '.$id.'
)
UNION 
SELECT 6 AS id, 
    "Andamento generale del progetto" AS name, 
    CASE WHEN AVG(score) IS NULL THEN 0.00 ELSE AVG(score) END AS value 
FROM (SELECT '.$id.' AS project_id) AS R
LEFT JOIN standups ON R.project_id = standups.project_id
WHERE R.project_id = '.$id.'
GROUP BY R.project_id
UNION 
SELECT 7 AS id, 
    "Affidabilità delle traduzioni" AS name, 
    CASE WHEN AVG(magnitude) IS NULL THEN 0.00 ELSE AVG(magnitude) END AS value 
FROM (SELECT '.$id.' AS project_id) AS R
LEFT JOIN standups ON R.project_id = standups.project_id
WHERE R.project_id = '.$id.'
GROUP BY R.project_id');
        return $records->result();
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
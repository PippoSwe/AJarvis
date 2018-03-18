<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends CI_Controller
{

    function __construct()
    {
        //http://www.restapitutorial.com/lessons/httpmethods.html
        parent::__construct();
        $this->load->model('project_model', 'projects', TRUE);
        $this->load->model('project_keyword_model', 'projects_keywords', TRUE);
    }

    public function index() {
        if($this->input->method(TRUE) == 'POST') {
            $this->insert();
            return;
        }
        // else: GET
        $this->find();
    }

    public function target($id) {
        if($this->input->method(TRUE) == 'PUT') {
            $this->update($id);
            return;
        }
        else if($this->input->method(TRUE) == 'DELETE') {
            $this->delete($id);
            return;
        }
        // else: GET
        $this->view($id);
    }

    private function find() {
        $entry = $this->projects->find(
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );
        $content = array (
            'json' => json_encode($entry)
        );
        $this->load->view('project/list',$content);
    }

    private function insert() {
        // Dichiariamo i valori di default
        $data = array(
            "name" => null
        );

        // Normalizzazione
        if(!empty($this->input->post('name')))
            $data["name"] = $this->input->post('name');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->projects->insert($data);
        if($entry == null)
            show_error("Cannot insert due to service malfunctioning", 500);

        $content = array (
            'json' => json_encode($entry)
        );

        $this->load->view('project/insert',$content);
    }

    private function view($id) {
        $entry = $this->projects->get($id);
        if($entry == null)
            show_404();
        $content = array (
            'json' => json_encode($entry)
        );
        $this->load->view('project/view',$content);
    }

    private function update($id) {
        // Dichiariamo i valori di default
        $data = array(
            "name" => null
        );

        // Normalizzazione
        if(!empty($this->input->input_stream('name')))
            $data["name"] = $this->input->input_stream('name');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->projects->update($id, $data);
        if($entry == null)
            show_error("Cannot update due to service malfunctioning", 500);

        $content = array (
            'json' => json_encode($entry)
        );

        $this->load->view('project/update',$content);
    }

    private function delete($id) {
        if(!$this->projects->delete($id))
            show_error("Cannot delete due to service malfunctioning", 500);
    }

    /* Keywords */
    public function index_keyword($project_id) {
        if($this->input->method(TRUE) == 'POST') {
            $this->keyword_insert($project_id);
            return;
        }
        // else: GET
        $this->keyword_find($project_id);
    }

    public function target_keyword($project_id, $keyword_id) {
        if($this->input->method(TRUE) == 'DELETE') {
            $this->keyword_delete($project_id, $keyword_id);
            return;
        }
        // else: GET
        $this->keyword_view($project_id, $keyword_id);
    }

    private function keyword_insert($project_id) {
        // Dichiariamo i valori di default
        $data = array(
            "project_id" => $project_id,
            "keyword_id" => null
        );

        // Normalizzazione
        if(!empty($this->input->post('keyword_id')))
            $data["keyword_id"] = $this->input->post('keyword_id');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->projects_keywords->insert($data);
        if($entry == null)
            show_error("Cannot insert due to service malfunctioning", 500);

        $content = array (
            'json' => json_encode($entry)
        );

        $this->load->view('project/keyword_insert',$content);
    }

    private function keyword_find($project_id) {
        $entry = $this->projects_keywords->find(
            $project_id,
            null,
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );
        $content = array (
            'json' => json_encode($entry)
        );
        $this->load->view('project/keyword_list',$content);
    }

    private function keyword_view($project_id, $keyword_id) {
        $records = $this->projects_keywords->find(
            $project_id,
            $keyword_id,
            $limit = 1,
            $offset = 0
        );
        $entry = null;
        if(sizeof($records) > 0)
            $entry = $records[0];
        if($entry == null)
            show_404();
        $content = array (
            'json' => json_encode($entry)
        );
        $this->load->view('project/keyword_view',$content);
    }


    private function keyword_delete($project_id, $keyword_id) {
        if(!$this->projects_keywords->delete($project_id, $keyword_id))
            show_error("Cannot delete due to service malfunctioning", 500);
    }


}
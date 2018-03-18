<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectStandup extends CI_Controller
{

    function __construct()
    {
        //http://www.restapitutorial.com/lessons/httpmethods.html
        parent::__construct();
        $this->load->model('Standup_model', 'standups', TRUE);
    }

    /* Keywords */
    public function index($project_id) {
        if($this->input->method(TRUE) == 'POST') {
            $this->insert($project_id);
            return;
        }
        // else: GET
        $this->find($project_id);
    }

    public function target($project_id, $id) {
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

    private function insert($project_id) {
        // Dichiariamo i valori di default
        $data = array(
            "project_id" => $project_id,
            "standup" => null
        );

        // Normalizzazione
        if(!empty($this->input->post('standup')))
            $data["standup"] = $this->input->post('standup');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->standups->insert($data);
        if($entry == null)
            show_error("Cannot insert due to service malfunctioning", 500);

        $content = array (
            'json' => json_encode($entry)
        );

        $this->load->view('standup/insert',$content);
    }

    private function update($id) {
        // Dichiariamo i valori di default
        $data = array(
            "standup" => null
        );

        // Normalizzazione
        if(!empty($this->input->input_stream('standup')))
            $data["standup"] = $this->input->input_stream('standup');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->projects->update($id, $data);
        if($entry == null)
            show_error("Cannot update due to service malfunctioning", 500);

        $content = array (
            'json' => json_encode($entry)
        );

        $this->load->view('standup/update',$content);
    }

    private function find($project_id) {
        $entry = $this->standups->find(
            $project_id,
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );
        $content = array (
            'json' => json_encode($entry)
        );
        $this->load->view('standup/list',$content);
    }

    private function view($id) {
        $entry = $this->standups->get($id);
        if($entry == null)
            show_404();
        $content = array (
            'json' => json_encode($entry)
        );
        $this->load->view('standup/view',$content);
    }


    private function delete($id) {
        if(!$this->standups->delete($id))
            show_error("Cannot delete due to service malfunctioning", 500);
    }


}
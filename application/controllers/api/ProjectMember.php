<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectMember extends CI_Controller
{

    function __construct()
    {
        //http://www.restapitutorial.com/lessons/httpmethods.html
        parent::__construct();
        $this->load->model('ProjectMember_model', 'projects_members', TRUE);
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

    public function target($project_id, $member_id) {
        if($this->input->method(TRUE) == 'DELETE') {
            $this->delete($project_id, $member_id);
            return;
        }
        // else: GET
        $this->view($project_id, $member_id);
    }

    private function insert($project_id) {
        // Dichiariamo i valori di default
        $data = array(
            "project_id" => $project_id,
            "member_id" => null
        );

        // Normalizzazione
        if(!empty($this->input->post('member_id')))
            $data["member_id"] = $this->input->post('member_id');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->projects_members->insert($data);
        if($entry == null)
            show_error("Cannot insert due to service malfunctioning", 500);

        $content = array (
            'json' => json_encode($entry)
        );

        $this->load->view('project_member/insert',$content);
    }

    private function find($project_id) {
        $entry = $this->projects_members->find(
            $project_id,
            null,
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );
        $content = array (
            'json' => json_encode($entry)
        );
        $this->load->view('project_member/list',$content);
    }

    private function view($project_id, $member_id) {
        $records = $this->projects_members->find(
            $project_id,
            $member_id,
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
        $this->load->view('project_member/view',$content);
    }


    private function delete($project_id, $member_id) {
        if(!$this->projects_members->delete($project_id, $member_id))
            show_error("Cannot delete due to service malfunctioning", 500);
    }


}
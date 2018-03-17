<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends CI_Controller
{

    function __construct()
    {
        //http://www.restapitutorial.com/lessons/httpmethods.html
        parent::__construct();
        $this->load->model('member_model', 'members', TRUE);
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
        $entry = $this->members->find(
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );
        $content = array (
            'json' => json_encode($entry)
        );
        $this->load->view('member/list',$content);
    }

    private function insert() {
        // Dichiariamo i valori di default
        $data = array(
            "firstname" => null,
            "lastname" => null
        );

        // Normalizzazione
        if(!empty($this->input->post('firstname')))
            $data["firstname"] = $this->input->post('firstname');
        if(!empty($this->input->post('lastname')))
            $data["lastname"] = $this->input->post('lastname');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->members->insert($data);
        if($entry == null)
            show_error("Cannot insert due to service malfunctioning", 500);

        $content = array (
            'json' => json_encode($entry)
        );

        $this->load->view('member/insert',$content);
    }

    private function view($id) {
        $entry = $this->members->get($id);
        if($entry == null)
            show_404();
        $content = array (
            'json' => json_encode($entry)
        );
        $this->load->view('member/view',$content);
    }

    private function update($id) {
        // Dichiariamo i valori di default
        $data = array(
            "firstname" => null,
            "lastname" => null
        );

        // Normalizzazione
        if(!empty($this->input->input_stream('firstname')))
            $data["firstname"] = $this->input->input_stream('firstname');
        if(!empty($this->input->input_stream('lastname')))
            $data["lastname"] = $this->input->input_stream('lastname');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->members->update($id, $data);
        if($entry == null)
            show_error("Cannot update due to service malfunctioning", 500);

        $content = array (
            'json' => json_encode($entry)
        );

        $this->load->view('member/update',$content);
    }

    private function delete($id) {
        if(!$this->members->delete($id))
            show_error("Cannot delete due to service malfunctioning", 500);
    }

}
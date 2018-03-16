<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('member_model', 'members', TRUE);
    }

    public function index() {
        $entry = $this->members->find();
        $content = array (
            'json' => json_encode($entry)
        );
        $this->load->view('member/list',$content);
    }

    public function view($id) {
        $entry = $this->members->get($id);
        if($entry == null)
            show_404();
        $content = array (
            'json' => json_encode($entry)
        );
        $this->load->view('member/view',$content);
    }

    public function insert() {
        if($this->input->method(TRUE) != 'POST')
            show_error("POST Request needed", 400);

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

    public function update($id) {
        if($this->input->method(TRUE) != 'PUT')
            show_error("PUT Request needed", 400);

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

}
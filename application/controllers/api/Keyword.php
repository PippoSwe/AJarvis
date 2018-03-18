<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Keyword extends CI_Controller
{

    function __construct()
    {
        //http://www.restapitutorial.com/lessons/httpmethods.html
        parent::__construct();
        $this->load->model('Keyword_model', 'keywords', TRUE);
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
        $entry = $this->keywords->find(
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );
        $content = array (
            'json' => json_encode($entry)
        );
        $this->load->view('keyword/list',$content);
    }

    private function insert() {
        // Dichiariamo i valori di default
        $data = array(
            "keyword" => null
        );

        // Normalizzazione
        if(!empty($this->input->post('keyword')))
            $data["keyword"] = $this->input->post('keyword');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->keywords->insert($data);
        if($entry == null)
            show_error("Cannot insert due to service malfunctioning", 500);

        $content = array (
            'json' => json_encode($entry)
        );

        $this->load->view('keyword/insert',$content);
    }

    private function view($id) {
        $entry = $this->keywords->get($id);
        if($entry == null)
            show_404();
        $content = array (
            'json' => json_encode($entry)
        );
        $this->load->view('keyword/view',$content);
    }

    private function update($id) {
        // Dichiariamo i valori di default
        $data = array(
            "keyword" => null
        );

        // Normalizzazione
        if(!empty($this->input->input_stream('keyword')))
            $data["keyword"] = $this->input->input_stream('keyword');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->keywords->update($id, $data);
        if($entry == null)
            show_error("Cannot update due to service malfunctioning", 500);

        $content = array (
            'json' => json_encode($entry)
        );

        $this->load->view('keyword/update',$content);
    }

    private function delete($id) {
        if(!$this->keywords->delete($id))
            show_error("Cannot delete due to service malfunctioning", 500);
    }

}
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

    /**
     * @SWG\Get(
     *     path="keyword/",
     *     summary="Elenco keyword",
     *     description="Elenco delle keyword disponibili",
     *     produces={"application/json"},
     *     tags={"keyword"},
     *     @SWG\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Recupera {limit} elementi",
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Inizio paginazione",
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error"
     *     )
     * )
     */
    private function find() {
        $entry = $this->keywords->find(
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Post(
     *     path="keyword/",
     *     summary="Aggiungi keyword",
     *     description="Aggiungi una nuova keyword",
     *     produces={"application/json"},
     *     tags={"keyword"},
     *     @SWG\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Keyword",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error"
     *     )
     * )
     */
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

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Get(
     *     path="keyword/{keyword_id}/",
     *     summary="Visualizza keyword",
     *     description="Visualizza tutti i dettagli di una keyword",
     *     produces={"application/json"},
     *     tags={"keyword"},
     *     @SWG\Parameter(
     *         name="keyword_id",
     *         in="path",
     *         description="Keyword id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Page Not Found"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error"
     *     )
     * )
     */
    private function view($id) {
        $entry = $this->keywords->get($id);
        if($entry == null)
            show_404();
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Put(
     *     path="keyword/{keyword_id}/",
     *     summary="Aggiorna keyword",
     *     description="Aggiorna gli attributi della keyword",
     *     produces={"application/json"},
     *     tags={"keyword"},
     *     @SWG\Parameter(
     *         name="keyword_id",
     *         in="path",
     *         description="Keyword id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Nome progetto",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error"
     *     )
     * )
     */
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

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Delete(
     *     path="keyword/{keyword_id}/",
     *     summary="Cancella keyword",
     *     description="Cancella la keyword",
     *     produces={"application/json"},
     *     tags={"keyword"},
     *     @SWG\Parameter(
     *         name="keyword_id",
     *         in="path",
     *         description="Keyword id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error"
     *     )
     * )
     */
    private function delete($id) {
        if(!$this->keywords->delete($id))
            show_error("Cannot delete due to service malfunctioning", 500);
    }

}
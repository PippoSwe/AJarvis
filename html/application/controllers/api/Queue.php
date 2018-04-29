<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once  FCPATH . '/application/core/MY_Standup.php';
class Queue extends MY_Standup
{

    function __construct()
    {
        //http://www.restapitutorial.com/lessons/httpmethods.html
        parent::__construct();
    }

    public function index() {
        // else: GET
        $this->find();
    }

    public function target($id) {
        // else: GET
        $this->view($id);
    }

    /**
     * @SWG\Get(
     *     path="queue/",
     *     summary="Elenco processi Speech to Text, Text Analysys in lavorazione",
     *     description="Elenca tutti processi di Speech to Text e Text Analysys in lavorazione",
     *     produces={"application/json"},
     *     tags={"queue"},
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
        $entry = $this->queues->find(
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset'),
            $onlyPending = $this->input->get('pending')
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Get(
     *     path="queue/{standup_id}/",
     *     summary="Visualizza informazioni sulla conversione di uno standup",
     *     description="Visualizza informazioni sulla conversione di uno standup",
     *     produces={"application/json"},
     *     tags={"queue"},
     *     @SWG\Parameter(
     *         name="standup_id",
     *         in="path",
     *         description="Standup id",
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
        $entry = $this->queues->get($id);
        if($entry == null)
            show_404();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Put(
     *     path="queue/{standup_id}/nlp/",
     *     summary="Aggiorna lo stato di lavorazione del Natural Language Processing",
     *     description="Aggiorna lo stato di lavorazione del Natural Language Processing",
     *     produces={"application/json"},
     *     tags={"queue"},
     *     @SWG\Parameter(
     *         name="standup_id",
     *         in="path",
     *         description="Standup id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status",
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
    public function nlp($id) {
        // Dichiariamo i valori di default
        $data = array(
            "status" => null
        );

        // Normalizzazione
        if(!empty($this->input->input_stream('status')))
            $data["status"] = $this->input->input_stream('status');
        if(!empty($this->input->input_stream('logs')))
            $data["logs"] = $this->input->input_stream('logs');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->set_nlp_status($id, $data);
        if($entry == null)
            show_error("Cannot update due to service malfunctioning", 500);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Put(
     *     path="queue/{standup_id}/stt/",
     *     summary="Aggiorna lo stato di lavorazione dello Speech to Text",
     *     description="Aggiorna lo stato di lavorazione dello Speech to Text",
     *     produces={"application/json"},
     *     tags={"queue"},
     *     @SWG\Parameter(
     *         name="standup_id",
     *         in="path",
     *         description="Standup id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status",
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
    public function stt($id) {
        // Dichiariamo i valori di default
        $data = array(
            "status" => null
        );

        // Normalizzazione
        if(!empty($this->input->input_stream('status')))
            $data["status"] = $this->input->input_stream('status');
        if(!empty($this->input->input_stream('logs')))
            $data["logs"] = $this->input->input_stream('logs');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->set_stt_status($id, $data);
        if($entry == null)
            show_error("Cannot update due to service malfunctioning", 500);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Get(
     *     path="queue/count",
     *     summary="Numero di processi Speech to Text, Text Analysys in lavorazione",
     *     description="Numero di processi di Speech to Text e Text Analysys in lavorazione",
     *     produces={"application/json"},
     *     tags={"queue"},
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
    public function countPending() {
        $entry = $this->queues->countPending();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

}
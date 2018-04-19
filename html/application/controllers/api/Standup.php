<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once  FCPATH . '/application/core/MY_Standup.php';
class Standup extends MY_Standup
{

    function __construct()
    {
        //http://www.restapitutorial.com/lessons/httpmethods.html
        parent::__construct();
    }

    /**
     * @SWG\Get(
     *     path="standup/{standup_id}/pie",
     *     summary="Distribuzione Tipi",
     *     description="Fornisce i tipi e i dati con il numero di frasi divisi per tipo",
     *     produces={"application/json"},
     *     tags={"standup"},
     *     @SWG\Parameter(
     *         name="standup_id",
     *         in="path",
     *         description="Stand up id",
     *         required=true,
     *         type="integer",
     *     ),
     *      @SWG\Parameter(
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
    public function pie($id)
    {

        $result= new stdClass();
        $result->labels =  array('positive', 'negative', 'neutral', 'mixed');
        $result->series = array();

        foreach ($result->labels as $label) {
            $entry = $this->sentences->countType(
                $id,
                $label,
                $limit = $this->input->get('limit'),
                $offset = $this->input->get('offset')
            );

            foreach ( $entry as $value ){
                array_push( $result->series, $value->number  );
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));

    }

    /**
     * @SWG\Get(
     *     path="standup/{standup_id}/flow",
     *     summary="Andamento del discorso",
     *     description="Ritorna una serie di Double che rappresentano l'andamento dello standup",
     *     produces={"application/json"},
     *     tags={"standup"},
     *     @SWG\Parameter(
     *         name="standup_id",
     *         in="path",
     *         description="Stand up id",
     *         required=true,
     *         type="integer",
     *     ),
     *      @SWG\Parameter(
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
    public function flow($id)
    {
        $entry = $this->sentences->flow(
            $id,
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );

        $result= new stdClass();
        $result->series = array();

        foreach ($entry as $value) {
            if( !is_null($value->score) )
                array_push($result->series, $value->score);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    /**
     * @SWG\Get(
     *     path="standup/{standup_id}/entities",
     *     summary="Entità Rilevanti",
     *     description="Ritorna tutte le entità trovate nel testo dello standup, tranne quelle di tipo OTHER, riodinate per importanza",
     *     produces={"application/json"},
     *     tags={"standup"},
     *     @SWG\Parameter(
     *         name="standup_id",
     *         in="path",
     *         description="Stand up id",
     *         required=true,
     *         type="integer",
     *     ),
     *      @SWG\Parameter(
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
    public function entities($id)
    {
        $entry = $this->entities->getMoreImportant(
            $id,
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }


    /**
     * @SWG\Get(
     *     path="standup/{standup_id}/sentences",
     *     summary="Testo del discorso",
     *     description="Ritorna l'intero testo del discorso diviso per frasi con i relativi score e magnitude",
     *     produces={"application/json"},
     *     tags={"standup"},
     *     @SWG\Parameter(
     *         name="standup_id",
     *         in="path",
     *         description="Stand up id",
     *         required=true,
     *         type="integer",
     *     ),
     *      @SWG\Parameter(
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
    public function sentences($id)
    {
        $entry = $this->sentences->sentences(
            $id,
            $type = 'all',
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );

        foreach ($entry as $value){
            if($value->score == 0 && $value->magnitude > 0)
                $value->color = 'neutral';
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Get(
     *     path="standup/{standup_id}/sentences/good",
     *     summary="Frasi Rilevanti",
     *     description="Ritorna le frasi rilevanti nel discorso con relativi score e magnitude",
     *     produces={"application/json"},
     *     tags={"standup"},
     *     @SWG\Parameter(
     *         name="standup_id",
     *         in="path",
     *         description="Stand up id",
     *         required=true,
     *         type="integer",
     *     ),
     *      @SWG\Parameter(
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
    public function sentences_good($id)
    {
        $entry = $this->sentences->sentences(
            $id,
            $type = 'positive',
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Get(
     *     path="standup/{standup_id}/sentences/bad",
     *     summary="Frasi Problematiche",
     *     description="Ritorna le frasi problematiche nel discorso con relativi score e magnitude",
     *     produces={"application/json"},
     *     tags={"standup"},
     *     @SWG\Parameter(
     *         name="standup_id",
     *         in="path",
     *         description="Stand up id",
     *         required=true,
     *         type="integer",
     *     ),
     *      @SWG\Parameter(
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
    public function sentences_bad($id)
    {
        $entry = $this->sentences->sentences(
            $id,
            $type = 'negative',
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    public function target($id)
    {
        $this->nlp($id);
    }

    /**
     * @SWG\Post(
     *     path="standup/{standup_id}/nlp/",
     *     summary="Aggiunge risultati analisi",
     *     description="Aggiunge risultati analisi effettuati da Google Speeck to Text e Google NLP",
     *     produces={"application/json"},
     *     tags={"standup"},
     *     @SWG\Parameter(
     *         name="standup_id",
     *         in="path",
     *         description="Standup id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="json",
     *         in="query",
     *         description="NLP in JSON format",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @SWG\Response(
     *         response="412",
     *         description="Precondition failed"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error"
     *     )
     * )
     */
    private function nlp($id)
    {
        $standup_entity = $this->set_nlp($id, $this->security->xss_clean($this->input->raw_input_stream));
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($standup_entity));
        /*
        $nlp = json_decode($this->security->xss_clean($this->input->raw_input_stream));

        // PRE: dict needs score, magnitude, etc ...
        if (!isset($nlp->documentSentiment) ||
            !isset($nlp->documentSentiment->magnitude) ||
            !isset($nlp->documentSentiment->score) ||
            !isset($nlp->sentences) ||
            !isset($nlp->entities))
            show_error("NLP prerequisites missing", 412);

        // Update standup
        $data = array(
            "magnitude" => null,
            "score" => null
        );

        // Normalizzazione
        if (!is_null($nlp->documentSentiment->magnitude))
            $data["magnitude"] = (float) $nlp->documentSentiment->magnitude;
        if (!is_null($nlp->documentSentiment->score))
            $data["score"] = (float) $nlp->documentSentiment->score;

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->standups->update($id, $data);


        $sentence_records = remap_sentences($entry->id, $nlp->sentences);
        $sentence_counter = sizeof($sentence_records);
        $this->sentences->insert_batch($sentence_records);

        $entities_records = remap_entities($entry->id, $nlp->entities);
        $entities_counter = sizeof($entities_records);
        $this->entities->insert_batch($entities_records);

        // Scrittura e gestion del risultato REST-Style
        $data = array(
            "status" => "Success"
        );
        $this->queues->update_nlp($id, $data);

        // Risultato dell'operazione
        $standup_entity = array(
            "id" => $entry->id,
            "project_id" => $entry->project_id,
            "project" => $entry->project,
            "standup" => $entry->standup,
            "score" => $entry->score,
            "magnitude" => $entry->magnitude,
            "end" => $entry->end,
            "sentence_count" => $sentence_counter,
            "entities_count" => $entities_counter
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($standup_entity));
        */
    }

}
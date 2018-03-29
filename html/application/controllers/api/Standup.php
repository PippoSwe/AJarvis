<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Standup extends CI_Controller
{

    function __construct()
    {
        //http://www.restapitutorial.com/lessons/httpmethods.html
        parent::__construct();
        $this->load->model('Standup_model', 'standups', TRUE);
        $this->load->model('Sentence_model', 'sentences', TRUE);
        $this->load->model('Entity_model', 'entities', TRUE);
    }

    /**
     * @SWG\Get(
     *     path="member/{id}/",
     *     summary="Distribuzione Tipi",
     *     description="Fornisce i tipi e i dati con il numero di frasi divisi per tipo",
     *     produces={"application/json"},
     *     tags={"Standup"},
     *     @SWG\Parameter(
     *         name="id",
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
     *     path="member/{id}/",
     *     summary="Andamento del discorso",
     *     description="Ritorna una serie di Double che rappresentano l'andamento dello standup",
     *     produces={"application/json"},
     *     tags={"Standup"},
     *     @SWG\Parameter(
     *         name="id",
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
            array_push($result->series, $value->score);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    /**
     * @SWG\Get(
     *     path="member/{id}/",
     *     summary="Entità Rilevanti",
     *     description="Ritorna tutte le entità trovate nel testo dello standup, tranne quelle di tipo OTHER, riodinate per importanza",
     *     produces={"application/json"},
     *     tags={"Standup"},
     *     @SWG\Parameter(
     *         name="id",
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
     *     path="member/{id}/",
     *     summary="Testo del discorso",
     *     description="Ritorna l'intero testo del discorso diviso per frasi con i relativi score e magnitude",
     *     produces={"application/json"},
     *     tags={"Standup"},
     *     @SWG\Parameter(
     *         name="id",
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

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Get(
     *     path="member/{id}/",
     *     summary="Frasi Rilevanti",
     *     description="Ritorna le frasi rilevanti nel discorso con relativi score e magnitude",
     *     produces={"application/json"},
     *     tags={"Standup"},
     *     @SWG\Parameter(
     *         name="id",
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
     *     path="member/{id}/",
     *     summary="Frasi Problematiche",
     *     description="Ritorna le frasi problematiche nel discorso con relativi score e magnitude",
     *     produces={"application/json"},
     *     tags={"Standup"},
     *     @SWG\Parameter(
     *         name="id",
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
        $nlp = json_decode($this->security->xss_clean($this->input->raw_input_stream));

        // PRE: dict needs score, magnitude, etc ...
        if (!isset($nlp->documentSentiment))
            show_error("NLP prerequisites missing", 412);

        if (!isset($nlp->documentSentiment->magnitude))
            show_error("NLP prerequisites missing", 412);

        if (!isset($nlp->documentSentiment->score))
            show_error("NLP prerequisites missing", 412);

        if (!isset($nlp->sentences))
            show_error("NLP prerequisites missing", 412);

        if (!isset($nlp->entities))
            show_error("NLP prerequisites missing", 412);

        // Update standup
        $data = array(
            "magnitude" => null,
            "score" => null
        );

        // Normalizzazione
        if (!is_null($nlp->documentSentiment->magnitude))
            $data["magnitude"] = (float)$nlp->documentSentiment->magnitude;
        if (!is_null($nlp->documentSentiment->score))
            $data["score"] = (float)$nlp->documentSentiment->score;

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->standups->update($id, $data);

        // Insert sentences
        $sentence_counter = 0;
        foreach ($nlp->sentences as $sentence) {
            $data = array(
                "standup_id" => $entry->id,
                "sentence" => null,
                "magnitude" => null,
                "score" => null
            );
            // Normalizzazione
            if (!empty($sentence->text->content))
                $data["sentence"] = $sentence->text->content;
            if (!is_null($sentence->sentiment->score))
                $data["score"] = (float)$sentence->sentiment->score;
            if (!is_null($sentence->sentiment->magnitude))
                $data["magnitude"] = (float)$sentence->sentiment->magnitude;

            // Insert alla sentences
            $this->sentences->insert($data);
            $sentence_counter++;
        }

        // Insert entities
        $entities_counter = 0;
        foreach ($nlp->entities as $entities) {
            $data = array(
                "standup_id" => $entry->id,
                "name" => null,
                "type" => null,
                "salience" => null
            );
            // Normalizzazione
            if (!empty($entities->name))
                $data["name"] = $entities->name;
            if (!empty($entities->type))
                $data["type"] = $entities->type;
            if (!is_null($entities->salience))
                $data["salience"] = round((float)$entities->salience, 8);

            // Insert alla sentences
            $this->entities->insert($data);
            $entities_counter++;
        }

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

    }

}
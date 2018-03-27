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

    public function entities($id)
    {
        /*
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($standup_entity));
        */
    }

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
        if (!empty($nlp->documentSentiment->magnitude))
            $data["magnitude"] = (float)$nlp->documentSentiment->magnitude;
        if (!empty($nlp->documentSentiment->score))
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
            if (!empty($sentence->sentiment->score))
                $data["score"] = (float)$sentence->sentiment->score;
            if (!empty($sentence->sentiment->magnitude))
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
            if (!empty($entities->salience))
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
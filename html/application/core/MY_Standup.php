<?php
/**
 * Created by PhpStorm.
 * User: mfasolo
 * Date: 18/04/18
 * Time: 20:53
 */

class MY_Standup extends CI_Controller
{

    function __construct()
    {
        //http://www.restapitutorial.com/lessons/httpmethods.html
        parent::__construct();
        $this->load->model('Standup_model', 'standups', TRUE);
        $this->load->model('Sentence_model', 'sentences', TRUE);
        $this->load->model('Entity_model', 'entities', TRUE);
        $this->load->model('Queue_model', 'queues', TRUE);
        $this->load->helper(array('google_nlp_helper'));
    }

    protected function set_nlp_status($id, $data) {
        return $this->queues->update_nlp($id, $data);
    }

    protected function set_stt_status($id, $data) {
        return $this->queues->update_stt($id, $data);
    }

    protected function set_nlp($id, $json) {
        $nlp = json_decode($json);

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

        $sentence_counter = sizeof($nlp->sentences);
        if($sentence_counter) {
            $sentence_records = remap_sentences($entry->id, $nlp->sentences);
            $this->sentences->insert_batch($sentence_records);
        }

        $entities_counter = sizeof($nlp->entities);
        if($entities_counter) {
            $entities_records = remap_entities($entry->id, $nlp->entities);
            $this->entities->insert_batch($entities_records);
        }

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

        return $standup_entity;
    }
}
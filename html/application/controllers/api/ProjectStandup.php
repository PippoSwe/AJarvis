<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectStandup extends CI_Controller
{

    function __construct()
    {
        //http://www.restapitutorial.com/lessons/httpmethods.html
        parent::__construct();
        $this->load->model('Standup_model', 'standups', TRUE);
        $this->load->model('Sentence_model', 'sentences', TRUE);
        $this->load->model('Entity_model', 'entities', TRUE);
        $this->load->helper(array('google_storage_helper'));
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

    public function target($project_id, $id) {
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

    public function nlp($id) {
        $this->store_analysis($id);
    }

    /**
     * @SWG\Post(
     *     path="project/{project_id}/standup/",
     *     summary="Registra standup",
     *     description="Registra gli attributi e l'audio di uno standup ed archivia l'audio in Google Storage ",
     *     produces={"application/json"},
     *     tags={"project standup"},
     *     @SWG\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="file",
     *         in="query",
     *         description="Audio in formato WAV",
     *         required=true,
     *         type="string",
     *         format="binary",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @SWG\Response(
     *         response="412",
     *         description="Precondition Failed"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error"
     *     )
     * )
     */
    private function insert($project_id) {
        if(!array_key_exists('file', $_FILES))
            show_error("File is missing", 412);

        // Dichiariamo i valori di default
        $data = array(
            "project_id" => $project_id,
        );

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->standups->insert($data);
        if($entry == null)
            show_error("Cannot insert due to service malfunctioning", 500);

        // Converto i files
        if(!file_exists($_FILES['file']['tmp_name']))
            show_error("File ".$_FILES['file']['tmp_name']." not uploaded", 500);

        if(!isset($_FILES['file']) || $_FILES['file']['error'])
            show_error("Errors in ".$_FILES['file']." upload process", 500);

        // Conversione ffmpeg + invio a Google Storage
        $fname = "standup-".$entry->id;
        $this->save_audio($fname);

        // Response
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Put(
     *     path="project/{project_id}/standup/{standup_id}/",
     *     summary="Aggiorna standup",
     *     description="Aggiorna attributi standup",
     *     produces={"application/json"},
     *     tags={"project standup"},
     *     @SWG\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="standup_id",
     *         in="path",
     *         description="Standup id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="standup",
     *         in="query",
     *         description="Descrizione dello standup",
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
            "standup" => null
        );

        // Normalizzazione
        if(!empty($this->input->input_stream('standup')))
            $data["standup"] = $this->input->input_stream('standup');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->standups->update($id, $data);
        if($entry == null)
            show_error("Cannot update due to service malfunctioning", 500);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Get(
     *     path="project/{project_id}/standup/",
     *     summary="Elenca gli standup",
     *     description="Elenca gli standup per un progetto",
     *     produces={"application/json"},
     *     tags={"project standup"},
     *     @SWG\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project id",
     *         required=true,
     *         type="integer",
     *     ),
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
     *         response="404",
     *         description="Page Not Found"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error"
     *     )
     * )
     */
    private function find($project_id) {
        $entry = $this->standups->find(
            $project_id,
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Get(
     *     path="project/{project_id}/standup/{standup_id}/",
     *     summary="Visualizza standup",
     *     description="Visualizza tutti gli attributi dello standup",
     *     produces={"application/json"},
     *     tags={"project standup"},
     *     @SWG\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="standup_id",
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
    private function view($id) {
        $entry = $this->standups->get($id);
        if($entry == null)
            show_404();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Delete(
     *     path="project/{project_id}/standup/{standup_id}/",
     *     summary="Cancella standup",
     *     description="Cancella standup",
     *     produces={"application/json"},
     *     tags={"project standup"},
     *     @SWG\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="standup_id",
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
        if(!$this->standups->delete($id))
            show_error("Cannot delete due to service malfunctioning", 500);
    }


    private function save_audio($fname)
    {
        $path      = realpath("./application/audio_files");
        $wav_file  = $path . '/' . $fname . ".wav";
        $flac_file = $path . '/' . $fname . ".FLAC";

        rename ( $_FILES['file']['tmp_name'], $wav_file );
        // convert wav to FLAC
        $command = '/usr/bin/ffmpeg -i ' . $wav_file  . ' -ac 1 ' . $flac_file;
        exec($command);

        unlink( $wav_file );
        upload_file( $flac_file, $fname . ".FLAC" );
        unlink( $flac_file );
        return TRUE;
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
    private function store_analysis($id)
    {
        $nlp = json_decode($this->security->xss_clean($this->input->raw_input_stream));

        //throw new Exception(print_r($nlp->documentSentiment, true));

        // PRE: dict needs score, magnitude, etc ...
        if(!isset($nlp->documentSentiment))
            show_error("NLP prerequisites missing", 412);

        if(!isset($nlp->documentSentiment->magnitude))
            show_error("NLP prerequisites missing", 412);

        if(!isset($nlp->documentSentiment->score))
            show_error("NLP prerequisites missing", 412);

        if(!isset($nlp->sentences))
            show_error("NLP prerequisites missing", 412);

        if(!isset($nlp->entities))
            show_error("NLP prerequisites missing", 412);

        // Update standup
        $data = array(
            "magnitude" => null,
            "score" => null
        );

        // Normalizzazione
        if(!empty( $nlp->documentSentiment->magnitude ))
            $data["magnitude"] = (float) $nlp->documentSentiment->magnitude;
        if(!empty( $nlp->documentSentiment->score ))
            $data["score"] = (float) $nlp->documentSentiment->score;

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->standups->update($id, $data);

        // Insert sentences
        $sentence_counter = 0;
        foreach($nlp->sentences as $sentence) {
            $data = array(
                "standup_id" => $entry->id,
                "sentence" => null,
                "magnitude" => null,
                "score" => null
            );
            // Normalizzazione
            if(!empty( $sentence->text->content ))
                $data["sentence"] = $sentence->text->content;
            if(!empty( $sentence->sentiment->score ))
                $data["score"] = (float) $sentence->sentiment->score;
            if(!empty( $sentence->sentiment->magnitude ))
                $data["magnitude"] = (float) $sentence->sentiment->magnitude;

            // Insert alla sentences
            $this->sentences->insert($data);
            $sentence_counter++;
        }

        // Insert entities
        $entities_counter = 0;
        foreach($nlp->entities as $entities) {
            $data = array(
                "standup_id" => $entry->id,
                "name" => null,
                "type" => null,
                "salience" => null
            );
            // Normalizzazione
            if(!empty( $entities->name ))
                $data["name"] = $entities->name;
            if(!empty( $entities->type ))
                $data["type"] = $entities->type;
            if(!empty( $entities->salience ))
                $data["salience"] = round((float) $entities->salience, 8);

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
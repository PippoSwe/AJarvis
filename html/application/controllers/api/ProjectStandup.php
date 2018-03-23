<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectStandup extends CI_Controller
{

    function __construct()
    {
        //http://www.restapitutorial.com/lessons/httpmethods.html
        parent::__construct();
        $this->load->model('Standup_model', 'standups', TRUE);
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
     *         response="500",
     *         description="Internal Server Error"
     *     )
     * )
     */
    private function insert($project_id) {
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


}
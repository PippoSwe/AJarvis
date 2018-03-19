<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectStandup extends CI_Controller
{

    function __construct()
    {
        //http://www.restapitutorial.com/lessons/httpmethods.html
        parent::__construct();
        $this->load->model('Standup_model', 'standups', TRUE);
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
     *     path="/api/project/{project_id}/standup/",
     *     summary="Register standup",
     *     description="Register a standup in a project",
     *     produces={"application/json"},
     *     tags={"project standup"},
     *     @SWG\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
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
        try {
            $this->save_audio($entry->id);
        } catch (Exception $e) {
            show_error($e->getMessage(), 500);
        }

        // Response
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Put(
     *     path="/api/project/{project_id}/standup/{standup_id}/",
     *     summary="Register standup",
     *     description="Register a standup in a project",
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
     *         description="Standup description",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
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
     *     path="/api/project/{project_id}/standup/",
     *     summary="List standups for a project",
     *     description="List all standups registered in this projects",
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
     *         description="Retrieve {limit} elements",
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Pagination index start",
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
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
     *     path="/api/project/{project_id}/standup/{standup_id}/",
     *     summary="View standup",
     *     description="View standup attributes",
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
     *     path="/api/project/{project_id}/standup/{standup_id}/",
     *     summary="Delete standup",
     *     description="Delete standup",
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
     *     )
     * )
     */
    private function delete($id) {
        if(!$this->standups->delete($id))
            show_error("Cannot delete due to service malfunctioning", 500);
    }


    private function save_audio($standup_id)
    {
        // Processo di upload gestito in modo non sicuro per consentire
        // ai phpunit di funzionare e effettuare il Code Coverage
        if(!file_exists($_FILES['file']['tmp_name']))
            throw new Exception("File ".$_FILES['file']['tmp_name']." not uploaded");


        if(!isset($_FILES['file']) || $_FILES['file']['error'])
            throw new Exception("Errors in ".$_FILES['file']." upload process");

        $fname     = "standup-$standup_id";
        $path      = realpath("./application/audio_files");
        $wav_file  = $path . '/' . $fname . ".wav";
        $flac_file = $path . '/' . $fname . ".FLAC";

        if( !rename ( $_FILES['file']['tmp_name'], $wav_file ) )
            throw new Exception("Can't move ".$_FILES['file']['tmp_name']);

        // convert wav to FLAC
        $command = '/usr/bin/ffmpeg -i ' . $wav_file  . ' -ac 1 ' . $flac_file;
        exec($command);

        unlink( $wav_file );
        unlink( $flac_file );
        return TRUE;
    }


}
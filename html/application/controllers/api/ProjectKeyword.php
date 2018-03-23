<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectKeyword extends CI_Controller
{

    function __construct()
    {
        //http://www.restapitutorial.com/lessons/httpmethods.html
        parent::__construct();
        $this->load->model('ProjectKeyword_model', 'projects_keywords', TRUE);
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

    public function target($project_id, $keyword_id) {
        if($this->input->method(TRUE) == 'DELETE') {
            $this->delete($project_id, $keyword_id);
            return;
        }
        // else: GET
        $this->view($project_id, $keyword_id);
    }

    /**
     * @SWG\Post(
     *     path="project/{project_id}/keyword/",
     *     summary="Associa parola chiave e progetto",
     *     description="Associa una parola chiave al progetto",
     *     produces={"application/json"},
     *     tags={"project keyword"},
     *     @SWG\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="keyword_id",
     *         in="query",
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
    private function insert($project_id) {
        // Dichiariamo i valori di default
        $data = array(
            "project_id" => $project_id,
            "keyword_id" => null
        );

        // Normalizzazione
        if(!empty($this->input->post('keyword_id')))
            $data["keyword_id"] = $this->input->post('keyword_id');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->projects_keywords->insert($data);
        if($entry == null)
            show_error("Cannot insert due to service malfunctioning", 500);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Get(
     *     path="project/{project_id}/keyword/",
     *     summary="Elenco parole chiave per progetto",
     *     description="Elenca le parole chiave definite per un progetto",
     *     produces={"application/json"},
     *     tags={"project keyword"},
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
     *         response="500",
     *         description="Internal Server Error"
     *     )
     * )
     */
    private function find($project_id) {
        $entry = $this->projects_keywords->find(
            $project_id,
            null,
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Get(
     *     path="project/{project_id}/keyword/{keyword_id}/",
     *     summary="Visualizza progetti e parola chiave",
     *     description="Visualizza tutti gli attributi di parola chiave e progetto",
     *     produces={"application/json"},
     *     tags={"project keyword"},
     *     @SWG\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project id",
     *         required=true,
     *         type="integer",
     *     ),
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
    private function view($project_id, $keyword_id) {
        $records = $this->projects_keywords->find(
            $project_id,
            $keyword_id,
            $limit = 1,
            $offset = 0
        );
        $entry = null;
        if(sizeof($records) > 0)
            $entry = $records[0];
        if($entry == null)
            show_404();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Delete(
     *     path="project/{project_id}/keyword/{keyword_id}/",
     *     summary="Dissocia parola chiave dal progetto",
     *     description="Dissocia parola chiave dal progetto",
     *     produces={"application/json"},
     *     tags={"project keyword"},
     *     @SWG\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project id",
     *         required=true,
     *         type="integer",
     *     ),
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
    private function delete($project_id, $keyword_id) {
        if(!$this->projects_keywords->delete($project_id, $keyword_id))
            show_error("Cannot delete due to service malfunctioning", 500);
    }


}
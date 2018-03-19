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
     *     path="/api/project/{project_id}/keyword/",
     *     summary="Associate keyword to project",
     *     description="Associate keyword to this project",
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
     *     path="/api/project/{project_id}/keyword/",
     *     summary="List keywords for a project",
     *     description="List all keywords associated to this projects",
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
     *     path="/api/project/{project_id}/keyword/{keyword_id}/",
     *     summary="View project and keyword",
     *     description="View project and keyword attributes",
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
     *     path="/api/project/{project_id}/keyword/{keyword_id}/",
     *     summary="Delete project",
     *     description="Delete project",
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
     *     )
     * )
     */
    private function delete($project_id, $keyword_id) {
        if(!$this->projects_keywords->delete($project_id, $keyword_id))
            show_error("Cannot delete due to service malfunctioning", 500);
    }


}
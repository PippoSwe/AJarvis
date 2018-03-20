<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends CI_Controller
{

    /**
     * @SWG\Swagger(
     *     basePath="/api/",
     *     host="localhost:8443",
     *     schemes={"http", "https"},
     *     produces={"application/json"},
     *     consumes={"application/json"},
     *     @SWG\Info(
     *         version="0.1",
     *         title="AJarvis API",
     *         description="Documentazione delle API HTTP/HTTPS esposte dal microservizio AJarvis",
     *         termsOfService="http://swagger.io/terms/",
     *         @SWG\Contact(name="Pippo.swe API Team"),
     *         @SWG\License(name="GPL 3")
     *     )
     * )
     */

    function __construct()
    {
        //http://www.restapitutorial.com/lessons/httpmethods.html
        parent::__construct();
        $this->load->model('Project_model', 'projects', TRUE);
    }

    public function index() {
        if($this->input->method(TRUE) == 'POST') {
            $this->insert();
            return;
        }
        // else: GET
        $this->find();
    }

    public function target($id) {
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
     * @SWG\Get(
     *     path="project/",
     *     summary="List projects",
     *     description="List all projects",
     *     produces={"application/json"},
     *     tags={"project"},
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
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error"
     *     )
     * )
     */
    private function find() {
        $entry = $this->projects->find(
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Post(
     *     path="project/",
     *     summary="Create project",
     *     description="Create new project",
     *     produces={"application/json"},
     *     tags={"project"},
     *     @SWG\Parameter(
     *         name="project",
     *         in="query",
     *         description="Project name",
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
    private function insert() {
        // Dichiariamo i valori di default
        $data = array(
            "project" => null
        );

        // Normalizzazione
        if(!empty($this->input->post('project')))
            $data["project"] = $this->input->post('project');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->projects->insert($data);
        if($entry == null)
            show_error("Cannot insert due to service malfunctioning", 500);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Get(
     *     path="project/{project_id}",
     *     summary="View project",
     *     description="View all details for a project",
     *     produces={"application/json"},
     *     tags={"project"},
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
        $entry = $this->projects->get($id);
        if($entry == null)
            show_404();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Put(
     *     path="project/{project_id}",
     *     summary="Update project",
     *     description="Update project attributes",
     *     produces={"application/json"},
     *     tags={"project"},
     *     @SWG\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="project",
     *         in="query",
     *         description="Project name",
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
            "project" => null
        );

        // Normalizzazione
        if(!empty($this->input->input_stream('project')))
            $data["project"] = $this->input->input_stream('project');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->projects->update($id, $data);
        if($entry == null)
            show_error("Cannot update due to service malfunctioning", 500);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Delete(
     *     path="project/{project_id}",
     *     summary="Delete project",
     *     description="Delete project",
     *     produces={"application/json"},
     *     tags={"project"},
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
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error"
     *     )
     * )
     */
    private function delete($id) {
        if(!$this->projects->delete($id))
            show_error("Cannot delete due to service malfunctioning", 500);
    }

}
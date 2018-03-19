<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectMember extends CI_Controller
{

    function __construct()
    {
        //http://www.restapitutorial.com/lessons/httpmethods.html
        parent::__construct();
        $this->load->model('ProjectMember_model', 'projects_members', TRUE);
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

    public function target($project_id, $member_id) {
        if($this->input->method(TRUE) == 'DELETE') {
            $this->delete($project_id, $member_id);
            return;
        }
        // else: GET
        $this->view($project_id, $member_id);
    }

    /**
     * @SWG\Post(
     *     path="/api/project/{project_id}/member/",
     *     summary="Associate member to project",
     *     description="Associate member to this project",
     *     produces={"application/json"},
     *     tags={"project member"},
     *     @SWG\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="member_id",
     *         in="query",
     *         description="Member id",
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
            "member_id" => null
        );

        // Normalizzazione
        if(!empty($this->input->post('member_id')))
            $data["member_id"] = $this->input->post('member_id');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->projects_members->insert($data);
        if($entry == null)
            show_error("Cannot insert due to service malfunctioning", 500);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Get(
     *     path="/api/project/{project_id}/member/",
     *     summary="List members for a project",
     *     description="List all members associated to this projects",
     *     produces={"application/json"},
     *     tags={"project member"},
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
        $entry = $this->projects_members->find(
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
     *     path="/api/project/{project_id}/member/{member_id}/",
     *     summary="View project and member",
     *     description="View project and member attributes",
     *     produces={"application/json"},
     *     tags={"project member"},
     *     @SWG\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="member_id",
     *         in="path",
     *         description="Member id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *     )
     * )
     */
    private function view($project_id, $member_id) {
        $records = $this->projects_members->find(
            $project_id,
            $member_id,
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
     *     path="/api/project/{project_id}/member/{member_id}/",
     *     summary="Delete member",
     *     description="Delete member",
     *     produces={"application/json"},
     *     tags={"project member"},
     *     @SWG\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="member_id",
     *         in="path",
     *         description="Member id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *     )
     * )
     */
    private function delete($project_id, $member_id) {
        if(!$this->projects_members->delete($project_id, $member_id))
            show_error("Cannot delete due to service malfunctioning", 500);
    }


}
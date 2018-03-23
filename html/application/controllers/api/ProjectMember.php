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
     *     path="project/{project_id}/member/",
     *     summary="Associa membro al progetto",
     *     description="Associa Un nuovo membro a questo progetto",
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
     *     path="project/{project_id}/member/",
     *     summary="Elenca i membri del progetto",
     *     description="Elenca i membri di questo progetto",
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
     *     path="project/{project_id}/member/{member_id}/",
     *     summary="Visualizza progetto e membro",
     *     description="Visualizza tutti gli attributi di membro e progetto",
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
     *     path="project/{project_id}/member/{member_id}/",
     *     summary="Dissocia membro dal progetto",
     *     description="Dissocia membro dal progetto",
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
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error"
     *     )
     * )
     */
    private function delete($project_id, $member_id) {
        if(!$this->projects_members->delete($project_id, $member_id))
            show_error("Cannot delete due to service malfunctioning", 500);
    }


}
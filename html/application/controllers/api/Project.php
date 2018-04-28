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
     *     path="project/{project_id}/statistics",
     *     summary="Statistiche generali del progetto",
     *     description="Ritorna una serie di informazioni che forniscono indicatori sul progetto",
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
    public function statistics($id)
    {
        $result = $this->projects->statistics($id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }


    /**
     * @SWG\Get(
     *     path="project/{project_id}/flow",
     *     summary="Andamento del discorso",
     *     description="Ritorna una serie di Double che rappresentano l'andamento del progetto",
     *     produces={"application/json"},
     *     tags={"project"},
     *     @SWG\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project id",
     *         required=true,
     *         type="integer",
     *     ),
     *      @SWG\Parameter(
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
    public function flow($id)
    {
        $entry = $this->projects->flow(
            $id,
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );

        $result= new stdClass();
        $result->series = array();

        foreach ($entry as $value) {
            if( !is_null($value->score) )
                array_push($result->series, $value->score);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    /**
     * @SWG\Get(
     *     path="project/{project_id}/sentences/good",
     *     summary="Frasi Rilevanti",
     *     description="Ritorna le frasi rilevanti nel discorso con relativi score e magnitude",
     *     produces={"application/json"},
     *     tags={"project"},
     *     @SWG\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project id",
     *         required=true,
     *         type="integer",
     *     ),
     *      @SWG\Parameter(
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
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function sentences_good($id)
    {
        $entry = $this->projects->sentences(
            $id,
            $type = 'positive',
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Get(
     *     path="project/{project_id}/sentences/bad",
     *     summary="Frasi Problematiche",
     *     description="Ritorna le frasi problematiche nel discorso con relativi score e magnitude",
     *     produces={"application/json"},
     *     tags={"project"},
     *     @SWG\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project id",
     *         required=true,
     *         type="integer",
     *     ),
     *      @SWG\Parameter(
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
    public function sentences_bad($id)
    {
        $entry = $this->projects->sentences(
            $id,
            $type = 'negative',
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Get(
     *     path="project/{project_id}/entities",
     *     summary="Le entitá piú discusse del progetto",
     *     description="Ritorna le entitá piú discusse del progetto, insieme alla somma della loro salience e di quante volte sono state dette",
     *     produces={"application/json"},
     *     tags={"project"},
     *     @SWG\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project id",
     *         required=true,
     *         type="integer",
     *     ),
     *      @SWG\Parameter(
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
    public function entities($id)
    {
        $entry = $this->projects->entities(
            $id,
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset')
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Get(
     *     path="project/",
     *     summary="Elenco dei progetti",
     *     description="Elenca tutti i progetti dell'azienda",
     *     produces={"application/json"},
     *     tags={"project"},
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
     *     summary="Aggiungi progetto",
     *     description="Aggiungi un nuovo progetto",
     *     produces={"application/json"},
     *     tags={"project"},
     *     @SWG\Parameter(
     *         name="project",
     *         in="query",
     *         description="Nome progetto",
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
     *     summary="Visualizza progetto",
     *     description="Visualizza tutti gli attributi di un progetto",
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
     *     summary="Aggiorna progetto",
     *     description="Aggiorna tutti gli attributi di un progetto",
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
     *         description="Nome progetto",
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
     *     summary="Cancella progetto",
     *     description="Cancella un progetto dell'azienda",
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
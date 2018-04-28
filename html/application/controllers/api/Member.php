<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends CI_Controller
{

    function __construct()
    {
        //http://www.restapitutorial.com/lessons/httpmethods.html
        parent::__construct();
        $this->load->model('Member_model', 'members', TRUE);
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
     *     path="member/",
     *     summary="Elenco dei membri",
     *     description="Elenca tutti i membri dell'azienda",
     *     produces={"application/json"},
     *     tags={"member"},
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
        $entry = $this->members->find(
            $limit = $this->input->get('limit'),
            $offset = $this->input->get('offset'),
            $searchParam = $this->input->get('q')
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Post(
     *     path="member/",
     *     summary="Aggiungi membro",
     *     description="Aggiungi un nuovo membro",
     *     produces={"application/json"},
     *     tags={"member"},
     *     @SWG\Parameter(
     *         name="firstname",
     *         in="query",
     *         description="Nome",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="lastname",
     *         in="query",
     *         description="Cognome",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="work",
     *         in="query",
     *         description="Indica se il lavoratore lavora opppure no",
     *         required=false,
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
            "firstname" => null,
            "lastname" => null,
            "work" => false,
        );

        // Normalizzazione
        if(!empty($this->input->post('firstname')))
            $data["firstname"] = $this->input->post('firstname');
        if(!empty($this->input->post('lastname')))
            $data["lastname"] = $this->input->post('lastname');
        if(!empty($this->input->post('work')))
            $data["work"] = $this->input->post('work');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->members->insert($data);
        if($entry == null)
            show_error("Cannot insert due to service malfunctioning", 500);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Get(
     *     path="member/{member_id}/",
     *     summary="Visualizza membro",
     *     description="Visualizza tutti gli attributi di un membro",
     *     produces={"application/json"},
     *     tags={"member"},
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
    private function view($id) {
        $entry = $this->members->get($id);
        if($entry == null)
            show_404();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Put(
     *     path="member/{member_id}/",
     *     summary="Aggiorna membro",
     *     description="Aggiorna tutti gli attributi di un membro",
     *     produces={"application/json"},
     *     tags={"member"},
     *     @SWG\Parameter(
     *         name="member_id",
     *         in="path",
     *         description="Member id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="firstname",
     *         in="query",
     *         description="Nome",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="lastname",
     *         in="query",
     *         description="Cognome",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="work",
     *         in="query",
     *         description="Indica se il lavoratore lavora oppure no ",
     *         required=false,
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
            "firstname" => null,
            "lastname" => null,
            "work" => false,
        );

        // Normalizzazione
        if(!empty($this->input->input_stream('firstname')))
            $data["firstname"] = $this->input->input_stream('firstname');
        if(!empty($this->input->input_stream('lastname')))
            $data["lastname"] = $this->input->input_stream('lastname');
        if(!empty($this->input->input_stream('work')))
            $data["work"] = $this->input->input_stream('work');

        // Scrittura e gestion del risultato REST-Style
        $entry = $this->members->update($id, $data);
        if($entry == null)
            show_error("Cannot update due to service malfunctioning", 500);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    /**
     * @SWG\Delete(
     *     path="member/{member_id}/",
     *     summary="Cancella membro",
     *     description="Cancella un membro dell'azienda",
     *     produces={"application/json"},
     *     tags={"member"},
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
    private function delete($id) {
        if(!$this->members->delete($id))
            show_error("Cannot delete due to service malfunctioning", 500);
    }

}
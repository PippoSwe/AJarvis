<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Config_model', 'configs', TRUE);
    }

/**
     * @SWG\Get(
     *     path="config/read",
     *     summary="Configurazioni",
     *     description="Restituisce un array associativo con tutte le configurazioni",
     *     produces={"application/json"},
     *     tags={"config"},
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
   function readData(){
       $entry = $this->configs->readAll();

       $this->output
           ->set_content_type('application/json')
           ->set_output(json_encode($entry));
   }

  /**
   * @SWG\Post(
   *     path="config/update",
   *     summary="Aggiorna la chiave Google",
   *     description="Aggiorna la chiave di Google",
   *     produces={"application/json"},
   *     tags={"config"},
   *     @SWG\Parameter(
   *         name="key_file",
   *         in="query",
   *         description="Il json della chiave di Google",
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
   function updateData(){
       $this->configs->update(array('key' => 'key_file', 'value' => $this->input->post('key_file')));
   }

  /**
   * @SWG\Post(
   *     path="config/checkConnection",
   *     summary="Controlla la validità della chiave Google",
   *     description="Controlla la validità della chiave Google",
   *     produces={"application/json"},
   *     tags={"config"},
   *     @SWG\Parameter(
   *         name="key_file",
   *         in="query",
   *         description="Il json della chiave di Google",
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
   function checkKey()
   {
        $this->load->helper(array('google_storage_helper'));
        $entry = checkConnection($this->input->post('key_file'));
        $this->output
           ->set_content_type('application/json')
           ->set_output(json_encode($entry));
   }
}
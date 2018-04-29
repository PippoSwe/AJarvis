<?php
defined('BASEPATH') OR exit('No direct script access allowed');

# Includes the autoloader for libraries installed with composer
require  FCPATH . '/vendor/autoload.php';

use Google\Cloud\Core\Exception\GoogleException;

class Config extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Config_model', 'configs', TRUE);
        $this->load->helper(array('google_storage_helper'));
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
       $c = array();
       foreach($entry as $record) {
           $c[$record->key] = $record->value;
       }

       $this->output
           ->set_content_type('application/json')
           ->set_output(json_encode($c));
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
       if(!empty($this->input->post('key_file')))
           $this->configs->update(
               array('key' => 'key_file', 'value' => $this->input->post('key_file'))
           );
       if(!empty($this->input->post('service_type')))
           $this->configs->update(
               array('key' => 'service_type', 'value' => $this->input->post('service_type'))
           );
       if(!empty($this->input->post('silence_tolerance')))
           $this->configs->update(
               array('key' => 'silence_tolerance', 'value' => $this->input->post('silence_tolerance'))
           );
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
        try {
            $entry = checkConnection($this->input->post('key_file'));
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($entry));
        }
        catch(GoogleException $e) {
            show_error("Cannot connection to Google", 500);
        }
   }
}
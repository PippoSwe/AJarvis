<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Config_model', 'configs', TRUE);
    }

   function readData(){
       $entry = $this->configs->readAll();

       $this->output
           ->set_content_type('application/json')
           ->set_output(json_encode($entry));
   }

   function updateData(){
       $this->configs->update(array('key' => 'key_file', 'value' => $this->input->post('key_file')));
   }

   function checkKey()
   {
        $this->load->helper(array('google_storage_helper'));
        $entry = checkConnection($this->input->post('key_file'));
        $this->output
           ->set_content_type('application/json')
           ->set_output(json_encode($entry));
   }
}
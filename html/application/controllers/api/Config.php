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
       $data = array(
           array('key' => 'audio_bucket_name', 'value' => $this->input->post('audio_bucket_name')),
           array('key' => 'key_file', 'value' => $this->input->post('key_file')),
           array('key' => 'ip', 'value' => $this->input->post('ip')),
           array('key' => 'port ', 'value' => $this->input->post('port')),
       );

       foreach($data as $updateData) {
           $this->configs->update($updateData);

       }

   }

}
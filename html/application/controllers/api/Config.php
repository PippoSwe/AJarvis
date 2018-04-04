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
           array('key' => 'audio_bucket_name', 'value' => 'ajarvis-rest'),
           array('key' => 'key_file', 'value' => ''),
           array('key' => 'ip', 'value' => ''),
           array('key' => 'port ', 'value' => ''),
       );

       foreach($data as $updateData)
           $this->configs->update($updateData);
   }

}
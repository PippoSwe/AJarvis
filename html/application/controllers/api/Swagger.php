<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require  FCPATH . '/vendor/autoload.php';

class Swagger extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index() {
        $path = realpath(dirname(__FILE__));
        $swagger = \Swagger\scan($path);
        $this->output
            ->set_header('Access-Control-Allow-Origin: *')
            ->set_header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT')
            ->set_header('Access-Control-Allow-Headers: Content-Type')
            ->set_content_type('application/json')
            ->set_output($swagger);
    }

}
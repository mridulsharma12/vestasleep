<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

use chriskacerguis\RestServer\RestController;

class States extends RestController
{

    private $param                   =     array();
    private $nonAuthentication_arr     =     array('getstate', 'addstate');
    private    $data                    =    array();
    var $id;
    var $session_key;
    var    $method;

    function __construct()
    {
        parent::__construct();
        $this->load->model('QueryBuilder');
        $this->load->model('AuthModel');
        $this->load->helper('common_helper');

        $this->method =    $this->uri->segment(1);
        if (!empty($this->method) && $this->method !== NULL) {
            if (in_array($this->method, $this->nonAuthentication_arr) == false) {
                $data    =    $this->AuthModel->checkAuth();
                if ($data['status'] != 200) {
                    $this->response($data, $data['status']);
                } else {
                    $this->id        =    $data['data']['id'];
                    $this->session_key    =    $data['data']['session_key'];
                }
            }
        } else {
            $data = array('status' => 404, 'message' => 'Not Found');
            $this->response($data, $data['status']);
        }
    }


    #_____________________ADD STATES_____________________#

    public function addState_post()
    {
        $states  =  array('Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', ' Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming', 'American Samoa', 'Guam', 'the Northern Mariana Islands', 'Puerto Rico', 'the U.S. Virgin Islands');

        foreach ($states as $value) {
            $data = array('name' => $value);
            $insert = $this->QueryBuilder->insert_data('states', $data);
        }
        if ($insert) {
            $this->response(['status' => 200, 'message' => 'Inserted Successfully'], RestController::HTTP_OK);
        } else {
            $this->response(['status' => 400, 'message' => 'Insertion Not Done'], RestController::HTTP_BAD_REQUEST);
        }
    }

    #_____________________END_____________________#






    #_____________________GET STATES_____________________#

    public function getState_get()
    {
        $get_states = $this->QueryBuilder->get_state('states');
        if ($get_states) {
            $this->response(['status' => 200, 'data' => $get_states], RestController::HTTP_OK);
        } else {
            $this->response(['status' => 400, 'message' => 'Insertion Not Done'], RestController::HTTP_BAD_REQUEST);
        }
    }
    #_____________________END_____________________#


}

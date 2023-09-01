<?php

class AuthModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('QueryBuilder');
    }


    public function checkAuth()
    {
        $check     = array('id' => $this->input->get_request_header('id', TRUE), 'session_key' => $this->input->get_request_header('session_key', TRUE));

        if (checkRequired($check)) {
            return array('status' => 400, 'message' => "Header Request : " . checkRequired($check), 'method' => $this->method);
        } else {
            $id            =     $this->input->get_request_header('id', TRUE);
            $session_key        =     $this->input->get_request_header('session_key', TRUE);
            if ($this->AuthModel->checkSession(array('id' => $id, 'session_key' => $session_key)) != 200) {
                return array('status' => 401, 'message' =>  'Unauthorized', 'method' => $this->method);
            } else {
                return array('status' => 200, 'data' => array('id' => $id, 'session_key' => $session_key));
            }
        }
    }


    public function checkSession($data)
    {
        $check    =    $this->QueryBuilder->select_data('user', array('id' => $data['id'], 'session_key' => $data['session_key']));
        // if (count($check) > 0) {
        if ($check > 0) {
            return 200;
        } else {
            return 401;
        }
    }
}

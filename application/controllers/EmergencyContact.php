<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

use chriskacerguis\RestServer\RestController;

class EmergencyContact extends RestController
{

    private $param                   =     array();
    private $nonAuthentication_arr     =     array();
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


    #_____________________EmergencyContact_____________________#

    public function emergencyContact_post()
    {
        $rawdata = file_get_contents("php://input");
        $data = json_decode($rawdata, true);


        if (count($data) === 0) {
            $this->response(['status' => 400, 'message' => 'Single Data Must Needed'], RestController::HTTP_BAD_REQUEST);
        } else {
            foreach ($data as $value) {

                $userdata = array(
                    'name' => $value['name'],
                    'contact' => $value['contact'],
                    'country_code' => $value['country_code'],
                );

                $array_filter = array_filter($userdata);
                if (!empty($array_filter)) {
                    $array_filter['user_id'] = $this->id;
                    $result = $this->QueryBuilder->insert_data('emergency_contact', $array_filter);

                    if ($result) {
                        $where = array('user_id' => $this->id);
                        $data = $this->QueryBuilder->select_data('emergency_contact', $where);
                        $response  = array('status' => 200, 'message' => 'Emergency Contact Updated Successfully', 'data' => $data);
                    } else {
                        $response  = array('status' => 400, 'message' => 'Emergency Contact Not Updated',);
                    }
                } else {
                    $this->response(['status' => 400, 'message' => 'Incomplete Data for Emergency Contact'], RestController::HTTP_BAD_REQUEST);
                }
            }
            if ($response) {
                $this->response($response, $response['status']);
            }
        }
    }

    #_____________________End_____________________#





    #_____________________UPDATE EMERGENCY CONTACT_____________________#

    public function updateEmergencyContact_post()
    {
        $rawdata = file_get_contents("php://input");
        $data = json_decode($rawdata, true);

        if (count($data) === 0) {
            $this->response(['status' => 400, 'message' => 'Single Data Must Needed'], RestController::HTTP_BAD_REQUEST);
        } else {
            foreach ($data as $value) {
                $check_data = array(
                    'contact' => $value['contact'],
                    'user_id' => $this->id
                );
                $check = $this->QueryBuilder->get_column('emergency_contact', 'contact', $check_data);

                if ($check) {
                    $response  = array('status' => 400, 'message' => 'Duplicate Contacts Found');
                } else {
                    $userdata = array(
                        'name' => $value['name'],
                        'contact' => $value['contact'],
                        'country_code' => $value['country_code'],
                    );
                    $array_filter = array_filter($userdata);
                    if (!empty($array_filter)) {
                        $array_filter['user_id'] = $this->id;
                        $where = array(
                            'id' => $value['id'],
                            'user_id' => $this->id
                        );

                        $result = $this->QueryBuilder->update_data('emergency_contact', $array_filter, $where);
                        if ($result) {

                            $response  = array('status' => 200, 'message' => 'Emergency Contact Updated Successfully',);
                        }
                    } else {
                        $this->response(['status' => 400, 'message' => 'Incomplete Data for Emergency Contact'], RestController::HTTP_BAD_REQUEST);
                    }
                }
            }
            if ($response) {
                $this->response($response, $response['status']);
            } else {
                $this->response(['status' => 400, 'message' => 'Emergency Contact Not Updated'], RestController::HTTP_BAD_REQUEST);
            }
        }
    }
}

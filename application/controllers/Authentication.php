<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

use chriskacerguis\RestServer\RestController;

class Authentication extends RestController
{

    private $param                   =     array();
    private $nonAuthentication_arr     =     array('signup', 'signin',);
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

    #_____________________Registeration_____________________#

    public function signup_post()
    {
        $session_key = sessionGenrate();
        $input = $this->input->post();
        $data = array('session_key' => @$session_key, 'username' => @$input['username'], 'email' => @$input['email'], 'password' => @$input['password'], 'device_type' => @$input['device_type'], 'login_type' => @$input['login_type']);

        if (checkRequired($input)) {
            $result  = array('status' => 400, 'message' => checkRequired($data));
            $this->response($result, $result['status']);
        } else {
            $data_check = array(
                'username' => $data['username'],
                'email' => $data['email'],
            );
            $isExist = $this->QueryBuilder->get_data('user', $data_check);

            if ($isExist > 0) {
                $this->response(['status' => 400, 'message' => 'Username or Email Already Exist'], RestController::HTTP_BAD_REQUEST);
            } else {
                $user = array(
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'password' => md5($data['password']),
                    'login_type' => $data['login_type'],
                    'device_type' => $data['device_type'],
                    'session_key' => $data['session_key'],
                    'created_at' => createdDate(),
                    'role' => 1,
                );

                $insert = $this->QueryBuilder->insert_data('user', $user);

                if ($insert) {
                    $this->response(['status' => 200, 'message' => 'Registered SuccessFully'], RestController::HTTP_OK);
                } else {
                    $this->response(['status' => 400, 'message' => 'Registration Failed'], RestController::HTTP_BAD_REQUEST);
                }
            }
        }
    }
    #_____________________END_____________________#



    #_____________________Login_____________________#


    public function signin_post()
    {
        $input = $this->input->post();
        $data = array(
            'email' => @$input['email'],
            'password' => md5(@$input['password'])
        );

        if (checkRequired($input)) {
            $result  = array('status' => 400, 'message' => checkRequired($input));
            $this->response($result, $result['status']);
        } else {
            $user_data =  array(
                'email' => $data['email'],
                'password' => $data['password']
            );
        }
        $fetch = $this->QueryBuilder->select_data('user', $user_data);
        if ($fetch) {
            foreach ($fetch as $value) {
                if (($value['role'] == 0)) {
                    $session_key = sessionGenrate();
                    $data = array(
                        'status' => 1,
                        'session_key' => $session_key,
                    );
                    $this->QueryBuilder->update('user', $data, $value['id']);
                    $value['session_key'] = $session_key;
                    unset($value['password']);
                    $this->response(['status' => 200, 'message' => 'Admin Login SuccessFully', 'data' => $value], RestController::HTTP_OK);
                } elseif (($value['role'] == 1)) {
                    $data = array(
                        'status' => 1,
                        'session_key' => sessionGenrate(),
                    );
                    $this->QueryBuilder->update('user', $data, $value['id']);
                    unset($value['password']);
                    $this->response(['status' => 200, 'message' => 'User Login SuccessFully', 'data' => $value], RestController::HTTP_OK);
                } else {
                    $this->response(['status' => 400, 'message' => 'Your account has been Deactivated'], RestController::HTTP_BAD_REQUEST);
                }
            }
        } else {
            $this->response(['status' => 400, 'message' => 'Invalid Email or Password'], RestController::HTTP_BAD_REQUEST);
        }
    }


    #_____________________End_____________________#



    #_____________________Details_____________________#

    public function details_post()
    {
        $input = $this->input->post();
        if (checkRequired($input)) {
            $result  = array('status' => 400, 'message' => checkRequired($input));
            $this->response($result, $result['status']);
        } else {
            $data = array(
                'id' => @$input['id'],
                'gender' => @$input['gender'],
                'age' => @$input['age'],
                'height' => @$input['height'],
                'weight' => @$input['weight'],
                'address' => @$input['address'],
                'apartment' => @$input['apartment'],
                'unit_number' => @$input['unit_number'],
                'city' => @$input['city'],
                'state' => @$input['state'],
                'zip_code' => @$input['zip_code']
            );
            if (!empty(isset($data['gender']))) {
                if ($data['gender'] == 0) {
                    $gender = 'Male';
                } elseif ($data['gender'] == 1) {
                    $gender = 'Female';
                } elseif ($data['gender'] == 2) {
                    $gender =  'Others';
                } else {
                    $this->response(['status' => 400, 'message' => 'Please Enter Valid Gender'], RestController::HTTP_BAD_REQUEST);
                }
            }

            $user_data = array(
                'gender' => $gender,
                'age' => $data['age'],
                'height' => $data['height'],
                'weight' => $data['weight'],
                'address' => $data['address'],
                'apartment' => @$input['apartment'],
                'unit_number' => $data['unit_number'],
                'city' => $data['city'],
                'state' => $data['state'],
                'zip_code' => $data['zip_code']
            );
            $id = $this->id;
            $update = $this->QueryBuilder->update('user', $user_data, $id);
            if ($update) {
                $this->response(['status' => 200, 'message' => 'Data Updated Successfully'], RestController::HTTP_OK);
            } else {
                $this->response(['status' => 400, 'message' => 'Data Updation Not Done'], RestController::HTTP_BAD_REQUEST);
            }
        }
    }


    #_____________________END_____________________#



    #_____________________Profile Update_____________________#


    public function profileUpdate_post()
    {
        $input = $this->input->post();

        $username = "";
        $email = "";
        $password = "";
        $image = "";

        if (isset($input['username']) && !empty($input['username'])) {
            $check_data = array(
                'username' => @$input['username'],
            );
            $check = $this->QueryBuilder->get_data('user', $check_data);
            if ($check) {
                $this->response(['status' => 400, 'message' => 'Username Already Exist'], RestController::HTTP_BAD_REQUEST);
            } else {
                $username = $input['username'];
            }
        }
        if (isset($input['email']) && !empty($input['email'])) {
            $check_data = array(
                'email' => @$input['email'],
            );
            $check = $this->QueryBuilder->get_data('user', $check_data);
            if ($check) {
                $this->response(['status' => 400, 'message' => 'Email Already Exist'], RestController::HTTP_BAD_REQUEST);
            } else {
                $email = $input['email'];
            }
        }
        if ((isset($input['password'])) && (!empty($input['password'])) || ((isset($input['repeat_password'])) && !empty($input['repeat_password']))) {
            if (@$input['password'] !== @$input['repeat_password']) {
                $this->response(['status' => 400, 'message' => 'Repeat Password Not Matched'], RestController::HTTP_BAD_REQUEST);
            } else {
                $password = $input['password'];
            }
        }

        if (isset($_FILES['image']) && !empty($_FILES['image'])) {
            $config['upload_path'] = './upload/userprofiles/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            $this->upload->initialize($config);
            if (($this->upload->do_upload('image'))) {
                $upload = $this->upload->data();
                $image = $config['upload_path'] . $upload['file_name'];
            }
        }

        $user_data = array(
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'image' => $image,
        );

        $filter_data = array_filter($user_data);

        if ($filter_data) {
            $filter_data['password'] = md5($password);
            $where = array('id' => $this->id);
            $update = $this->QueryBuilder->update_data('user', $filter_data, $where);

            if ($update) {
                $this->response(['status' => 200, 'message' => 'Updated Successfully'], RestController::HTTP_OK);
            } else {
                $this->response(['status' => 400, 'message' => 'Updation Not Done'], RestController::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response(['status' => 400, 'message' => 'No Valid Data to Update'], RestController::HTTP_BAD_REQUEST);
        }
    }




    #_____________________END_____________________#






    #____________________Update Address_____________________#


    // public function updateAddress_post()
    // {

    //     $input = $this->input->post();



    //     if (checkRequired($input)) {
    //         $result  = array('status' => 400, 'message' => checkRequired($input));
    //         $this->response($result, $result['status']);
    //     } else {
    //         $data = array(
    //             'address' => @$input['address'],
    //             'apartment' => @$input['apartment'],
    //             'unit_number' => @$input['unit_number'],
    //             'city' => @$input['city'],
    //             'state' => @$input['state'],
    //             'zip_code' => @$input['zip_code']
    //         );
    //         $where = array('id' => $this->id);
    //         $update = $this->QueryBuilder->update_data('user', $data, $where);
    //         if ($update) {
    //             $this->response(['status' => 200, 'message' => 'Data Updated Successfully'], RestController::HTTP_OK);
    //         } else {
    //             $this->response(['status' => 400, 'message' => 'Data Updation Not Done'], RestController::HTTP_BAD_REQUEST);
    //         }
    //     }
    // }

    public function updateAddress_post()
    {
        $input = $this->input->post();

        $address =     "";
        $apartment =    "";
        $unit_number =    "";
        $city =    "";
        $state =    "";
        $zip_code =    "";

        if (isset($input['address']) && !empty($input['address'])) {
            $address = $input['address'];
        }
        if (isset($input['apartment']) && !empty($input['apartment'])) {
            $apartment = $input['apartment'];
        }
        if (isset($input['unit_number']) && !empty($input['unit_number'])) {
            $unit_number = $input['unit_number'];
        }
        if (isset($input['city']) && !empty($input['city'])) {
            $city = $input['city'];
        }
        if (isset($input['state']) && !empty($input['state'])) {
            $state = $input['state'];
        }
        if (isset($input['zip_code']) && !empty($input['zip_code'])) {
            $zip_code = $input['zip_code'];
        }
        $user_data = array(
            'address' => $address,
            'apartment' => $apartment,
            'unit_number' => $unit_number,
            'city' => $city,
            'state' => $state,
            'zip_code' => $zip_code,
        );

        $filter_data = array_filter($user_data);

        if ($filter_data) {
            $where = array('id' => $this->id);
            $update = $this->QueryBuilder->update_data('user', $filter_data, $where);
            if ($update) {
                $this->response(['status' => 200, 'message' => 'Address Updated Successfully'], RestController::HTTP_OK);
            } else {
                $this->response(['status' => 400, 'message' => 'Details Updated Not Done'], RestController::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response(['status' => 400, 'message' => 'No Valid Data to Update'], RestController::HTTP_BAD_REQUEST);
        }
    }

    #_____________________END_____________________#



    #____________________Update Personal Details_____________________#

    public function updatePersonalDetails_post()
    {

        $input = $this->input->post();

        $age =     "";
        $weight =    "";
        $height =    "";

        if (isset($input['age']) && !empty($input['age'])) {
            $age = $input['age'];
        }
        if (isset($input['weight']) && !empty($input['weight'])) {
            $weight = $input['weight'];
        }
        if (isset($input['height']) && !empty($input['height'])) {
            $height = $input['height'];
        }
        $user_data = array(
            'age' => $age,
            'weight' => $weight,
            'height' => $height,
        );

        $filter_data = array_filter($user_data);

        if ($filter_data) {
            $where = array('id' => $this->id);
            $update = $this->QueryBuilder->update_data('user', $filter_data, $where);
            if ($update) {
                $this->response(['status' => 200, 'message' => 'Details Updated Successfully'], RestController::HTTP_OK);
            } else {
                $this->response(['status' => 400, 'message' => 'Details Updated Not Done'], RestController::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response(['status' => 400, 'message' => 'No Valid Data to Update'], RestController::HTTP_BAD_REQUEST);
        }
    }

    #_____________________END_____________________#



    #_____________________Logout_____________________#


    public function logout_get()
    {
        $data  = array(
            'session_key' => '',
            'status' => 0
        );
        $logout = $this->QueryBuilder->update('user', $data, $this->id);
        if ($logout) {
            $this->response(['status' => 200, 'message' => 'Logged Out Successfully'], RestController::HTTP_OK);
        } else {
            $this->response(['status' => 400, 'message' => 'Logged Out Not Done'], RestController::HTTP_BAD_REQUEST);
        }
    }

    #_____________________END_____________________#
}



//ID:  dbttebmy_vesta_sleep
//Password : Br(DIpxmH{]F




// public function edit_put()
//     {
//         // $data = array(
//         //     'id' => @$input['id'],
//         //     'gender' => @$input['gender'],
//         //     'age' => @$input['age'],
//         //     'height' => @$input['height'],
//         //     'weight' => @$input['weight'],
//         //     'address' => @$input['address'],
//         //     'unit_number' => @$input['unit_number'],
//         //     'city' => @$input['city'],
//         //     'state' => @$input['state'],
//         //     'zip_code' => @$input['zip_code']
//         // );

//         $input = $this->put();
//         $gender = array('gender' => @$input['gender']);
//         $yourprofile = array(
//             'age' => @$input['age'],
//             'height' => @$input['height'],
//             'weight' => @$input['weight'],
//         );
//         $address = array(
//             'address' => @$input['address'],
//             'unit_number' => @$input['unit_number'],
//             'city' => @$input['city'],
//             'state' => @$input['state'],
//             'zip_code' => @$input['zip_code']
//         );

//         if (!empty(isset($gender['gender'])) && ($gender['gender'] == 0 || $gender['gender'] == 1 || $gender['gender'] == 2)) {
//             if (checkRequired($gender)) {
//                 $result  = array('status' => 400, 'message' => checkRequired($gender) . " are requied fields.");
//                 $this->response($result, $result['status']);
//             } else {
//                 if ($gender['gender'] == 0) {
//                     $user_data = array(
//                         'gender' => 'Male',
//                         'steps_done' => 1,
//                         'updated_at' => updateDate()
//                     );
//                 } elseif ($gender['gender'] == 1) {
//                     $user_data = array(
//                         'gender' => 'Female',
//                         'steps_done' => 1,
//                         'updated_at' => updateDate()
//                     );
//                 } elseif ($gender['gender'] == 2) {
//                     $user_data = array(
//                         'gender' => 'Others',
//                         'steps_done' => 1,
//                         'updated_at' => updateDate()
//                     );
//                 }
//             }
//         } elseif ((!empty(isset($yourprofile['age']))) || (!empty(isset($yourprofile['height']))) || (!empty(isset($yourprofile['weight'])))) {
//             if (checkRequired($yourprofile)) {
//                 $result  = array('status' => 400, 'message' => checkRequired($yourprofile) . " are requied fields.");
//                 $this->response($result, $result['status']);
//             } else {

//                 $user_data = array(
//                     'age' => $input['age'],
//                     'height' => $input['height'],
//                     'weight' => $input['weight'],
//                     'steps_done' => 2,
//                     'updated_at' => updateDate()
//                 );
//             }
//         } elseif ((!empty(isset($address['address']))) || (!empty(isset($address['unit_number']))) || (!empty(isset($address['city']))) || (!empty(isset($address['state']))) || (!empty(isset($address['zip_code']))) || (!empty(isset($address['address'])))) {
//             if (checkRequired($address)) {
//                 $result  = array('status' => 400, 'message' => checkRequired($address) . " are requied fields.");
//                 $this->response($result, $result['status']);
//             } else {
//                 $user_data = array(
//                     'address' => $input['address'],
//                     'unit_number' => $input['unit_number'],
//                     'city' => $input['city'],
//                     'state' => $input['state'],
//                     'zip_code' => $input['zip_code'],
//                     'steps_done' => 3,
//                     'updated_at' => updateDate()
//                 );
//             }
//         } else {
//             $this->response(['status' => 400, 'message' => 'Updation Field Not Selected'], RestController::HTTP_BAD_REQUEST);
//         }
//         $id = @$input['id'];
//         $update = $this->QueryBuilder->update('user', $user_data, $id);
//         if ($update) {
//             $this->response(['status' => 200, 'message' => 'Data Updated Successfully'], RestController::HTTP_OK);
//         } else {
//             $this->response(['status' => 400, 'message' => 'Data Updation Not Done'], RestController::HTTP_BAD_REQUEST);
//         }
//     }

<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

use chriskacerguis\RestServer\RestController;

class ForgetPassword extends RestController
{

    private $param                   =     array();
    private $nonAuthentication_arr     =     array('forgetpassword');
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

    #_____________________Forget Password_____________________#

    public function forgetPassword_post()
    {
        $input = $this->input->post();

        if (checkRequired($input)) {
            $result  = array('status' => 400, 'message' => checkRequired($input));
            $this->response($result, $result['status']);
        } else {

            $data = array('email' => @$input['email']);

            $check = $this->QueryBuilder->select_data('user', $data);

            if ($check) {
                foreach ($check as $value) {
                    $id = $value['id'];
                }
                $newpassword = generatePassword();

                $this->load->library('phpmailer_lib');

                // PHPMailer object
                $mail = $this->phpmailer_lib->load();


                $mail->isSMTP();
                $mail->Host     = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'zargam.techwinlabs@gmail.com';
                $mail->Password = 'gkfckkdkawethkmm';
                $mail->SMTPSecure = 'ssl';
                $mail->Port     = 465;



                $mail->setFrom('zargam.techwinlabs@gmail.com');
                // $mail->addReplyTo('info@example.com', 'CodexWorld');

                // Add a recipient
                $mail->addAddress(@$input['email']);

                // Add cc or bcc 
                // $mail->addCC('cc@example.com');
                // $mail->addBCC('bcc@example.com');

                // Email subject
                $mail->Subject = 'Your New Login Password';

                // Set email format to HTML
                $mail->isHTML(true);

                // Email body content
                $mailContent = "<h1>Dear User , Your New Password  </h1>
                <p>$newpassword</p>";
                $mail->Body = $mailContent;

                if ($mail->send()) {
                    $data = array(
                        'password' => md5($newpassword),
                    );

                    $this->QueryBuilder->update('user', $data, $id);
                    $this->response(['status' => 200, 'message' => 'Password has been sent.'], RestController::HTTP_OK);
                } else {
                    $this->response(['status' => 400, 'message' => 'Password could not be sent.'], RestController::HTTP_OK);
                }
            } else {
                $this->response(['status' => 400, 'message' => 'Email Address Not Found'], RestController::HTTP_OK);
            }
        }
    }


    #_____________________END_____________________#
}

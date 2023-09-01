<?php
defined('BASEPATH') or exit('No direct script access allowed');

function checkRequired($array)
{
    $requried = "";
    foreach ($array as $key => $value) {
        if ($value == '') {
            $requried .= $key . ',';
        }
    }
    if ($requried != "") {
        return rtrim($requried, ',') . ' field is required';
    } else {
        return false;
    }
}


function sessionGenrate()
{
    $session = rand();
    $encrypt = md5($session);
    return $encrypt;
}

function createdDate()
{
    $date_utc = new \DateTime("now", new \DateTimeZone("UTC"));
    return  $date_utc->format('Y-m-d H:i:s');

    // $date = date("Y-m-d H:i:s");
    // return $date;
}


function generatePassword($length = 8)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = mb_strlen($chars);
    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }
    return $result;
}

<?php
date_default_timezone_set('America/Los_Angeles');
error_reporting(E_ALL);
ini_set('display_errors', 1);

function well_get($url, $user_token, $debug = false) {
    echo '-----------------------------------------------------------------------------------------------------------' . PHP_EOL;
    echo date('Y-m-d H:i:s') . PHP_EOL;
    echo $url . PHP_EOL;

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer " . $user_token,
            "Cache-Control: no-cache"
        ),
    ));

    $res = curl_exec($curl);
    if (!$res) {
        $error = curl_error($curl) . '(' . curl_errno($curl) . ')';
        die($error);
    }
    if (strpos($res, 'Token has expired') > 0) {
        die('token has expired');
    }
    if (strpos($res, '<!DOCTYPE html>') === 0) {
        die($res);
    }

    $result = json_decode($res, true);
    if (@$result['error']) {
        die('ERROR! ' . $res);
    }
    if ($debug) {
        echo '=>' . PHP_EOL;
        if (@$result['result']) {
            echo json_encode($result['result']) . PHP_EOL . PHP_EOL . PHP_EOL;
        } else {
            echo $res . PHP_EOL . PHP_EOL . PHP_EOL;
        }
    }

    return @$result;
}

function well_post($url, $fields, $user_token = false, $debug = false)
{
    echo '-----------------------------------------------------------------------------------------------------------' . PHP_EOL;
    echo date('Y-m-d H:i:s') . PHP_EOL;
    echo $url . PHP_EOL;

    $curl = curl_init();

    $fields_string = array();
    foreach ($fields AS $key => $val) {
        $fields_string[] = "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"$key\"\r\n\r\n$val\r\n";
    }
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $headers = array(
        "Cache-Control: no-cache",
        "Postman-Token: 24f288c8-4a9e-4720-b607-085730e60a97",
        "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
    );
    if ($user_token) {
        $headers[] = "Authorization: Bearer " . $user_token;
    }
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => implode('',$fields_string) . "------WebKitFormBoundary7MA4YWxkTrZu0gW--",
        CURLOPT_HTTPHEADER => $headers
    ));

    $res = curl_exec($curl);
    if (!$res) {
        $error = curl_error($curl) . '(' . curl_errno($curl) . ')';
        die($error);
    }
    if (strpos($res, 'Token has expired') > 0) {
        die('token has expired');
    }
    if (strpos($res, '<!DOCTYPE html>') === 0) {
        die($res);
    }

    $result = json_decode($res, true);
    if (@$result['error']) {
        die('ERROR! ' . $res);
    }
    if ($debug) {
        echo '=>' . PHP_EOL;
        if (@$result['result']) {
            echo json_encode($result['result']) . PHP_EOL . PHP_EOL . PHP_EOL;
        } else {
            echo $res . PHP_EOL . PHP_EOL . PHP_EOL;
        }
    }

    return @$result;
}

function curl($url, $fields, $debug = false)
{
    echo '-----------------------------------------------------------------------------------------------------------' . PHP_EOL;
    echo date('Y-m-d H:i:s') . PHP_EOL;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($fields));
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
    $res = curl_exec($curl);
    if (!$res) {
        $error = curl_error($curl) . '(' . curl_errno($curl) . ')';
        die($error);
    }
    $json = @json_decode($res, true);
    if (@$json['code'] || @$json['error']) {
        echo PHP_EOL . $url . PHP_EOL;
        var_dump($json);
        die('error!');
    }

    $fields_2 = $fields;
    if (isset($fields_2['id'])) unset($fields_2['id']);
    if (isset($fields_2['jsonrpc'])) unset($fields_2['jsonrpc']);
    echo json_encode($fields_2) . PHP_EOL;
    $result = json_decode($res, true);
    if (@$result['error']) {
        die('ERROR! ' . $res);
    }
    if ($debug) {
        echo '=>' . PHP_EOL;
        if (@$result['result']) {
            echo json_encode($result['result']) . PHP_EOL . PHP_EOL . PHP_EOL;
        } else {
            echo $res . PHP_EOL . PHP_EOL . PHP_EOL;
        }
    }

    return @$result['result'] ? $result['result'] : $res;
}
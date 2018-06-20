<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function curl($url, $fields, $debug = false)
{
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
    if (@$json['code'] && @$json['error']) {
        echo PHP_EOL . $url . PHP_EOL;
        var_dump($json);
        die('error!');
    }


    echo '-----------------------------------------------------------------------------------------------------------' . PHP_EOL;
    echo date('Y-m-d H:i:s') . PHP_EOL;
    $fields_2 = $fields;
    if (isset($fields_2['id'])) unset($fields_2['id']);
    if (isset($fields_2['jsonrpc'])) unset($fields_2['jsonrpc']);
    echo json_encode($fields_2) . PHP_EOL;
    echo '=>' . PHP_EOL;
    $result = json_decode($res, true);
    if (@$result['error']) {
        die('ERROR! ' . $res);
    }
    if ($debug) {
        if (@$result['result']) {
            echo json_encode($result['result']) . PHP_EOL . PHP_EOL . PHP_EOL;
        } else {
            echo $res . PHP_EOL . PHP_EOL . PHP_EOL;
        }
    }

    return @$result['result'] ? $result['result'] : $res;
}
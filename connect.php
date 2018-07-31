<?php
require_once 'libs/funcs.php';

/*
 * call on restart
 * docker exec -i -t factom-all /go/bin/factom-walletd
 * check on restart
 * docker exec -i factom-all /go/bin/factom-cli -s courtesy-node.factom.com:80 properties
 *
 * docker exec -i factom-all /go/bin/factom-cli -s courtesy-node.factom.com:80 importaddress Es49rq6MUgTrmEYNB61eWCpyX1gNLuvfM9mpL4oGDXq53bwX8Rko
 */
define('FACTOM_CHAIN_ID', '90162c4247c4ef5eab7b9161a0a5f56c6db437548453db214413c85ea44c4a53');
define('FACTOM_PUB', 'EC2dnBDuLb9gWLbSLZn8QbAKsox52vq9bCrrZUATqPMttcwVwFdw');

define('FACTOM_NODE', 'courtesy-node.factom.com:80');
define('FACTOM_WALLET_URL', 'http://10.185.89.1:8089/v2');
define('FACTOM_API_URL', 'http://courtesy-node.factom.com:80/v2'); //http://10.185.89.1:8088/v2');
define('FACTOM_API_URL_V1', 'http://courtesy-node.factom.com:80/v1'); //http://10.185.89.1:8088/v1');

define('WELL_API_URL', 'https://well-api.joinwell.com');

//check balance
$test = curl(FACTOM_API_URL,
    array(
        'jsonrpc' => '2.0', 'id' => 0, 'method' => 'entry-credit-balance', 'params' => array('address' => FACTOM_PUB)
    )
);
if (!$test['balance']) {
    $rate = curl(FACTOM_API_URL,
        array(
            'jsonrpc' => '2.0', 'id' => 0, 'method' => 'entry-credit-rate', 'params' => array()
        )
    );
    die('need to buy more credits, current rate ' . $rate); //autobuy is not secure but can be done
}

//check last transactions of the chain
//@WARNING anyone can send transactions to all chains in the Factom - so plz just check, no useful usage of them
$cli_command = 'docker exec -i -t factom-all /go/bin/factom-cli -s ' . FACTOM_NODE . ' get allentries ' . FACTOM_CHAIN_ID;
exec($cli_command, $result);
$all_entries = []; //can cache in db
foreach ($result AS $row) {
    if (!$row || strpos($row, 'patient_id') === false) continue;
    $row = str_replace('\'', '"', substr($row, 1, strlen($row) - 2));
    $json = json_decode($row, true);
    if ($json) {
        $all_entries[$json['room']] = $json;
    }
}


//login
$auth = well_post(WELL_API_URL . '/api/auth', array(
    'email' => 'provider@demo.com',
    'password' => 'password',
    'partnerid' => '4'
));
$user_token = $auth['token'];
$user_id = $auth['user']['id'];
if (!$user_token) {
    die('need to check api, auth token is empty');
}


//get waiting room
$waiting_room = well_get(WELL_API_URL . '/api/provider/getwaitingroom/1', $user_token);
if (!$waiting_room) {
    //create waiting room for tests cases (in general - not needed!)
    $add_to_waiting_room = well_post(WELL_API_URL . '/api/patient/addtowaitingroom', array(
        'patient_id' => 5,
        'provider_id' => 1
    ), $user_token);
    if (!@$add_to_waiting_room['status']) {
        die('need to check api, add to waiting room is not working');
    }
}

//get waiting room data
$waiting_room_data = well_get(WELL_API_URL . '/api/provider/waitingroom/5/1', $user_token);
if (!$waiting_room_data) {
    die('need to check api, get waiting room data is not working');
}
$file_mark = $waiting_room_data['room'];
if (isset($all_entries[$waiting_room_data['room']])) {
    die('room ' . $waiting_room_data['room'] . ' is already saved');
}
if (@file_exists($file_mark)) {
    die('room ' . $waiting_room_data['room'] . ' is already sent');
}
$json = json_encode($waiting_room_data);

$cli_command = 'echo \'' . $json . '\' | docker exec -i factom-all /go/bin/factom-cli -s ' . FACTOM_NODE . ' addentry -c ' . FACTOM_CHAIN_ID . ' ' . FACTOM_PUB;

echo '-----------------------------------------------------------------------------------------------------------' . PHP_EOL;
echo date('Y-m-d H:i:s') . PHP_EOL;
echo $cli_command . PHP_EOL . PHP_EOL;
exec($cli_command, $result);

if($result) {
    file_put_contents($file_mark, 'saved');
}
<?php
require_once 'libs/funcs.php';

define('FACTOM_CHAIN_ID', '90162c4247c4ef5eab7b9161a0a5f56c6db437548453db214413c85ea44c4a53');
define('FACTOM_PUB', 'EC2dnBDuLb9gWLbSLZn8QbAKsox52vq9bCrrZUATqPMttcwVwFdw');

define('FACTOM_NODE', 'courtesy-node.factom.com:80');
define('FACTOM_WALLET_URL', 'http://10.185.89.1:8089/v2');
define('FACTOM_API_URL', 'http://courtesy-node.factom.com:80/v2'); //http://10.185.89.1:8088/v2');
define('FACTOM_API_URL_V1', 'http://courtesy-node.factom.com:80/v1'); //http://10.185.89.1:8088/v1');

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
    $all_entries[] = $row;
}
var_dump($all_entries);

//login
$auth = curl('https://well-api.joinwell.com/api/auth', array(
    'email' => 'provider@demo.com',
    'password' => 'password',
    'partnerid' => '4'
));
//here will be parse of auth token and send request to data


exit;
$json = json_encode('any json data');
$cli_command = 'echo ' . $json . ' | docker exec -i -t factom-all /go/bin/factom-cli -s ' . FACTOM_NODE . ' addentry -c ' . FACTOM_CHAIN_ID . ' ' . FACTOM_PUB;
echo $cli_command;
exec($cli_command, $result);
var_dump($result);